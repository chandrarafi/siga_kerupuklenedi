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
        // Pastikan user sudah login dan memiliki role pegawai
        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }

        // Ambil pengaturan lokasi kantor
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

        // Statistik absensi bulan ini
        $startDate = date('Y-m-01'); // Tanggal awal bulan ini
        $endDate = date('Y-m-t');    // Tanggal akhir bulan ini

        $absensiStats = $this->absensiModel->select('status, COUNT(*) as total')
            ->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->groupBy('status')
            ->findAll();

        // Inisialisasi statistik
        $stats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total' => 0
        ];

        // Hitung total hari kerja bulan ini (tidak termasuk Sabtu & Minggu dan hanya sampai hari ini)
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

        // Isi data statistik dari database
        foreach ($absensiStats as $stat) {
            if (isset($stats[$stat['status']])) {
                $stats[$stat['status']] = (int)$stat['total'];
            }
        }

        // Hitung alpa (tidak hadir tanpa keterangan)
        $totalAbsensi = $stats['hadir'] + $stats['sakit'] + $stats['izin'];
        $stats['alpa'] = max(0, $totalWorkDays - $totalAbsensi);

        // Ambil data absensi 7 hari terakhir
        $date7DaysAgo = date('Y-m-d', strtotime('-7 days'));
        $absensi7Hari = $this->absensiModel->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal >=', $date7DaysAgo)
            ->where('tanggal <=', $today)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        // Ambil data lembur terbaru
        $lemburModel = new \App\Models\LemburModel();
        $lemburTerbaru = $lemburModel->where('pegawai_id', $pegawai['idpegawai'])
            ->orderBy('tanggallembur', 'DESC')
            ->limit(3)
            ->findAll();

        // Ambil data gaji terbaru
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
        // Pastikan ini adalah request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Pastikan user sudah login dan memiliki role pegawai
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

        // Cek apakah sudah absen hari ini
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

        // Ambil data lokasi
        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');

        if (!$latitude || !$longitude) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data lokasi tidak lengkap'
            ]);
        }

        // Ambil pengaturan lokasi kantor
        $officeSetting = $this->officeSettingModel->first();
        $maxDistance = $officeSetting['radius'] ?? 5; // default 5m jika belum diset
        $officeLocation = [
            'latitude' => $officeSetting['latitude'] ?? -0.9467468,
            'longitude' => $officeSetting['longitude'] ?? 100.3534272
        ];

        // Hitung jarak dengan lokasi kantor
        $distance = $this->calculateDistance(
            $latitude,
            $longitude,
            $officeLocation['latitude'],
            $officeLocation['longitude']
        );

        // Cek jarak maksimum
        if ($distance > $maxDistance) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda berada diluar jangkauan kantor. Jarak Anda: ' . round($distance, 2) . ' meter'
            ]);
        }

        // Ambil pengaturan jam masuk
        $absensiSetting = $this->absensiSettingModel->first();
        $jamMasuk = $absensiSetting['jam_masuk'] ?? '08:00:00';

        // Cek waktu absen
        $now = time();
        $batasJamMasuk = strtotime(date('Y-m-d') . ' ' . $jamMasuk);
        $isTerlambat = $now > $batasJamMasuk;

        // Hitung keterlambatan dalam menit
        $terlambat = 0;
        if ($isTerlambat) {
            $terlambat = floor(($now - $batasJamMasuk) / 60);
        }

        // Simpan data absensi
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
        // Pastikan ini adalah request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Pastikan user sudah login dan memiliki role pegawai
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

        // Cek apakah sudah absen masuk hari ini
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

        // Ambil pengaturan jam pulang
        $absensiSetting = $this->absensiSettingModel->first();
        $jamPulang = $absensiSetting['jam_pulang'] ?? '17:00:00';

        // Cek waktu absen pulang
        $now = time();
        $batasJamPulang = strtotime(date('Y-m-d') . ' ' . $jamPulang); // Minimal jam 14:00 untuk absen pulang

        if ($now < $batasJamPulang) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Belum waktunya pulang. Absen pulang bisa dilakukan mulai jam ' . $jamPulang
            ]);
        }

        // Ambil data lokasi
        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');

        if (!$latitude || !$longitude) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data lokasi tidak lengkap'
            ]);
        }

        // Ambil pengaturan lokasi kantor
        $officeSetting = $this->officeSettingModel->first();
        $maxDistance = $officeSetting['radius'] ?? 20; // default 20m jika belum diset
        $officeLocation = [
            'latitude' => $officeSetting['latitude'] ?? -0.9467468,
            'longitude' => $officeSetting['longitude'] ?? 100.3534272
        ];

        // Hitung jarak dengan lokasi kantor
        $distance = $this->calculateDistance(
            $latitude,
            $longitude,
            $officeLocation['latitude'],
            $officeLocation['longitude']
        );

        // Cek jarak maksimum
        if ($distance > $maxDistance) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda berada diluar jangkauan kantor. Jarak Anda: ' . round($distance, 2) . ' meter'
            ]);
        }

        // Update data absensi
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
        // Pastikan user sudah login dan memiliki role pegawai
        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }

        // Default ke bulan dan tahun saat ini jika tidak ada parameter
        if ($bulan === null) {
            $bulan = date('n');
        }

        if ($tahun === null) {
            $tahun = date('Y');
        }

        // Konversi ke integer
        $bulan = (int)$bulan;
        $tahun = (int)$tahun;

        // Validasi bulan dan tahun
        if ($bulan < 1 || $bulan > 12) {
            $bulan = date('n');
        }

        if ($tahun < 2020 || $tahun > date('Y')) {
            $tahun = date('Y');
        }

        // Format tanggal untuk query
        $startDate = sprintf('%04d-%02d-01', $tahun, $bulan);
        $endDate = date('Y-m-t', strtotime($startDate));

        // Ambil data absensi
        $absensi = $this->absensiModel->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        // Statistik absensi bulan ini
        $absensiStats = $this->absensiModel->select('status, COUNT(*) as total')
            ->where('idpegawai', $pegawai['idpegawai'])
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->groupBy('status')
            ->findAll();

        // Inisialisasi statistik
        $stats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total' => 0
        ];

        // Hitung total hari kerja bulan ini (tidak termasuk Sabtu & Minggu dan hanya sampai hari ini)
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

        // Isi data statistik dari database
        foreach ($absensiStats as $stat) {
            if (isset($stats[$stat['status']])) {
                $stats[$stat['status']] = (int)$stat['total'];
            }
        }

        // Hitung alpa (tidak hadir tanpa keterangan)
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
        // Pastikan user sudah login dan memiliki role pegawai
        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }

        // Set bulan dan tahun default jika tidak ada
        if (!$bulan) {
            $bulan = date('m');
        }
        if (!$tahun) {
            $tahun = date('Y');
        }

        // Load model lembur
        $lemburModel = new \App\Models\LemburModel();

        // Ambil data lembur untuk bulan dan tahun yang dipilih
        $startDate = $tahun . '-' . $bulan . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $lembur = $lemburModel->select('lembur.*')
            ->where('pegawai_id', $pegawai['idpegawai'])
            ->where('tanggallembur >=', $startDate)
            ->where('tanggallembur <=', $endDate)
            ->orderBy('tanggallembur', 'DESC')
            ->findAll();

        // Hitung total jam lembur
        $totalLembur = 0;
        foreach ($lembur as $row) {
            $jammulai = strtotime($row['jammulai']);
            $jamselesai = strtotime($row['jamselesai']);

            // Jika jamselesai lebih kecil dari jammulai, berarti melewati tengah malam
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
        // Pastikan user sudah login dan memiliki role pegawai
        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }

        // Set bulan dan tahun default jika tidak ada
        if (!$bulan) {
            $bulan = date('m');
        }
        if (!$tahun) {
            $tahun = date('Y');
        }

        // Load model gaji
        $gajiModel = new \App\Models\GajiModel();

        // Format periode untuk pencarian
        $periode = $bulan . '-' . $tahun;

        // Ambil data gaji untuk periode yang dipilih
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
        // Pastikan user sudah login dan memiliki role pegawai
        if (!$this->session->has('user_id') || $this->session->get('role') !== 'pegawai') {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->where('userid', $userId)->first();

        if (!$pegawai) {
            $this->session->setFlashdata('error', 'Data pegawai tidak ditemukan');
            return redirect()->to('/logout');
        }

        // Load model gaji
        $gajiModel = new \App\Models\GajiModel();

        // Ambil detail gaji
        $gaji = $gajiModel->getGajiWithPegawai($id);

        // Verifikasi bahwa gaji ini milik pegawai yang sedang login
        if (!$gaji || $gaji['pegawai_id'] != $pegawai['idpegawai']) {
            $this->session->setFlashdata('error', 'Data slip gaji tidak ditemukan');
            return redirect()->to('pegawai/dashboard/gaji');
        }

        // Ambil setting office untuk perhitungan komponen gaji
        $officeSettingModel = new \App\Models\OfficeSettingModel();
        $setting = $officeSettingModel->first();

        // Nilai default jika setting tidak ditemukan
        $tunjanganTransport = $setting['tunjangan_transport'] ?? 10000;
        $tunjanganMakan = $setting['tunjangan_makan'] ?? 15000;
        $tarifLembur = $setting['tarif_lembur'] ?? 20000; // Per jam

        // Ambil data jabatan dan gaji pokok
        $db = \Config\Database::connect();
        $dataPegawai = $db->table('pegawai')
            ->select('pegawai.*, jabatan.namajabatan as nama_jabatan, jabatan.gajipokok, bagian.namabagian')
            ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid')
            ->join('bagian', 'bagian.idbagian = jabatan.bagianid')
            ->where('pegawai.idpegawai', $gaji['pegawai_id'])
            ->get()
            ->getRowArray();

        // Tambahkan data jabatan dan bagian ke dalam array gaji
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

    // Fungsi untuk menghitung jarak dalam meter
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
