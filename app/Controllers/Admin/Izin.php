<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\IzinModel;
use App\Models\PegawaiModel;

class Izin extends BaseController
{
    protected $izinModel;
    protected $pegawaiModel;
    protected $session;

    public function __construct()
    {
        $this->izinModel = new IzinModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->session = session();
    }

    /**
     * Menampilkan daftar semua pengajuan izin
     */
    public function index()
    {
        $request = $this->request;


        $status = $request->getGet('status');
        $search = $request->getGet('search');


        $db = \Config\Database::connect();
        $builder = $db->table('izin');
        $builder->select('
            izin.*,
            pegawai.namapegawai,
            pegawai.nik,
            jabatan.namajabatan,
            izin.alasan
        ');
        $builder->join('pegawai', 'pegawai.idpegawai = izin.pegawai_id', 'left');
        $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left');


        if ($status === 'pending') {
            $builder->groupStart()
                ->where('statusizin IS NULL', null, false)
                ->orWhere(function ($query) {
                    $query->where('statusizin', false)
                        ->where('keterangan_admin IS NULL', null, false);
                })
                ->groupEnd();
        } elseif ($status === 'approved') {
            $builder->where('statusizin', true);
        } elseif ($status === 'rejected') {
            $builder->where('statusizin', false)
                ->where('keterangan_admin IS NOT NULL', null, false);
        }


        if (!empty($search)) {
            $builder->groupStart()
                ->like('izin.idizin', $search)
                ->orLike('pegawai.namapegawai', $search)
                ->orLike('pegawai.idpegawai', $search)
                ->orLike('pegawai.nik', $search)
                ->groupEnd();
        }

        $builder->orderBy('izin.created_at', 'DESC');
        $izin_list = $builder->get()->getResultArray();

        $data = [
            'title' => 'Daftar Pengajuan Izin',
            'izin_list' => $izin_list,
            'status' => $status,
            'search' => $search
        ];

        return view('admin/izin/index', $data);
    }

    /**
     * Menampilkan detail izin
     */
    public function show($id)
    {

        $isAjax = $this->request->getGet('ajax') == 1;


        log_message('debug', "ADMIN IZIN SHOW: ID={$id}, AJAX={$isAjax}");

        try {

            $izin = $this->izinModel->getIzinWithPegawai($id);

            if (!$izin) {
                log_message('error', "ADMIN IZIN SHOW: Izin tidak ditemukan dengan ID={$id}");
                if ($isAjax) {
                    return $this->response->setStatusCode(404)
                        ->setJSON([
                            'status' => false,
                            'message' => 'Data izin tidak ditemukan.'
                        ]);
                }
                return redirect()->to('admin/izin')->with('error', 'Data izin tidak ditemukan.');
            }

            log_message('debug', "ADMIN IZIN SHOW: Izin ditemukan dengan ID={$id}");

            $data = [
                'title' => 'Detail Pengajuan Izin',
                'izin' => $izin
            ];

            if ($isAjax) {
                $data['ajax'] = true;
                log_message('debug', "ADMIN IZIN SHOW: Rendering view AJAX untuk ID={$id}");
                return view('admin/izin/show', $data);
            }

            log_message('debug', "ADMIN IZIN SHOW: Rendering view normal untuk ID={$id}");
            return view('admin/izin/show', $data);
        } catch (\Exception $e) {
            log_message('error', "ADMIN IZIN SHOW: Error: " . $e->getMessage());
            if ($isAjax) {
                return $this->response->setStatusCode(500)
                    ->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                    ]);
            }
            return redirect()->to('admin/izin')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menyetujui pengajuan izin
     */
    public function approve($id)
    {

        $isAjax = $this->request->isAJAX();

        log_message('debug', "ADMIN IZIN APPROVE: ID={$id}, AJAX={$isAjax}");


        if (is_numeric($id)) {
            $izin = $this->izinModel->find($id);
            log_message('debug', "ADMIN IZIN APPROVE: Mencari izin dengan ID numeric {$id}");
        } else {

            $izin = $this->izinModel->where('idizin', $id)->first();
            log_message('debug', "ADMIN IZIN APPROVE: Mencari izin dengan ID string {$id}");
        }

        if (!$izin) {
            log_message('error', "ADMIN IZIN APPROVE: Izin tidak ditemukan dengan ID={$id}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data izin tidak ditemukan.'
                ]);
            }
            return redirect()->to('admin/izin')->with('error', 'Data izin tidak ditemukan.');
        }

