<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PegawaiModel;
use App\Models\UserModel;
use App\Models\JabatanModel;
use App\Libraries\PdfGenerator;
use Config\Database;

class Pegawai extends BaseController
{
    protected $pegawaiModel;
    protected $userModel;
    protected $jabatanModel;
    protected $db;

    public function __construct()
    {
        $this->pegawaiModel = new PegawaiModel();
        $this->userModel = new UserModel();
        $this->jabatanModel = new JabatanModel();
        $this->db = Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Pegawai',
        ];

        return view('admin/pegawai/index', $data);
    }

    public function getAll()
    {
        $request = $this->request->getPost();

        // DataTables parameters
        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $search = $request['search']['value'] ?? '';
        $order = $request['order'] ?? [];

        // Get data
        $builder = $this->db->table('pegawai');
        $builder->select('pegawai.*, jabatan.namajabatan, bagian.namabagian, users.username, users.email');
        $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid');
        $builder->join('bagian', 'bagian.idbagian = jabatan.bagianid');
        $builder->join('users', 'users.id = pegawai.userid');

        // Search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('pegawai.idpegawai', $search)
                ->orLike('pegawai.namapegawai', $search)
                ->orLike('pegawai.nik', $search)
                ->orLike('jabatan.namajabatan', $search)
                ->orLike('bagian.namabagian', $search)
                ->groupEnd();
        }

        // Count filtered results
        $filteredCount = $builder->countAllResults(false);

        // Order
        if (!empty($order)) {
            $columns = ['', 'pegawai.idpegawai', 'pegawai.namapegawai', 'pegawai.nik', 'jabatan.namajabatan', 'bagian.namabagian', 'pegawai.jenkel', 'pegawai.nohp'];
            $orderColumn = $columns[$order[0]['column']] ?? 'pegawai.namapegawai';
            $orderDir = $order[0]['dir'] ?? 'asc';
            $builder->orderBy($orderColumn, $orderDir);
        } else {
            $builder->orderBy('pegawai.namapegawai', 'asc');
        }

        // Limit and offset
        $builder->limit($length, $start);

        // Get results
        $results = $builder->get()->getResultArray();

        // Count total records
        $totalCount = $this->db->table('pegawai')->countAllResults();

        // Format data for DataTables
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'idpegawai' => $row['idpegawai'],
                'namapegawai' => $row['namapegawai'],
                'nik' => $row['nik'] ?? '-',
                'namajabatan' => $row['namajabatan'],
                'namabagian' => $row['namabagian'],
                'jenkel' => $row['jenkel'],
                'nohp' => $row['nohp'] ?? '-',
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($request['draw'] ?? 1),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
            'token' => csrf_hash()
        ]);
    }

    public function getJabatan()
    {
        $jabatanModel = new \App\Models\JabatanModel();
        $jabatan = $jabatanModel->select('jabatan.*, bagian.namabagian')
            ->join('bagian', 'bagian.idbagian = jabatan.bagianid')
            ->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data' => $jabatan,
            'token' => csrf_hash()
        ]);
    }

    public function create()
    {
        // Generate ID pegawai baru
        $idpegawai = $this->pegawaiModel->generateIdPegawai();

        // Jika request AJAX atau GET, kembalikan dalam format JSON
        if ($this->request->isAJAX() || $this->request->getMethod(true) === 'GET') {
            return $this->response->setJSON([
                'status' => true,
                'idpegawai' => $idpegawai,
                'token' => csrf_hash()
            ]);
        }

        // Jika bukan AJAX, tampilkan view seperti biasa
        $data = [
            'title' => 'Tambah Pegawai',
            'jabatan' => $this->jabatanModel->select('jabatan.*, bagian.namabagian')
                ->join('bagian', 'bagian.idbagian = jabatan.bagianid')
                ->findAll(),
            'idpegawai' => $idpegawai
        ];

        return view('admin/pegawai/create', $data);
    }

    public function store()
    {
        // Log untuk debugging
        log_message('debug', 'Pegawai::store() dipanggil');
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));

        // Validasi input
        $rules = [
            'namapegawai' => 'required|max_length[255]',
            'jabatanid' => 'required|integer',
            'jenkel' => 'required|max_length[15]',
            'nik' => 'permit_empty|max_length[16]',
            'alamat' => 'permit_empty|max_length[255]',
            'nohp' => 'permit_empty|max_length[15]',
            'username' => 'required|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
        ];

        $messages = [
            'namapegawai' => [
                'required' => 'Nama pegawai harus diisi',
                'max_length' => 'Nama pegawai maksimal 255 karakter',
            ],
            'jabatanid' => [
                'required' => 'Jabatan harus dipilih',
                'integer' => 'Jabatan tidak valid',
            ],
            'jenkel' => [
                'required' => 'Jenis kelamin harus dipilih',
                'max_length' => 'Jenis kelamin maksimal 15 karakter',
            ],
            'nik' => [
                'max_length' => 'NIK maksimal 16 karakter',
            ],
            'alamat' => [
                'max_length' => 'Alamat maksimal 255 karakter',
            ],
            'nohp' => [
                'max_length' => 'No HP maksimal 15 karakter',
            ],
            'username' => [
                'required' => 'Username harus diisi',
                'is_unique' => 'Username sudah digunakan'
            ],
            'email' => [
                'required' => 'Email harus diisi',
                'valid_email' => 'Email tidak valid',
                'is_unique' => 'Email sudah digunakan'
            ],
            'password' => [
                'required' => 'Password harus diisi',
                'min_length' => 'Password minimal 6 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            log_message('debug', 'Validasi gagal: ' . json_encode($this->validator->getErrors()));
            return $this->response->setJSON([
                'status' => false,
                'errors' => $this->validator->getErrors(),
                'token' => csrf_hash()
            ]);
        }

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Pastikan ID pegawai valid
            $idpegawai = $this->request->getPost('idpegawai');
            if (empty($idpegawai)) {
                $idpegawai = $this->pegawaiModel->generateIdPegawai();
            }
            log_message('debug', 'ID Pegawai: ' . $idpegawai);

            // Insert data user
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'), // Model akan hash password
                'name' => $this->request->getPost('namapegawai'),
                'role' => 'pegawai',
                'status' => 'active',
            ];
            log_message('debug', 'User data: ' . json_encode($userData));

            // Nonaktifkan validasi model dan gunakan validasi manual
            $this->userModel->skipValidation(true);
            $userInserted = $this->userModel->insert($userData);

            if (!$userInserted) {
                log_message('error', 'Gagal menyimpan data user: ' . json_encode($this->userModel->errors()));
                throw new \Exception('Gagal menyimpan data user');
            }

            $userId = $this->userModel->getInsertID();
            log_message('debug', 'User ID: ' . $userId);

            if (!$userId) {
                log_message('error', 'User ID tidak valid');
                throw new \Exception('User ID tidak valid');
            }

            // Insert data pegawai
            $pegawaiData = [
                'idpegawai' => $idpegawai,
                'userid' => $userId,
                'jabatanid' => $this->request->getPost('jabatanid'),
                'nik' => $this->request->getPost('nik'),
                'namapegawai' => $this->request->getPost('namapegawai'),
                'jenkel' => $this->request->getPost('jenkel'),
                'alamat' => $this->request->getPost('alamat'),
                'nohp' => $this->request->getPost('nohp'),
            ];
            log_message('debug', 'Pegawai data: ' . json_encode($pegawaiData));

            // Bypass validasi model karena kita sudah memvalidasi di controller
            $this->pegawaiModel->skipValidation(true);
            $result = $this->pegawaiModel->insert($pegawaiData);
            log_message('debug', 'Hasil insert pegawai: ' . ($result ? 'berhasil' : 'gagal'));

            if (!$result) {
                log_message('error', 'Gagal menyimpan data pegawai: ' . json_encode($this->pegawaiModel->errors()));
                throw new \Exception('Gagal menyimpan data pegawai');
            }

            // Commit transaksi jika berhasil
            $this->db->transCommit();
            log_message('debug', 'Transaksi berhasil di-commit');

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data pegawai berhasil ditambahkan',
                'token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika gagal
            $this->db->transRollback();
            log_message('error', 'Error saat menyimpan data: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function edit($id)
    {
        $pegawai = $this->pegawaiModel->find($id);

        if (!$pegawai) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data pegawai tidak ditemukan',
                    'token' => csrf_hash()
                ]);
            }
            return redirect()->to('/admin/pegawai')->with('error', 'Data pegawai tidak ditemukan');
        }

        $user = $this->userModel->find($pegawai['userid']);
        $jabatan = $this->jabatanModel->select('jabatan.*, bagian.namabagian')
            ->join('bagian', 'bagian.idbagian = jabatan.bagianid')
            ->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => true,
                'pegawai' => $pegawai,
                'user' => $user,
                'jabatan' => $jabatan,
                'token' => csrf_hash()
            ]);
        }

        $data = [
            'title' => 'Edit Pegawai',
            'pegawai' => $pegawai,
            'user' => $user,
            'jabatan' => $jabatan,
        ];

        return view('admin/pegawai/edit', $data);
    }

    public function update($id)
    {
        $pegawai = $this->pegawaiModel->find($id);

        if (!$pegawai) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data pegawai tidak ditemukan',
                    'token' => csrf_hash()
                ]);
            }
            return redirect()->to('/admin/pegawai')->with('error', 'Data pegawai tidak ditemukan');
        }

        // Validasi input untuk data pegawai
        $rules = [
            'jabatanid' => 'required|integer',
            'nik' => 'permit_empty|max_length[16]',
            'namapegawai' => 'required|max_length[255]',
            'jenkel' => 'required|max_length[15]',
            'alamat' => 'permit_empty|max_length[255]',
            'nohp' => 'permit_empty|max_length[15]',
            'email' => 'required|valid_email',
        ];

        $messages = [
            'jabatanid' => [
                'required' => 'Jabatan harus dipilih',
                'integer' => 'Jabatan tidak valid',
            ],
            'nik' => [
                'max_length' => 'NIK maksimal 16 karakter',
            ],
            'namapegawai' => [
                'required' => 'Nama pegawai harus diisi',
                'max_length' => 'Nama pegawai maksimal 255 karakter',
            ],
            'jenkel' => [
                'required' => 'Jenis kelamin harus dipilih',
                'max_length' => 'Jenis kelamin maksimal 15 karakter',
            ],
            'alamat' => [
                'max_length' => 'Alamat maksimal 255 karakter',
            ],
            'nohp' => [
                'max_length' => 'No HP maksimal 15 karakter',
            ],
            'email' => [
                'required' => 'Email harus diisi',
                'valid_email' => 'Email tidak valid',
            ],
        ];

        // Validasi data user
        $user = $this->userModel->find($pegawai['userid']);
        $email = $this->request->getPost('email');

        // Cek apakah email diubah dan sudah ada di database
        if ($email != $user['email']) {
            $rules['email'] = 'required|valid_email|is_unique[users.email,id,' . $user['id'] . ']';
            $messages['email']['is_unique'] = 'Email sudah digunakan';
        }

        // Validasi password jika diisi
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $messages['password'] = [
                'min_length' => 'Password minimal 6 karakter'
            ];
        }

        if (!$this->validate($rules, $messages)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => false,
                    'errors' => $this->validator->getErrors(),
                    'token' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Update data pegawai
            $pegawaiData = [
                'jabatanid' => $this->request->getPost('jabatanid'),
                'nik' => $this->request->getPost('nik'),
                'namapegawai' => $this->request->getPost('namapegawai'),
                'jenkel' => $this->request->getPost('jenkel'),
                'alamat' => $this->request->getPost('alamat'),
                'nohp' => $this->request->getPost('nohp'),
            ];

            // Skip validasi model karena kita sudah validasi di controller
            $this->pegawaiModel->skipValidation(true);
            $pegawaiUpdated = $this->pegawaiModel->update($id, $pegawaiData);

            if (!$pegawaiUpdated) {
                throw new \Exception('Gagal memperbarui data pegawai: ' . json_encode($this->pegawaiModel->errors()));
            }

            // Update data user
            $userData = [
                'email' => $this->request->getPost('email'),
                'name' => $this->request->getPost('namapegawai'),
            ];

            // Jika password diisi, update password
            if ($this->request->getPost('password')) {
                $userData['password'] = $this->request->getPost('password');
            }

            // Skip validasi model karena kita sudah validasi di controller
            $this->userModel->skipValidation(true);
            $userUpdated = $this->userModel->update($user['id'], $userData);

            if (!$userUpdated) {
                throw new \Exception('Gagal memperbarui data user: ' . json_encode($this->userModel->errors()));
            }

            // Commit transaksi jika berhasil
            $this->db->transCommit();

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data pegawai berhasil diperbarui',
                    'token' => csrf_hash()
                ]);
            }

            return redirect()->to('/admin/pegawai')->with('success', 'Data pegawai berhasil diperbarui');
        } catch (\Exception $e) {
            // Rollback transaksi jika gagal
            $this->db->transRollback();
            log_message('error', 'Error saat update data: ' . $e->getMessage());

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'token' => csrf_hash()
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $pegawai = $this->pegawaiModel->find($id);

        if (!$pegawai) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan',
                'token' => csrf_hash()
            ]);
        }

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Hapus data pegawai
            $this->pegawaiModel->delete($id);

            // Hapus data user
            $this->userModel->delete($pegawai['userid']);

            // Commit transaksi jika berhasil
            $this->db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pegawai berhasil dihapus',
                'token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika gagal
            $this->db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function report()
    {
        // Get filter parameters
        $bagianId = $this->request->getGet('bagian');
        $jabatanId = $this->request->getGet('jabatan');
        $jenisKelamin = $this->request->getGet('jenkel');
        $isAjax = $this->request->getGet('ajax');

        // Get all bagian for filter dropdown
        $bagianModel = new \App\Models\BagianModel();
        $bagianList = $bagianModel->findAll();

        // Get all jabatan for filter dropdown
        $jabatanModel = new \App\Models\JabatanModel();
        $jabatanList = $jabatanModel->findAll();

        // Get all pegawai data with join to jabatan and bagian
        $builder = $this->db->table('pegawai');
        $builder->select('pegawai.*, jabatan.namajabatan, bagian.namabagian, jabatan.bagianid');
        $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid');
        $builder->join('bagian', 'bagian.idbagian = jabatan.bagianid');

        // Apply filters if provided
        if (!empty($bagianId)) {
            $builder->where('bagian.idbagian', $bagianId);
        }

        if (!empty($jabatanId)) {
            $builder->where('jabatan.idjabatan', $jabatanId);
        }

        if (!empty($jenisKelamin)) {
            $builder->where('pegawai.jenkel', $jenisKelamin);
        }

        $builder->orderBy('pegawai.namapegawai', 'ASC');
        $pegawai = $builder->get()->getResultArray();

        // Get filter names for display
        $bagian_name = '';
        $jabatan_name = '';

        if (!empty($bagianId)) {
            foreach ($bagianList as $bagian) {
                if ($bagian['idbagian'] == $bagianId) {
                    $bagian_name = $bagian['namabagian'];
                    break;
                }
            }
        }

        if (!empty($jabatanId)) {
            foreach ($jabatanList as $jabatan) {
                if ($jabatan['idjabatan'] == $jabatanId) {
                    $jabatan_name = $jabatan['namajabatan'];
                    break;
                }
            }
        }

        $data = [
            'title' => 'Laporan Data Pegawai',
            'pegawai' => $pegawai,
            'bagianList' => $bagianList,
            'jabatanList' => $jabatanList,
            'filters' => [
                'bagian' => $bagianId,
                'jabatan' => $jabatanId,
                'jenkel' => $jenisKelamin
            ],
            'bagian_name' => $bagian_name,
            'jabatan_name' => $jabatan_name
        ];

        // Jika request untuk cetak PDF
        if ($this->request->getGet('print') == 'true') {
            return $this->generatePDF($data);
        }

        // Jika request AJAX, kembalikan hanya bagian laporan
        if ($isAjax) {
            return view('admin/pegawai/report_partial', $data);
        }

        // Jika tidak, tampilkan dengan layout admin
        return view('admin/pegawai/report_preview', $data);
    }

    /**
     * Generate PDF report
     */
    protected function generatePDF($data)
    {
        // Tambahkan path logo
        $data['logo'] = ROOTPATH . 'public/image/logo.png';

        // Jika logo tidak ada, gunakan placeholder
        if (!file_exists($data['logo'])) {
            $data['logo'] = '';
        } else {
            // Convert logo to base64 for embedding in PDF
            $data['logo'] = 'data:image/png;base64,' . base64_encode(file_get_contents($data['logo']));
        }

        // Load PDF helper
        $pdfHelper = new \App\Helpers\PdfHelper();

        // Generate PDF
        $html = view('admin/pegawai/pdf_template', $data);

        // Filename dengan timestamp
        $filename = 'laporan_pegawai_' . date('Ymd_His') . '.pdf';

        // Generate PDF
        return $pdfHelper->generate($html, $filename, 'A4', 'portrait', [
            'attachment' => false // true untuk download, false untuk preview di browser
        ]);
    }

    // Method untuk mendapatkan jabatan berdasarkan bagian
    public function getJabatanByBagian()
    {
        $bagianId = $this->request->getGet('bagian_id');

        if (empty($bagianId)) {
            return $this->response->setJSON([
                'status' => false,
                'data' => [],
                'message' => 'ID Bagian tidak valid',
                'token' => csrf_hash()
            ]);
        }

        $jabatanModel = new \App\Models\JabatanModel();
        $jabatan = $jabatanModel->where('bagianid', $bagianId)->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data' => $jabatan,
            'token' => csrf_hash()
        ]);
    }
}
