<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\PegawaiModel;
use App\Models\UserModel;
use App\Models\OfficeSettingModel;
use App\Models\AbsensiSettingModel;
use CodeIgniter\I18n\Time;

class Dashboard extends BaseController
{
    protected $absensiModel;
    protected $pegawaiModel;
    protected $userModel;
    protected $session;
    protected $officeSettingModel;
    protected $absensiSettingModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->userModel = new UserModel();
        $this->session = session();
        $this->officeSettingModel = new OfficeSettingModel();
        $this->absensiSettingModel = new AbsensiSettingModel();
    }

    public function index()
    {

        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }


        $officeSetting = $this->officeSettingModel->first();
        $maxDistance = $officeSetting['radius'] ?? 5; // default 5m jika belum diset
        $officeLocation = [
            'latitude' => $officeSetting['latitude'] ?? -0.9467468,
            'longitude' => $officeSetting['longitude'] ?? 100.3534272
        ];

        $today = date('Y-m-d');
        $absensiHariIni = $this->absensiModel->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal', $today)
            ->first();


        $startDate = date('Y-m-01'); // Tanggal awal bulan ini
        $endDate = date('Y-m-t');    // Tanggal akhir bulan ini

        $absensiStats = $this->absensiModel->select('status, COUNT(*) as total')
            ->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->groupBy('status')
            ->findAll();


        $stats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total' => 0
        ];


        $totalWorkDays = 0;
        $currentDate = strtotime($startDate);
        $today = strtotime(date('Y-m-d')); // Timestamp hari ini
        $endDateTimestamp = min(strtotime($endDate), $today); // Ambil yang lebih kecil antara akhir bulan atau hari ini

        while ($currentDate <= $endDateTimestamp) {
            $dayOfWeek = date('N', $currentDate);
            if ($dayOfWeek < 6) { // 1 (Senin) sampai 5 (Jumat)
                $totalWorkDays++;
            }
            $currentDate = strtotime('+1 day', $currentDate);
        }

        $stats['total'] = $totalWorkDays;


        foreach ($absensiStats as $stat) {
            if (isset($stats[$stat['status']])) {
                $stats[$stat['status']] = (int)$stat['total'];
            }
        }


        $totalAbsensi = $stats['hadir'] + $stats['sakit'] + $stats['izin'];
        $stats['alpa'] = max(0, $totalWorkDays - $totalAbsensi);


        $date7DaysAgo = date('Y-m-d', strtotime('-7 days'));
        $absensi7Hari = $this->absensiModel->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal >=', $date7DaysAgo)
            ->where('tanggal <=', $today)
            ->orderBy('tanggal', 'DESC')
            ->findAll();


        $lemburModel = new \App\Models\LemburModel();
        $lemburTerbaru = $lemburModel->where('pegawai_id', $pegawai['idpegawai'])
            ->orderBy('tanggallembur', 'DESC')
            ->limit(3)
            ->findAll();


        $gajiModel = new \App\Models\GajiModel();
        $gajiTerbaru = $gajiModel->where('pegawai_id', $pegawai['idpegawai'])
            ->orderBy('tanggal', 'DESC')
            ->limit(1)
            ->first();

        $data = [
            'title' => 'Dashboard Pegawai',
            'pegawai' => $pegawai,
            'today' => $today,
            'absensiHariIni' => $absensiHariIni,
            'stats' => $stats,
            'absensi7Hari' => $absensi7Hari,
            'maxDistance' => $maxDistance,
            'officeLocation' => $officeLocation,
            'lemburTerbaru' => $lemburTerbaru,
            'gajiTerbaru' => $gajiTerbaru
        ];

        return view('pegawai/dashboard', $data);
    }

    public function absenMasuk()
    {

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid request'
            ]);
        }


        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data pegawai tidak ditemukan'
            ]);
        }


        $today = date('Y-m-d');
        $absensiHariIni = $this->absensiModel->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal', $today)
            ->first();

        if ($absensiHariIni) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda sudah melakukan absensi hari ini'
            ]);
        }


        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');

        if (!$latitude || !$longitude) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data lokasi tidak lengkap'
            ]);
        }


        $officeSetting = $this->officeSettingModel->first();
        $maxDistance = $officeSetting['radius'] ?? 5; // default 5m jika belum diset
        $officeLocation = [
            'latitude' => $officeSetting['latitude'] ?? -0.9467468,
            'longitude' => $officeSetting['longitude'] ?? 100.3534272
        ];


        $distance = $this->calculateDistance(
            $latitude,
            $longitude,
            $officeLocation['latitude'],
            $officeLocation['longitude']
        );


        if ($distance > $maxDistance) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda berada diluar jangkauan kantor. Jarak Anda: ' . round($distance, 2) . ' meter'
            ]);
        }


        $absensiSetting = $this->absensiSettingModel->first();
        $jamMasuk = $absensiSetting['jam_masuk'] ?? '08:00:00';


        $now = time();
        $batasJamMasuk = strtotime(date('Y-m-d') . ' ' . $jamMasuk);
        $isTerlambat = $now > $batasJamMasuk;


        $terlambat = 0;
        if ($isTerlambat) {
            $terlambat = floor(($now - $batasJamMasuk) / 60);
        }


        $data = [
            'idpegawai' => $pegawai['idpegawai'],
            'tanggal' => $today,
            'jammasuk' => date('Y-m-d H:i:s'),
            'jamkeluar' => null,
            'status' => 'hadir',
            'keterangan' => $isTerlambat ? 'Terlambat ' . $terlambat . ' menit' : '',
            'latitude_masuk' => $latitude,
            'longitude_masuk' => $longitude,
            'latitude_keluar' => null,
            'longitude_keluar' => null,
            'terlambat' => $terlambat
        ];

        $this->absensiModel->insert($data);

        $message = 'Absensi masuk berhasil dicatat';
        if ($isTerlambat) {
            $message .= '. Anda tercatat terlambat ' . $terlambat . ' menit!';
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => $message
        ]);
    }

    public function absenPulang()
    {

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid request'
            ]);
        }


        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data pegawai tidak ditemukan'
            ]);
        }


        $today = date('Y-m-d');
        $absensiHariIni = $this->absensiModel->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal', $today)
            ->first();

        if (!$absensiHariIni) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda belum melakukan absensi masuk hari ini'
            ]);
        }

        if ($absensiHariIni['jamkeluar']) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda sudah melakukan absensi pulang hari ini'
            ]);
        }


        $absensiSetting = $this->absensiSettingModel->first();
        $jamPulang = $absensiSetting['jam_pulang'] ?? '17:00:00';


        $now = time();
        $batasJamPulang = strtotime(date('Y-m-d') . ' ' . $jamPulang); // Minimal jam 14:00 untuk absen pulang

        if ($now < $batasJamPulang) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Belum waktunya pulang. Absen pulang bisa dilakukan mulai jam ' . $jamPulang
            ]);
        }


        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');

        if (!$latitude || !$longitude) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data lokasi tidak lengkap'
            ]);
        }


        $officeSetting = $this->officeSettingModel->first();
        $maxDistance = $officeSetting['radius'] ?? 20; // default 20m jika belum diset
        $officeLocation = [
            'latitude' => $officeSetting['latitude'] ?? -0.9467468,
            'longitude' => $officeSetting['longitude'] ?? 100.3534272
        ];


        $distance = $this->calculateDistance(
            $latitude,
            $longitude,
            $officeLocation['latitude'],
            $officeLocation['longitude']
        );


        if ($distance > $maxDistance) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda berada diluar jangkauan kantor. Jarak Anda: ' . round($distance, 2) . ' meter'
            ]);
        }


        $data = [
            'jamkeluar' => date('Y-m-d H:i:s'),
            'latitude_keluar' => $latitude,
            'longitude_keluar' => $longitude
        ];

        $this->absensiModel->update($absensiHariIni['idabsensi'], $data);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Absensi pulang berhasil dicatat'
        ]);
    }

    public function riwayat($bulan = null, $tahun = null)
    {

        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }


        if ($bulan === null) {
            $bulan = date('n');
        }

        if ($tahun === null) {
            $tahun = date('Y');
        }


        $bulan = (int)$bulan;
        $tahun = (int)$tahun;


        if ($bulan < 1 || $bulan > 12) {
            $bulan = date('n');
        }

        if ($tahun < 2020 || $tahun > date('Y')) {
            $tahun = date('Y');
        }


        $startDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $endDate = date('Y-m-t', strtotime($startDate));


        $absensi = $this->absensiModel->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->orderBy('tanggal', 'DESC')
            ->findAll();


        $absensiStats = $this->absensiModel->select('status, COUNT(*) as total')
            ->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->groupBy('status')
            ->findAll();


        $stats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total' => 0
        ];


        $totalWorkDays = 0;
        $currentDate = strtotime($startDate);
        $today = strtotime(date('Y-m-d')); // Timestamp hari ini
        $endDateTimestamp = min(strtotime($endDate), $today); // Ambil yang lebih kecil antara akhir bulan atau hari ini

        while ($currentDate <= $endDateTimestamp) {
            $dayOfWeek = date('N', $currentDate);
            if ($dayOfWeek < 6) { // 1 (Senin) sampai 5 (Jumat)
                $totalWorkDays++;
            }
            $currentDate = strtotime('+1 day', $currentDate);
        }

        $stats['total'] = $totalWorkDays;


        foreach ($absensiStats as $stat) {
            if (isset($stats[$stat['status']])) {
                $stats[$stat['status']] = (int)$stat['total'];
            }
        }


        $totalAbsensi = $stats['hadir'] + $stats['sakit'] + $stats['izin'];
        $stats['alpa'] = max(0, $totalWorkDays - $totalAbsensi);

        $data = [
            'title' => 'Riwayat Absensi',
            'pegawai' => $pegawai,
            'absensi' => $absensi,
            'stats' => $stats,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];

        return view('pegawai/riwayat', $data);
    }

    /**
     * Menampilkan riwayat lembur pegawai
     */
    public function lembur($bulan = null, $tahun = null)
    {

        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }


        if (!$bulan) {
            $bulan = date('m');
        }
        if (!$tahun) {
            $tahun = date('Y');
        }


        $lemburModel = new \App\Models\LemburModel();


        $startDate = $tahun . '-' . $bulan . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $lembur = $lemburModel->select('lembur.*')
            ->where('pegawai_id', $pegawai['idpegawai'])
            ->where('tanggallembur >=', $startDate)
            ->where('tanggallembur <=', $endDate)
            ->orderBy('tanggallembur', 'DESC')
            ->findAll();


        $totalLembur = 0;
        foreach ($lembur as $row) {
            $jammulai = strtotime($row['jammulai']);
            $jamselesai = strtotime($row['jamselesai']);


            if ($jamselesai < $jammulai) {
                $jamselesai += 86400; // Tambah 24 jam
            }

            $durasiMenit = round(abs($jamselesai - $jammulai) / 60);
            $durasiJam = $durasiMenit / 60;
            $totalLembur += $durasiJam;
        }

        $data = [
            'title' => 'Riwayat Lembur',
            'pegawai' => $pegawai,
            'lembur' => $lembur,
            'totalLembur' => $totalLembur,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];

        return view('pegawai/lembur', $data);
    }

    /**
     * Menampilkan riwayat gaji pegawai
     */
    public function gaji($bulan = null, $tahun = null)
    {

        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }


        if (!$bulan) {
            $bulan = date('m');
        }
        if (!$tahun) {
            $tahun = date('Y');
        }


        $gajiModel = new \App\Models\GajiModel();


        $periode = $bulan . '-' . $tahun;


        $gaji = $gajiModel->where('pegawai_id', $pegawai['idpegawai'])
            ->where('periode', $periode)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Slip Gaji',
            'pegawai' => $pegawai,
            'gaji' => $gaji,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];

        return view('pegawai/gaji', $data);
    }

    /**
     * Menampilkan detail slip gaji
     */
    public function slipGaji($id)
    {

        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }


        $gajiModel = new \App\Models\GajiModel();


        $gaji = $gajiModel->getGajiWithPegawai($id);


        if (!$gaji || $gaji['pegawai_id'] != $pegawai['idpegawai']) {
            $this->session->setFlashdata('error', 'Data slip gaji tidak ditemukan');
            return redirect()->to('pegawai/dashboard/gaji');
        }


        $officeSettingModel = new \App\Models\OfficeSettingModel();
        $setting = $officeSettingModel->first();


        $tunjanganTransport = $setting['tunjangan_transport'] ?? 10000;
        $tunjanganMakan = $setting['tunjangan_makan'] ?? 15000;
        $tarifLembur = $setting['tarif_lembur'] ?? 20000; // Per jam


        $db = \Config\Database::connect();
        $dataPegawai = $db->table('pegawai')
            ->select('pegawai.*, jabatan.namajabatan as nama_jabatan, jabatan.gajipokok, bagian.namabagian')
            ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid')
            ->join('bagian', 'bagian.idbagian = jabatan.bagianid')
            ->where('pegawai.idpegawai', $gaji['pegawai_id'])
            ->get()
            ->getRowArray();


        $gaji['nama_jabatan'] = $dataPegawai['nama_jabatan'] ?? '-';
        $gaji['namabagian'] = $dataPegawai['namabagian'] ?? '-';

        $gajiPokok = $dataPegawai['gajipokok'] ?? 0;
        $tunjanganHarian = ($tunjanganTransport + $tunjanganMakan) * $gaji['totalabsen'];
        $upahLembur = $gaji['totallembur'] * $tarifLembur;
        $gajiBruto = $gajiPokok + $tunjanganHarian + $upahLembur;

        $komponenGaji = [
            'gaji_pokok' => $gajiPokok,
            'tunjangan_harian' => $tunjanganHarian,
            'upah_lembur' => $upahLembur,
            'gaji_bruto' => $gajiBruto,
            'potongan' => $gaji['potongan'],
            'gaji_bersih' => $gaji['gajibersih']
        ];

        $detailGaji = [
            'total_absensi' => $gaji['totalabsen'],
            'total_lembur' => $gaji['totallembur'],
            'tunjangan_transport' => $tunjanganTransport,
            'tunjangan_makan' => $tunjanganMakan,
            'tarif_lembur' => $tarifLembur
        ];

        $data = [
            'title' => 'Detail Slip Gaji',
            'pegawai' => $pegawai,
            'gaji' => $gaji,
            'komponen_gaji' => $komponenGaji,
            'detail' => $detailGaji,
            'setting' => $setting
        ];

        return view('pegawai/slip_gaji', $data);
    }


    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = $R * $c;

        return $d;
    }
}