        log_message('debug', "ADMIN IZIN APPROVE: Izin ditemukan dengan ID={$id}");

        $data = [
            'statusizin' => 1, // 1 = disetujui
            'keterangan_admin' => $this->request->getPost('keterangan') ?? 'Disetujui oleh admin'
        ];

        if ($this->izinModel->update($izin['idizin'], $data)) {
            log_message('debug', "ADMIN IZIN APPROVE: Berhasil update izin ID={$id}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Pengajuan izin berhasil disetujui.'
                ]);
            }
            return redirect()->to('admin/izin')->with('success', 'Pengajuan izin berhasil disetujui.');
        } else {
            log_message('error', "ADMIN IZIN APPROVE: Gagal update izin ID={$id}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Gagal menyetujui pengajuan izin.'
                ]);
            }
            return redirect()->back()->with('error', 'Gagal menyetujui pengajuan izin.');
        }
    }

    /**
     * Menolak pengajuan izin
     */
    public function reject($id)
    {

        $isAjax = $this->request->isAJAX();

        log_message('debug', "ADMIN IZIN REJECT: ID={$id}, AJAX={$isAjax}");


        if (is_numeric($id)) {
            $izin = $this->izinModel->find($id);
            log_message('debug', "ADMIN IZIN REJECT: Mencari izin dengan ID numeric {$id}");
        } else {

            $izin = $this->izinModel->where('idizin', $id)->first();
            log_message('debug', "ADMIN IZIN REJECT: Mencari izin dengan ID string {$id}");
        }

        if (!$izin) {
            log_message('error', "ADMIN IZIN REJECT: Izin tidak ditemukan dengan ID={$id}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data izin tidak ditemukan.'
                ]);
            }
            return redirect()->to('admin/izin')->with('error', 'Data izin tidak ditemukan.');
        }

        log_message('debug', "ADMIN IZIN REJECT: Izin ditemukan dengan ID={$id}");

        $keterangan = $this->request->getPost('keterangan');

        if (empty($keterangan)) {
            log_message('error', "ADMIN IZIN REJECT: Keterangan kosong untuk ID={$id}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Keterangan penolakan harus diisi.'
                ]);
            }
            return redirect()->back()->with('error', 'Keterangan penolakan harus diisi.');
        }

        $data = [
            'statusizin' => 2, // 2 = ditolak
            'keterangan_admin' => $keterangan
        ];

        if ($this->izinModel->update($izin['idizin'], $data)) {
            log_message('debug', "ADMIN IZIN REJECT: Berhasil update izin ID={$id}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Pengajuan izin berhasil ditolak.'
                ]);
            }
            return redirect()->to('admin/izin')->with('success', 'Pengajuan izin berhasil ditolak.');
        } else {
            log_message('error', "ADMIN IZIN REJECT: Gagal update izin ID={$id}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Gagal menolak pengajuan izin.'
                ]);
            }
            return redirect()->back()->with('error', 'Gagal menolak pengajuan izin.');
        }
    }

    /**
     * Menampilkan laporan izin
     */
    public function report()
    {
        $request = $this->request;


        $startDate = $request->getGet('start_date') ?? date('Y-m-01'); // Default: awal bulan ini
        $endDate = $request->getGet('end_date') ?? date('Y-m-d'); // Default: hari ini
        $status = $request->getGet('status') ?? ''; // Default: semua status
        $pegawaiId = $request->getGet('pegawai_id') ?? ''; // Default: semua pegawai


        $pegawai_list = $this->pegawaiModel->orderBy('namapegawai', 'ASC')->findAll();

        $data = [
            'title' => 'Laporan Pengajuan Izin',
            'pegawai_list' => $pegawai_list,
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'pegawai_id' => $pegawaiId
            ]
        ];

        return view('admin/izin/report_preview', $data);
    }

    /**
     * Menampilkan laporan izin (partial untuk AJAX)
     */
    public function report_partial()
    {
        $request = $this->request;


        $tanggalAwal = $request->getGet('start_date') ?? date('Y-m-01'); // Default: awal bulan ini
        $tanggalAkhir = $request->getGet('end_date') ?? date('Y-m-d'); // Default: hari ini
        $status = $request->getGet('status') ?? ''; // Default: semua status
        $pegawaiId = $request->getGet('pegawai_id') ?? ''; // Default: semua pegawai


        if (!empty($tanggalAwal)) {

            $date = \DateTime::createFromFormat('Y-m-d', $tanggalAwal);
            if (!$date || $date->format('Y-m-d') !== $tanggalAwal) {

                $timestamp = strtotime($tanggalAwal);
                if ($timestamp !== false) {
                    $tanggalAwal = date('Y-m-d', $timestamp);
                }
            }
        }

        if (!empty($tanggalAkhir)) {

            $date = \DateTime::createFromFormat('Y-m-d', $tanggalAkhir);
            if (!$date || $date->format('Y-m-d') !== $tanggalAkhir) {

                $timestamp = strtotime($tanggalAkhir);
                if ($timestamp !== false) {
                    $tanggalAkhir = date('Y-m-d', $timestamp);
                }
            }
        }


        $db = \Config\Database::connect();
        $builder = $db->table('izin');
        $builder->select('izin.*, pegawai.namapegawai, pegawai.nik, jabatan.namajabatan, izin.alasan');
        $builder->join('pegawai', 'pegawai.idpegawai = izin.pegawai_id', 'left');
        $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left');


        if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {

            $builder->groupStart()
                ->where('izin.created_at <=', $tanggalAkhir) // izin dimulai sebelum periode filter berakhir
                ->where('izin.created_at >=', $tanggalAwal) // izin berakhir setelah periode filter dimulai
                ->groupEnd();
        } elseif (!empty($tanggalAwal)) {
            $builder->where('izin.created_at >=', $tanggalAwal);
        } elseif (!empty($tanggalAkhir)) {
            $builder->where('izin.created_at <=', $tanggalAkhir);
        }


        if ($status !== '') {
            $builder->where('statusizin', $status);
        }


        if ($pegawaiId !== '') {
            $builder->where('pegawai_id', $pegawaiId);
        }


        $builder->orderBy('izin.created_at', 'DESC');

        $izinList = $builder->get()->getResultArray();


        foreach ($izinList as $key => $item) {
            if (empty($item['namapegawai'])) {

                $pegawai = $db->table('pegawai')
                    ->where('idpegawai', $item['pegawai_id'])
                    ->get()
                    ->getRowArray();

                if ($pegawai) {
                    $izinList[$key]['namapegawai'] = $pegawai['namapegawai'];
                }
            }


            if (empty($item['namajabatan']) && !empty($item['pegawai_id'])) {

                $jabatan = $db->table('pegawai')
                    ->select('jabatan.namajabatan')
                    ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left')
                    ->where('pegawai.idpegawai', $item['pegawai_id'])
                    ->get()
                    ->getRowArray();

                if ($jabatan && !empty($jabatan['namajabatan'])) {
                    $izinList[$key]['namajabatan'] = $jabatan['namajabatan'];
                }
            }
        }


        $total_izin = count($izinList);

        $data = [
            'izin' => $izinList,
            'filters' => [
                'start_date' => $tanggalAwal,
                'end_date' => $tanggalAkhir,
                'status' => $status,
                'pegawai_id' => $pegawaiId
            ],
            'total_izin' => $total_izin
        ];

        return view('admin/izin/report_partial', $data);
    }

    /**
     * Menghasilkan laporan izin dalam format PDF
     */
    public function generatePdf()
    {
        $request = $this->request;


        $startDate = $request->getGet('start_date') ?? date('Y-m-01'); // Default: awal bulan ini
        $endDate = $request->getGet('end_date') ?? date('Y-m-d'); // Default: hari ini
        $status = $request->getGet('status') ?? ''; // Default: semua status
        $pegawaiId = $request->getGet('pegawai_id') ?? ''; // Default: semua pegawai


        $db = \Config\Database::connect();
        $builder = $db->table('izin');
        $builder->select('
            izin.*,
            pegawai.namapegawai,
            pegawai.nik,
            jabatan.namajabatan,
            izin.alasan
        ');
        $builder->join('pegawai', 'pegawai.idpegawai = izin.pegawai_id', 'left');
        $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left');


        if (!empty($startDate) && !empty($endDate)) {

            $builder->groupStart()
                ->where('izin.created_at <=', $endDate) // izin dimulai sebelum periode filter berakhir
                ->where('izin.created_at >=', $startDate) // izin berakhir setelah periode filter dimulai
                ->groupEnd();
        } elseif (!empty($startDate)) {
            $builder->where('izin.created_at >=', $startDate);
        } elseif (!empty($endDate)) {
            $builder->where('izin.created_at <=', $endDate);
        }


        if ($status !== '') {
            $builder->where('statusizin', $status);
        }


        if ($pegawaiId !== '') {
            $builder->where('pegawai_id', $pegawaiId);
        }

        $builder->orderBy('izin.created_at', 'DESC');
        $izinList = $builder->get()->getResultArray();


        foreach ($izinList as $key => $item) {
            if (empty($item['namapegawai'])) {

                $pegawai = $db->table('pegawai')
                    ->where('idpegawai', $item['pegawai_id'])
                    ->get()
                    ->getRowArray();

                if ($pegawai) {
                    $izinList[$key]['namapegawai'] = $pegawai['namapegawai'];
                }
            }


            if (empty($item['namajabatan']) && !empty($item['pegawai_id'])) {

                $jabatan = $db->table('pegawai')
                    ->select('jabatan.namajabatan')
                    ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left')
                    ->where('pegawai.idpegawai', $item['pegawai_id'])
                    ->get()
                    ->getRowArray();

                if ($jabatan && !empty($jabatan['namajabatan'])) {
                    $izinList[$key]['namajabatan'] = $jabatan['namajabatan'];
                }
            }
        }


        $logoPath = ROOTPATH . 'public/image/logo.png';


        if (file_exists($logoPath)) {
            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        } else {
            $logoBase64 = '';
        }


        $periodeText = '';
        if (!empty($startDate) && !empty($endDate)) {
            $periodeText = ' Periode ' . date('d-m-Y', strtotime($startDate)) . ' s/d ' . date('d-m-Y', strtotime($endDate));
        }

        $data = [
            'title' => 'Laporan Pengajuan Izin' . $periodeText,
            'izin' => $izinList,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'pegawai_id' => $pegawaiId
            ],
            'logo' => $logoBase64
        ];


        $pdfHelper = new \App\Helpers\PdfHelper();


        $html = view('admin/izin/pdf_template', $data);


        $filename = 'laporan_izin_' . date('Ymd_His') . '.pdf';


        return $pdfHelper->generate($html, $filename, 'A4', 'landscape', [
            'attachment' => false // true untuk download, false untuk preview di browser
        ]);
    }

    /**
     * Debug: Tampilkan data yang ada di tabel izin untuk memeriksa masalah
     */
    public function debug()
    {
        $db = \Config\Database::connect();


        $totalIzin = $db->table('izin')->countAllResults();


        $izinData = $db->table('izin')->get()->getResultArray();


        $tableInfo = $db->getFieldData('izin');
        $columns = [];
        foreach ($tableInfo as $field) {
            $columns[] = [
                'name' => $field->name,
                'type' => $field->type,
                'max_length' => $field->max_length ?? 'NULL',
                'primary_key' => $field->primary_key ? 'YES' : 'NO'
            ];
        }

        $data = [
            'total_records' => $totalIzin,
            'columns' => $columns,
            'data' => array_slice($izinData, 0, 10) // Ambil 10 data pertama saja
        ];


        return $this->response->setJSON($data);
    }

    /**
     * Menampilkan semua data izin tanpa filter (untuk debugging)
     */
    public function debug_all_data()
    {
        $db = \Config\Database::connect();
        $izinList = $db->table('izin')->get()->getResultArray();

        $data = [
            'title' => 'Semua Data Izin',
            'izin' => $izinList,
            'count' => count($izinList)
        ];


        return $this->response->setJSON($data);
    }

    /**
     * Menambahkan data izin contoh (untuk debugging)
     */
    public function add_sample_data()
    {

        $db = \Config\Database::connect();
        $pegawaiIds = $db->table('pegawai')->select('idpegawai')->get()->getResultArray();


        if (empty($pegawaiIds)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tidak ada data pegawai yang tersedia.'
            ]);
        }


        date_default_timezone_set('Asia/Jakarta');


        $data = [
            [
                'idizin' => 'IZN' . date('Ymd') . '001',
                'pegawai_id' => $pegawaiIds[0]['idpegawai'],
                'tanggalmulaiizin' => date('Y-m-d', strtotime('+1 days')),
                'tanggalselesaiizin' => date('Y-m-d', strtotime('+2 days')),
                'selected_dates' => json_encode([
                    date('Y-m-d', strtotime('+1 days')),
                    date('Y-m-d', strtotime('+2 days'))
                ]),
                'jenisizin' => 'Cuti',
                'alasan' => 'Keperluan keluarga',
                'lampiran' => null,
                'statusizin' => 0, // 0 = menunggu
                'keterangan_admin' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];


        try {
            $this->izinModel->insertBatch($data);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data izin contoh berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menampilkan struktur tabel dan data izin (untuk debugging)
     */
    public function debug_table()
    {
        $db = \Config\Database::connect();


        $total = $db->table('izin')->countAllResults();


        $fields = $db->getFieldData('izin');
        $structure = [];
        foreach ($fields as $field) {
            $structure[] = [
                'name' => $field->name,
                'type' => $field->type,
                'max_length' => $field->max_length ?? 'NULL',
                'primary_key' => $field->primary_key ? 'YES' : 'NO',
                'nullable' => isset($field->nullable) && $field->nullable ? 'YES' : 'NO',
                'default' => $field->default ?? 'NULL',
            ];
        }


        $data = [];
        if ($total > 0) {
            $data = $db->table('izin')
                ->select('izin.*, pegawai.namapegawai, jabatan.namajabatan')
                ->join('pegawai', 'pegawai.idpegawai = izin.pegawai_id', 'left')
                ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left')
                ->limit(5)
                ->get()
                ->getResultArray();
        }


        $pegawai = $db->table('pegawai')->countAllResults();
        $jabatan = $db->table('jabatan')->countAllResults();


        $lastQuery = $db->getLastQuery();

        $result = [
            'total_izin' => $total,
            'table_structure' => $structure,
            'data_sample' => $data,
            'related_data' => [
                'pegawai' => $pegawai,
                'jabatan' => $jabatan
            ],
            'last_query' => (string) $lastQuery,
            'database_details' => [
                'hostname' => $db->hostname,
                'database' => $db->database,
                'driver' => $db->DBDriver
            ]
        ];

        return $this->response->setJSON($result);
    }
}
