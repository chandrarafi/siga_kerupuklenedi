<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GajiModel;
use App\Models\PegawaiModel;
use App\Models\AbsensiModel;
use App\Models\LemburModel;
use App\Models\JabatanModel;
use App\Models\OfficeSettingModel;

class Gaji extends BaseController
{
    protected $gajiModel;
    protected $pegawaiModel;
    protected $absensiModel;
    protected $lemburModel;
    protected $jabatanModel;
    protected $officeSettingModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->gajiModel = new GajiModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->absensiModel = new AbsensiModel();
        $this->lemburModel = new LemburModel();
        $this->jabatanModel = new JabatanModel();
        $this->officeSettingModel = new OfficeSettingModel();
        $this->session = session();
        $this->db = \Config\Database::connect();
    }

    /**
     * Menampilkan daftar semua gaji
     */
    public function index()
    {
        $request = $this->request;


        $bulan = $request->getGet('bulan') ?? date('m');
        $tahun = $request->getGet('tahun') ?? date('Y');
        $pegawaiId = $request->getGet('pegawai_id') ?? '';
        $status = $request->getGet('status') ?? '';
        $search = $request->getGet('search') ?? '';


        $builder = $this->db->table('gaji');
        $builder->select('gaji.*, pegawai.namapegawai, pegawai.nik');
        $builder->join('pegawai', 'pegawai.idpegawai = gaji.pegawai_id');


        if ($bulan && $tahun) {
            $periode = $bulan . '-' . $tahun;
            $builder->where('gaji.periode', $periode);
        }


        if ($pegawaiId) {
            $builder->where('pegawai_id', $pegawaiId);
        }


        if ($status) {
            $builder->where('gaji.status', $status);
        }


        if ($search) {
            $builder->groupStart()
                ->like('gaji.idgaji', $search)
                ->orLike('gaji.noslip', $search)
                ->orLike('pegawai.namapegawai', $search)
                ->orLike('pegawai.nik', $search)
                ->groupEnd();
        }

        $builder->orderBy('gaji.tanggal', 'DESC');
        $gaji_list = $builder->get()->getResultArray();

        $data = [
            'title' => 'Daftar Gaji',
            'gaji_list' => $gaji_list,
            'pegawai_list' => $this->pegawaiModel->findAll(),
            'filter' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'pegawai_id' => $pegawaiId,
                'status' => $status,
                'search' => $search
            ]
        ];

        return view('admin/gaji/index', $data);
    }

    /**
     * Menampilkan form tambah data gaji
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Data Gaji',
            'validation' => \Config\Services::validation(),
            'bulan_sekarang' => date('m'),
            'tahun_sekarang' => date('Y')
        ];

        return view('admin/gaji/create', $data);
    }

    /**
     * Menghitung gaji pegawai berdasarkan periode
     */
    public function hitungGaji()
    {

        $this->response->setHeader('Cache-Control', 'no-store, max-age=0, no-cache, must-revalidate');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Content-Type', 'application/json');

        try {
            $pegawaiId = $this->request->getPost('pegawai_id');
            $bulan = $this->request->getPost('bulan');
            $tahun = $this->request->getPost('tahun');

            if (!$pegawaiId || !$bulan || !$tahun) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data tidak lengkap'
                ])->setStatusCode(400);
            }

            $periode = $bulan . '-' . $tahun;


            $existingGaji = $this->gajiModel->where('pegawai_id', $pegawaiId)
                ->where('periode', $periode)
                ->first();

            if ($existingGaji) {

                $pegawai = $this->db->table('pegawai')
                    ->select('namapegawai')
                    ->where('idpegawai', $pegawaiId)
                    ->get()
                    ->getRowArray();

                $namaPegawai = $pegawai ? $pegawai['namapegawai'] : 'Pegawai';


                $bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $bulanText = $bulanNames[intval($bulan) - 1];

                return $this->response->setJSON([
                    'status' => false,
                    'message' => $namaPegawai . ' sudah menerima gaji untuk periode ' . $bulanText . ' ' . $tahun . '. Silakan pilih pegawai atau periode lain.'
                ])->setStatusCode(409); // Conflict
            }


            $hasilHitung = $this->gajiModel->hitungGaji($pegawaiId, $periode);

            if (!$hasilHitung['status']) {
                return $this->response->setJSON($hasilHitung)->setStatusCode(400);
            }

            return $this->response->setJSON($hasilHitung)->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'Error pada hitungGaji: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Menyimpan data gaji baru
     */
    public function store()
    {

        $isAjax = $this->request->isAJAX();


        $rules = [
            'pegawai_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'tanggal' => 'required|valid_date',
            'totalabsen' => 'required|numeric',
            'totallembur' => 'required|numeric',
            'gajibersih' => 'required|numeric',
            'metodepembayaran' => 'required',
            'status' => 'required'
        ];

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


        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $periode = $bulan . '-' . $tahun;


        $idgaji = $this->gajiModel->generateIdGaji();
        $noslip = $this->gajiModel->generateNoSlip($periode);


        $data = [
            'idgaji' => $idgaji,
            'noslip' => $noslip,
            'pegawai_id' => $this->request->getPost('pegawai_id'),
            'periode' => $periode,
            'tanggal' => $this->request->getPost('tanggal'),
            'totalabsen' => $this->request->getPost('totalabsen'),
            'totallembur' => $this->request->getPost('totallembur'),
            'potongan' => 0,
            'gajibersih' => $this->request->getPost('gajibersih'),
            'metodepembayaran' => $this->request->getPost('metodepembayaran'),
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        if ($this->gajiModel->insert($data)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data gaji berhasil ditambahkan.',
                    'data' => $data
                ]);
            }
            return redirect()->to('admin/gaji')->with('success', 'Data gaji berhasil ditambahkan.');
        } else {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Gagal menambahkan data gaji.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data gaji.');
        }
    }

    /**
     * Menampilkan detail gaji
     */
    public function show($id)
    {

        $isAjax = $this->request->getGet('ajax') == 1;


        $gaji = $this->gajiModel->getGajiWithPegawai($id);

        if (!$gaji) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data gaji tidak ditemukan.'
                ]);
            }
            return redirect()->to('admin/gaji')->with('error', 'Data gaji tidak ditemukan.');
        }


        $periode = $gaji['periode'];
        list($bulan, $tahun) = explode('-', $periode);


        $dataPegawai = $this->db->table('pegawai')
            ->select('pegawai.*, jabatan.namajabatan as nama_jabatan, jabatan.gajipokok, jabatan.tunjangan, bagian.namabagian')
            ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid')
            ->join('bagian', 'bagian.idbagian = jabatan.bagianid')
            ->where('pegawai.idpegawai', $gaji['pegawai_id'])
            ->get()
            ->getRowArray();


        $detailAbsensi = $this->db->table('absensi')
            ->where('idpegawai', $gaji['pegawai_id'])
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();


        $rekapAbsensi = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'cuti' => 0,
            'alpha' => 0,
            'total_terlambat' => 0
        ];

        foreach ($detailAbsensi as $absen) {
            if ($absen['status'] == 'hadir') {
                $rekapAbsensi['hadir']++;
                $rekapAbsensi['total_terlambat'] += $absen['terlambat'] ?? 0;
            } elseif ($absen['status'] == 'izin') {
                $rekapAbsensi['izin']++;
            } elseif ($absen['status'] == 'sakit') {
                $rekapAbsensi['sakit']++;
            } elseif ($absen['status'] == 'cuti') {
                $rekapAbsensi['cuti']++;
            } elseif ($absen['status'] == 'alpha') {
                $rekapAbsensi['alpha']++;
            }
        }


        $detailLembur = $this->db->table('lembur')
            ->where('pegawai_id', $gaji['pegawai_id'])
            ->where('MONTH(tanggallembur)', $bulan)
            ->where('YEAR(tanggallembur)', $tahun)
            ->orderBy('tanggallembur', 'ASC')
            ->get()
            ->getResultArray();


        $totalMenitLembur = 0;
        foreach ($detailLembur as &$lembur) {
            $jammulai = strtotime($lembur['jammulai']);
            $jamselesai = strtotime($lembur['jamselesai']);


            if ($jamselesai < $jammulai) {
                $jamselesai += 86400; // Tambah 24 jam
            }

            $durasiMenit = round(abs($jamselesai - $jammulai) / 60);
            $durasiJam = $durasiMenit / 60;

            $lembur['durasi_menit'] = $durasiMenit;
            $lembur['durasi_jam'] = $durasiJam;
            $lembur['upah_lembur'] = $durasiJam * 20000; // 20.000 per jam

            $totalMenitLembur += $durasiMenit;
        }

        $totalJamLembur = $totalMenitLembur / 60;


        $gajiPokok = $dataPegawai['gajipokok'] ?? 0;


        $hariKerjaNormal = 30;
        $tunjanganPenuh = $dataPegawai['tunjangan'] ?? 0;
        $tunjanganPerHari = $tunjanganPenuh / $hariKerjaNormal;
        $tunjangan = $tunjanganPerHari * $gaji['totalabsen'];


        $tarifLembur = 20000;
        $upahLembur = $gaji['totallembur'] * $tarifLembur;


        $gajiBersih = $gajiPokok + $tunjangan + $upahLembur;

        $komponenGaji = [
            'gaji_pokok' => $gajiPokok,
            'tunjangan' => $tunjangan,
            'upah_lembur' => $upahLembur,
            'gaji_bersih' => $gaji['gajibersih']
        ];

        $detailGaji = [
            'total_absensi' => $gaji['totalabsen'],
            'total_lembur' => $gaji['totallembur'],
            'tunjangan_penuh' => $tunjanganPenuh,
            'tunjangan_per_hari' => $tunjanganPerHari,
            'tarif_lembur' => $tarifLembur
        ];

        $data = [
            'title' => 'Detail Gaji',
            'gaji' => $gaji,
            'pegawai' => $dataPegawai,
            'komponen_gaji' => $komponenGaji,
            'detail' => $detailGaji,
            'detail_lembur' => $detailLembur,
            'total_jam_lembur' => $totalJamLembur,
            'total_upah_lembur' => $upahLembur,
            'detail_absensi' => $detailAbsensi,
            'rekap_absensi' => $rekapAbsensi
        ];

        if ($isAjax) {
            $data['ajax'] = true;
            return view('admin/gaji/show', $data);
        }

        return view('admin/gaji/show', $data);
    }

    /**
     * Menampilkan form edit data gaji
     */
    public function edit($id)
    {
        $gaji = $this->gajiModel->getGajiWithPegawai($id);

        if (!$gaji) {
            return redirect()->to('admin/gaji')->with('error', 'Data gaji tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Data Gaji',
            'gaji' => $gaji,
            'pegawai_list' => $this->pegawaiModel->findAll(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/gaji/edit', $data);
    }

    /**
     * Menyimpan perubahan data gaji
     */
    public function update($id)
    {

        $isAjax = $this->request->isAJAX();


        $rules = [
            'metodepembayaran' => 'required',
            'status' => 'required'
        ];

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


        if (is_numeric($id)) {
            $gaji = $this->gajiModel->find($id);
        } else {

            $gaji = $this->gajiModel->where('idgaji', $id)->first();
        }

        if (!$gaji) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data gaji tidak ditemukan.'
                ]);
            }
            return redirect()->to('admin/gaji')->with('error', 'Data gaji tidak ditemukan.');
        }


        $data = [
            'metodepembayaran' => $this->request->getPost('metodepembayaran'),
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        if ($this->gajiModel->update($gaji['idgaji'], $data)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data gaji berhasil diperbarui.',
                    'data' => $data
                ]);
            }
            return redirect()->to('admin/gaji')->with('success', 'Data gaji berhasil diperbarui.');
        } else {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Gagal memperbarui data gaji.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data gaji.');
        }
    }

    /**
     * Menghapus data gaji
     */
    public function delete($id)
    {

        $isPost = $this->request->getMethod() === 'post';


        if (is_numeric($id)) {
            $gaji = $this->gajiModel->find($id);
        } else {

            $gaji = $this->gajiModel->where('idgaji', $id)->first();
        }

        if (!$gaji) {
            return redirect()->to('admin/gaji')->with('error', 'Data gaji tidak ditemukan.');
        }

        if ($this->gajiModel->delete($gaji['idgaji'])) {
            return redirect()->to('admin/gaji')->with('success', 'Data gaji berhasil dihapus.');
        } else {
            return redirect()->to('admin/gaji')->with('error', 'Gagal menghapus data gaji.');
        }
    }

    /**
     * Menampilkan laporan gaji
     */
    public function report()
    {
        $request = $this->request;


        $bulan = $request->getGet('bulan') ?? date('m');
        $tahun = $request->getGet('tahun') ?? date('Y');
        $status = $request->getGet('status') ?? '';

        $periode = $bulan . '-' . $tahun;


        $builder = $this->db->table('gaji');
        $builder->select('gaji.*, pegawai.namapegawai, pegawai.nik, jabatan.namajabatan');
        $builder->join('pegawai', 'pegawai.idpegawai = gaji.pegawai_id');
        $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left');
        $builder->where('gaji.periode', $periode);


        if ($status) {
            $builder->where('gaji.status', $status);
        }

        $builder->orderBy('pegawai.namapegawai', 'ASC');
        $gaji_list = $builder->get()->getResultArray();


        $pegawai_list = $this->pegawaiModel->orderBy('namapegawai', 'ASC')->findAll();

        $data = [
            'title' => 'Laporan Gaji',
            'gaji_list' => $gaji_list,
            'pegawai_list' => $pegawai_list,
            'filter' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'status' => $status,
                'periode' => $periode
            ]
        ];

        return view('admin/gaji/report_preview', $data);
    }

    /**
     * Menampilkan laporan gaji (partial untuk AJAX)
     */
    public function report_partial()
    {
        $request = $this->request;


        $bulan = $request->getGet('bulan') ?? date('m');
        $tahun = $request->getGet('tahun') ?? date('Y');
        $status = $request->getGet('status') ?? '';

        $periode = $bulan . '-' . $tahun;


        $builder = $this->db->table('gaji');
        $builder->select('gaji.*, pegawai.namapegawai, pegawai.nik, jabatan.namajabatan');
        $builder->join('pegawai', 'pegawai.idpegawai = gaji.pegawai_id');
        $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left');
        $builder->where('gaji.periode', $periode);


        if ($status) {
            $builder->where('gaji.status', $status);
        }

        $builder->orderBy('pegawai.namapegawai', 'ASC');
        $gaji_list = $builder->get()->getResultArray();


        $total_gaji = 0;
        foreach ($gaji_list as $gaji) {
            $total_gaji += $gaji['gajibersih'];
        }

        $data = [
            'gaji_list' => $gaji_list,
            'filter' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'status' => $status,
                'periode' => $periode
            ],
            'total_gaji' => $total_gaji
        ];

        return view('admin/gaji/report_partial', $data);
    }

    /**
     * Menghasilkan laporan gaji dalam format PDF
     */
    public function generatePdf()
    {
        $request = $this->request;


        $bulan = $request->getGet('bulan') ?? date('m');
        $tahun = $request->getGet('tahun') ?? date('Y');
        $status = $request->getGet('status') ?? '';

        $periode = $bulan . '-' . $tahun;


        $builder = $this->db->table('gaji');
        $builder->select('gaji.*, pegawai.namapegawai, pegawai.nik, jabatan.namajabatan');
        $builder->join('pegawai', 'pegawai.idpegawai = gaji.pegawai_id');
        $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid', 'left');
        $builder->where('gaji.periode', $periode);


        if ($status) {
            $builder->where('gaji.status', $status);
        }

        $builder->orderBy('pegawai.namapegawai', 'ASC');
        $gaji_list = $builder->get()->getResultArray();


        $logoPath = ROOTPATH . 'public/image/logo.png';


        if (file_exists($logoPath)) {
            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        } else {
            $logoBase64 = '';
        }


        $bulan_list = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        $bulan_nama = $bulan_list[$bulan] ?? $bulan;

        $data = [
            'title' => 'Laporan Gaji ',
            'gaji_list' => $gaji_list,
            'filters' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'status' => $status,
                'periode' => $periode
            ],
            'logo' => $logoBase64
        ];


        $pdfHelper = new \App\Helpers\PdfHelper();


        $html = view('admin/gaji/pdf_template', $data);


        $filename = 'laporan_gaji_' . $periode . '_' . date('Ymd_His') . '.pdf';


        return $pdfHelper->generate($html, $filename, 'A4', 'landscape', [
            'attachment' => false // true untuk download, false untuk preview di browser
        ]);
    }

    /**
     * Menghasilkan slip gaji dalam format PDF
     */
    public function slip($id)
    {

        $gaji = $this->gajiModel->getGajiWithPegawai($id);

        if (!$gaji) {
            return redirect()->to('admin/gaji')->with('error', 'Data gaji tidak ditemukan.');
        }


        $periode = $gaji['periode'];
        list($bulan, $tahun) = explode('-', $periode);


        $dataPegawai = $this->db->table('pegawai')
            ->select('pegawai.*, jabatan.namajabatan as nama_jabatan, jabatan.gajipokok, jabatan.tunjangan, bagian.namabagian')
            ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid')
            ->join('bagian', 'bagian.idbagian = jabatan.bagianid')
            ->where('pegawai.idpegawai', $gaji['pegawai_id'])
            ->get()
            ->getRowArray();


        $detailLembur = $this->db->table('lembur')
            ->where('pegawai_id', $gaji['pegawai_id'])
            ->where('MONTH(tanggallembur)', $bulan)
            ->where('YEAR(tanggallembur)', $tahun)
            ->orderBy('tanggallembur', 'ASC')
            ->get()
            ->getResultArray();


        $totalMenitLembur = 0;
        foreach ($detailLembur as &$lembur) {
            $jammulai = strtotime($lembur['jammulai']);
            $jamselesai = strtotime($lembur['jamselesai']);


            if ($jamselesai < $jammulai) {
                $jamselesai += 86400; // Tambah 24 jam
            }

            $durasiMenit = round(abs($jamselesai - $jammulai) / 60);
            $durasiJam = $durasiMenit / 60;

            $lembur['durasi_menit'] = $durasiMenit;
            $lembur['durasi_jam'] = $durasiJam;
            $lembur['upah_lembur'] = $durasiJam * 20000; // 20.000 per jam

            $totalMenitLembur += $durasiMenit;
        }

        $totalJamLembur = $totalMenitLembur / 60;


        $gajiPokok = $dataPegawai['gajipokok'] ?? 0;


        $hariKerjaNormal = 30;
        $tunjanganPenuh = $dataPegawai['tunjangan'] ?? 0;
        $tunjanganPerHari = $tunjanganPenuh / $hariKerjaNormal;
        $tunjangan = $tunjanganPerHari * $gaji['totalabsen'];


        $tarifLembur = 20000;
        $upahLembur = $gaji['totallembur'] * $tarifLembur;


        $gajiBruto = $gajiPokok + $tunjangan + $upahLembur;

        $komponenGaji = [
            'gaji_pokok' => $gajiPokok,
            'tunjangan' => $tunjangan,
            'upah_lembur' => $upahLembur,
            'gaji_bruto' => $gajiBruto,
            'potongan' => 0,
            'gaji_bersih' => $gaji['gajibersih']
        ];

        $detailGaji = [
            'total_absensi' => $gaji['totalabsen'],
            'total_lembur' => $gaji['totallembur'],
            'tunjangan_penuh' => $tunjanganPenuh,
            'tunjangan_per_hari' => $tunjanganPerHari,
            'tarif_lembur' => $tarifLembur
        ];


        $logoPath = ROOTPATH . 'public/image/logo.png';


        if (file_exists($logoPath)) {
            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        } else {
            $logoBase64 = '';
        }

        $data = [
            'title' => 'Slip Gaji - ' . $dataPegawai['namapegawai'],
            'gaji' => $gaji,
            'pegawai' => $dataPegawai,
            'komponen_gaji' => $komponenGaji,
            'detail' => $detailGaji,
            'setting' => $officeSetting ?? [],
            'logo' => $logoBase64,
            'detail_lembur' => $detailLembur,
            'total_jam_lembur' => $totalJamLembur,
            'total_upah_lembur' => $upahLembur
        ];


        $pdfHelper = new \App\Helpers\PdfHelper();


        $html = view('admin/gaji/slip_template', $data);


        $filename = 'slip_gaji_' . $gaji['noslip'] . '_' . date('Ymd_His') . '.pdf';


        return $pdfHelper->generate($html, $filename, 'A4', 'portrait', [
            'attachment' => false // true untuk download, false untuk preview di browser
        ]);
    }

    /**
     * Memproses pembayaran gaji
     */
    public function processPayment($id)
    {

        if (is_numeric($id)) {
            $gaji = $this->gajiModel->find($id);
        } else {

            $gaji = $this->gajiModel->where('idgaji', $id)->first();
        }

        if (!$gaji) {
            return redirect()->to('admin/gaji')->with('error', 'Data gaji tidak ditemukan.');
        }


        $data = [
            'status' => 'paid',
            'keterangan' => 'Pembayaran diproses pada ' . date('d-m-Y H:i:s')
        ];

        if ($this->gajiModel->update($gaji['idgaji'], $data)) {
            return redirect()->to('admin/gaji')->with('success', 'Pembayaran gaji berhasil diproses.');
        } else {
            return redirect()->to('admin/gaji')->with('error', 'Gagal memproses pembayaran gaji.');
        }
    }

    /**
     * Membatalkan pembayaran gaji
     */
    public function cancelPayment($id)
    {

        if (is_numeric($id)) {
            $gaji = $this->gajiModel->find($id);
        } else {

            $gaji = $this->gajiModel->where('idgaji', $id)->first();
        }

        if (!$gaji) {
            return redirect()->to('admin/gaji')->with('error', 'Data gaji tidak ditemukan.');
        }


        $data = [
            'status' => 'cancelled',
            'keterangan' => 'Pembayaran dibatalkan pada ' . date('d-m-Y H:i:s')
        ];

        if ($this->gajiModel->update($gaji['idgaji'], $data)) {
            return redirect()->to('admin/gaji')->with('success', 'Pembayaran gaji berhasil dibatalkan.');
        } else {
            return redirect()->to('admin/gaji')->with('error', 'Gagal membatalkan pembayaran gaji.');
        }
    }

    /**
     * Mendapatkan data pegawai melalui AJAX
     */
    public function getPegawai()
    {

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        try {
            $search = $this->request->getGet('search');
            $page = $this->request->getGet('page') ?? 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $builder = $this->db->table('pegawai');
            $builder->select('pegawai.idpegawai, pegawai.namapegawai, pegawai.nik, jabatan.namajabatan, bagian.namabagian');
            $builder->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid');
            $builder->join('bagian', 'bagian.idbagian = jabatan.bagianid');


            if ($search) {
                $builder->groupStart()
                    ->like('pegawai.namapegawai', $search)
                    ->orLike('pegawai.nik', $search)
                    ->orLike('jabatan.namajabatan', $search)
                    ->orLike('bagian.namabagian', $search)
                    ->groupEnd();
            }


            $total = $builder->countAllResults(false);


            $pegawai_list = $builder->limit($limit, $offset)->get()->getResultArray();


            $results = [];
            foreach ($pegawai_list as $pegawai) {
                $results[] = [
                    'id' => $pegawai['idpegawai'],
                    'text' => $pegawai['namapegawai'] . ' - ' . $pegawai['nik'] . ' (' . $pegawai['namajabatan'] . ')',
                    'nik' => $pegawai['nik'],
                    'jabatan' => $pegawai['namajabatan'],
                    'bagian' => $pegawai['namabagian']
                ];
            }

            return $this->response->setJSON([
                'results' => $results,
                'pagination' => [
                    'more' => ($offset + $limit) < $total
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error pada getPegawai: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
