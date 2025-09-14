<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LemburModel;
use App\Models\PegawaiModel;

class Lembur extends BaseController
{
    protected $lemburModel;
    protected $pegawaiModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->lemburModel = new LemburModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->session = session();
        $this->db = \Config\Database::connect();
    }

    /**
     * Menampilkan daftar semua lembur
     */
    public function index()
    {
        $request = $this->request;


        $bulan = $request->getGet('bulan') ?? date('m');
        $tahun = $request->getGet('tahun') ?? date('Y');
        $pegawaiId = $request->getGet('pegawai_id') ?? '';
        $search = $request->getGet('search') ?? '';


        $builder = $this->db->table('lembur');
        $builder->select('lembur.*, pegawai.namapegawai, pegawai.nik');
        $builder->join('pegawai', 'pegawai.idpegawai = lembur.pegawai_id');


        if ($bulan && $tahun) {
            $startDate = $tahun . '-' . $bulan . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
            $builder->where('tanggallembur >=', $startDate);
            $builder->where('tanggallembur <=', $endDate);
        }


        if ($pegawaiId) {
            $builder->where('pegawai_id', $pegawaiId);
        }


        if ($search) {
            $builder->groupStart()
                ->like('idlembur', $search)
                ->orLike('pegawai.namapegawai', $search)
                ->orLike('pegawai.nik', $search)
                ->groupEnd();
        }

        $builder->orderBy('lembur.tanggallembur', 'DESC');
        $lembur_list = $builder->get()->getResultArray();


        foreach ($lembur_list as &$lembur) {
            $durasi = $this->lemburModel->hitungDurasiLembur($lembur['jammulai'], $lembur['jamselesai']);
            $lembur['durasi_menit'] = $durasi;
            $lembur['durasi_format'] = floor($durasi / 60) . ' jam ' . ($durasi % 60) . ' menit';
        }

        $data = [
            'title' => 'Daftar Lembur',
            'lembur_list' => $lembur_list,
            'pegawai_list' => $this->pegawaiModel->findAll(),
            'filter' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'pegawai_id' => $pegawaiId,
                'search' => $search
            ]
        ];

        return view('admin/lembur/index', $data);
    }

    /**
     * Menampilkan form tambah data lembur
     */
    public function create()
    {

        $pegawaiList = $this->db->table('pegawai')
            ->select('pegawai.*, bagian.namabagian as nama_bagian')
            ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid')
            ->join('bagian', 'bagian.idbagian = jabatan.bagianid')
            ->get()->getResultArray();

        $data = [
            'title' => 'Tambah Data Lembur',
            'pegawai_list' => $pegawaiList,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/lembur/create', $data);
    }

    /**
     * Menyimpan data lembur baru
     */
    public function store()
    {

        $isAjax = $this->request->isAJAX();


        $rules = $this->lemburModel->getValidationRules();
        if (!$this->validate($rules)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Validasi gagal. Silakan periksa formulir Anda.',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }


        $pegawai_id = $this->request->getPost('pegawai_id');
        $this->session->setFlashdata('pegawai_nama', $this->request->getPost('pegawai_nama'));


        $idlembur = $this->lemburModel->generateIdLembur();


        $data = [
            'idlembur' => $idlembur,
            'pegawai_id' => $pegawai_id,
            'tanggallembur' => $this->request->getPost('tanggallembur'),
            'jammulai' => $this->request->getPost('jammulai'),
            'jamselesai' => $this->request->getPost('jamselesai'),
            'alasan' => $this->request->getPost('alasan')
        ];

        if ($this->lemburModel->insert($data)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data lembur berhasil ditambahkan.',
                    'data' => $data
                ]);
            }
            return redirect()->to('admin/lembur')->with('success', 'Data lembur berhasil ditambahkan.');
        } else {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Gagal menambahkan data lembur.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data lembur.');
        }
    }

    /**
     * Menampilkan detail lembur
     */
    public function show($id)
    {

        $isAjax = $this->request->getGet('ajax') == 1;


        log_message('debug', "ADMIN LEMBUR SHOW: ID={$id}, AJAX={$isAjax}");


        $lembur = $this->lemburModel->getLemburWithPegawai($id);

        if (!$lembur) {
            log_message('error', "ADMIN LEMBUR SHOW: Lembur tidak ditemukan dengan ID={$id}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data lembur tidak ditemukan.'
                ]);
            }
            return redirect()->to('admin/lembur')->with('error', 'Data lembur tidak ditemukan.');
        }


        $durasi = $this->lemburModel->hitungDurasiLembur($lembur['jammulai'], $lembur['jamselesai']);
        $lembur['durasi_menit'] = $durasi;
        $lembur['durasi_format'] = floor($durasi / 60) . ' jam ' . ($durasi % 60) . ' menit';

        log_message('debug', "ADMIN LEMBUR SHOW: Lembur ditemukan dengan ID={$id}");

        $data = [
            'title' => 'Detail Lembur',
            'lembur' => $lembur
        ];

        if ($isAjax) {
            $data['ajax'] = true;
            log_message('debug', "ADMIN LEMBUR SHOW: Rendering view AJAX untuk ID={$id}");
            return view('admin/lembur/show', $data);
        }

        log_message('debug', "ADMIN LEMBUR SHOW: Rendering view normal untuk ID={$id}");
        return view('admin/lembur/show', $data);
    }

    /**
     * Menampilkan form edit data lembur
     */
    public function edit($id)
    {
        $lembur = $this->lemburModel->getLemburWithPegawai($id);

        if (!$lembur) {
            return redirect()->to('admin/lembur')->with('error', 'Data lembur tidak ditemukan.');
        }


        $pegawaiList = $this->db->table('pegawai')
            ->select('pegawai.*, bagian.namabagian as nama_bagian')
            ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid')
            ->join('bagian', 'bagian.idbagian = jabatan.bagianid')
            ->get()->getResultArray();

        $data = [
            'title' => 'Edit Data Lembur',
            'lembur' => $lembur,
            'pegawai_list' => $pegawaiList,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/lembur/edit', $data);
    }

    /**
     * Menyimpan perubahan data lembur
     */
    public function update($id)
    {

        $isAjax = $this->request->isAJAX();


        $rules = $this->lemburModel->getValidationRules();
        if (!$this->validate($rules)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Validasi gagal. Silakan periksa formulir Anda.',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }


        $pegawai_id = $this->request->getPost('pegawai_id');
        $this->session->setFlashdata('pegawai_nama', $this->request->getPost('pegawai_nama'));


        if (is_numeric($id)) {
            $lembur = $this->lemburModel->find($id);
            log_message('debug', "ADMIN LEMBUR UPDATE: Mencari lembur dengan ID numeric {$id}");
        } else {

            $lembur = $this->lemburModel->where('idlembur', $id)->first();
            log_message('debug', "ADMIN LEMBUR UPDATE: Mencari lembur dengan ID string {$id}");
        }

        if (!$lembur) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data lembur tidak ditemukan.'
                ]);
            }
            return redirect()->to('admin/lembur')->with('error', 'Data lembur tidak ditemukan.');
        }


        $data = [
            'pegawai_id' => $pegawai_id,
            'tanggallembur' => $this->request->getPost('tanggallembur'),
            'jammulai' => $this->request->getPost('jammulai'),
            'jamselesai' => $this->request->getPost('jamselesai'),
            'alasan' => $this->request->getPost('alasan')
        ];

        if ($this->lemburModel->update($lembur['idlembur'], $data)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data lembur berhasil diperbarui.',
                    'data' => $data
                ]);
            }
            return redirect()->to('admin/lembur')->with('success', 'Data lembur berhasil diperbarui.');
        } else {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Gagal memperbarui data lembur.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data lembur.');
        }
    }

    /**
     * Menghapus data lembur
     */
    public function delete($id)
    {

        if (is_numeric($id)) {
            $lembur = $this->lemburModel->find($id);
        } else {

            $lembur = $this->lemburModel->where('idlembur', $id)->first();
        }

        if (!$lembur) {
            return redirect()->to('admin/lembur')->with('error', 'Data lembur tidak ditemukan.');
        }

        if ($this->lemburModel->delete($lembur['idlembur'])) {
            return redirect()->to('admin/lembur')->with('success', 'Data lembur berhasil dihapus.');
        } else {
            return redirect()->to('admin/lembur')->with('error', 'Gagal menghapus data lembur.');
        }
    }

    /**
     * Menampilkan laporan data lembur
     */
    public function report()
    {

        $pegawaiId = $this->request->getGet('pegawai');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');
        $isPrint = $this->request->getGet('print');
        $isAjax = $this->request->getGet('ajax');


        $filters = [
            'pegawai' => $pegawaiId,
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir
        ];


        if (!$isPrint && !$isAjax && !$pegawaiId && !$tanggalAwal && !$tanggalAkhir) {
            $data = [
                'title' => 'Laporan Data Lembur',
                'filters' => $filters,
                'pegawaiList' => $this->pegawaiModel->findAll()
            ];
            return view('admin/lembur/report_preview', $data);
        }


        $query = $this->db->table('lembur')
            ->select('lembur.*, pegawai.namapegawai, jabatan.namajabatan')
            ->join('pegawai', 'pegawai.idpegawai = lembur.pegawai_id')
            ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid');


        if ($pegawaiId) {
            $query->where('lembur.pegawai_id', $pegawaiId);
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $query->where('lembur.tanggallembur >=', $tanggalAwal);
            $query->where('lembur.tanggallembur <=', $tanggalAkhir);
        } elseif ($tanggalAwal) {
            $query->where('lembur.tanggallembur', $tanggalAwal);
        }


        $query->orderBy('lembur.tanggallembur', 'DESC');

        $lembur = $query->get()->getResultArray();


        $pegawai_name = '';
        if ($pegawaiId) {
            $pegawai = $this->pegawaiModel->find($pegawaiId);
            if ($pegawai) {
                $pegawai_name = $pegawai['namapegawai'];
            }
        }

        $data = [
            'title' => 'Laporan Data Lembur',
            'lembur' => $lembur,
            'filters' => $filters,
            'pegawai_name' => $pegawai_name
        ];


        if ($isPrint) {
            return $this->generatePdf($data);
        }


        if ($isAjax) {
            return view('admin/lembur/report_partial', $data);
        }


        $data['pegawaiList'] = $this->pegawaiModel->findAll();
        return view('admin/lembur/report_preview', $data);
    }

    /**
     * Generate PDF report
     */
    private function generatePdf($data)
    {

        $data['logo'] = ROOTPATH . 'public/image/logo.png';


        if (!file_exists($data['logo'])) {
            $data['logo'] = '';
        } else {

            $data['logo'] = 'data:image/png;base64,' . base64_encode(file_get_contents($data['logo']));
        }


        $pdfHelper = new \App\Helpers\PdfHelper();


        $html = view('admin/lembur/pdf_template', $data);


        $filename = 'Laporan_Lembur_' . date('Y-m-d_H-i-s') . '.pdf';


        return $pdfHelper->generate($html, $filename, 'A4', 'landscape', [
            'attachment' => false // true untuk download, false untuk preview di browser
        ]);
    }
}
