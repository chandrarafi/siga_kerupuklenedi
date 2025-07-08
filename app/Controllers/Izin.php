<?php

namespace App\Controllers;

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
     * Menampilkan halaman pengajuan izin untuk pegawai
     */
    public function index()
    {
        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->getPegawaiByUserId($userId);

        // Cek apakah request AJAX
        $isAjax = $this->request->getGet('ajax') == 1;

        if (!$pegawai) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data pegawai tidak ditemukan.'
                ]);
            }
            return redirect()->to('pegawai/dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }

        $data = [
            'title' => 'Pengajuan Izin',
            'pegawai' => $pegawai,
            'izin_list' => $this->izinModel->getIzinByPegawai($pegawai['idpegawai'])
        ];

        if ($isAjax) {
            // Jika request AJAX, hanya return bagian tabel
            return view('pegawai/izin/_table', $data);
        }

        return view('pegawai/izin/index', $data);
    }

    /**
     * Menampilkan form pengajuan izin baru
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Pengajuan Izin',
            'validation' => \Config\Services::validation()
        ];

        return view('pegawai/izin/create', $data);
    }

    /**
     * Menyimpan data pengajuan izin baru
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'tanggal_izin' => 'required',
            'jenis_izin' => 'required',
            'keterangan' => 'required',
        ];

        // Jika ada file yang diupload, validasi file tersebut
        $bukti = $this->request->getFile('bukti');
        if ($bukti && $bukti->isValid()) {
            $rules['bukti'] = 'max_size[bukti,2048]|mime_in[bukti,image/png,image/jpg,image/jpeg,application/pdf]';
        }

        // Cek apakah request AJAX
        $isAjax = $this->request->isAJAX();

        if (!$this->validate($rules)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Dapatkan ID pegawai dari user yang login
        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->getPegawaiByUserId($userId);

        if (!$pegawai) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data pegawai tidak ditemukan.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Data pegawai tidak ditemukan.');
        }

        // Dapatkan tanggal yang dipilih
        $tanggalIzin = $this->request->getPost('tanggal_izin');
        $selectedDatesArray = explode(',', $tanggalIzin);

        // Bersihkan array dari nilai kosong
        $selectedDatesArray = array_filter($selectedDatesArray, function ($date) {
            return !empty(trim($date));
        });

        // Validasi tanggal
        if (empty($selectedDatesArray)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Tanggal izin harus dipilih',
                    'errors' => ['tanggal_izin' => 'Tanggal izin harus dipilih']
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Tanggal izin harus dipilih.');
        }

        // Validasi maksimal 3 hari
        if (count($selectedDatesArray) > 3) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Maksimal 3 hari yang dapat dipilih',
                    'errors' => ['tanggal_izin' => 'Maksimal 3 hari yang dapat dipilih']
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Maksimal 3 hari yang dapat dipilih.');
        }

        // Urutkan tanggal
        sort($selectedDatesArray);

        // NONAKTIFKAN validasi tanggal berurutan
        $isConsecutive = true; // Anggap selalu berurutan

        $buktiName = '';

        if ($bukti && $bukti->isValid() && !$bukti->hasMoved()) {
            $newName = $bukti->getRandomName();
            $bukti->move(ROOTPATH . 'public/uploads/izin', $newName);
            $buktiName = $newName;
        }

        // Generate ID izin
        $idIzin = $this->izinModel->generateIdIzin();

        try {
            // Bersihkan array dari nilai kosong dan trim spasi
            $selectedDatesArray = array_map('trim', $selectedDatesArray);
            $selectedDatesArray = array_filter($selectedDatesArray, function ($date) {
                return !empty($date);
            });

            // Urutkan tanggal yang dipilih dan simpan sebagai array baru
            sort($selectedDatesArray);

            // Debug tanggal yang dipilih
            log_message('debug', 'Tanggal yang dipilih (setelah sort): ' . implode(', ', $selectedDatesArray));

            // Ambil tanggal terawal dan terakhir
            $tanggalMulai = reset($selectedDatesArray); // Tanggal pertama (terawal)
            $tanggalSelesai = end($selectedDatesArray);  // Tanggal terakhir (terakhir)

            // Debug tanggal mulai dan selesai
            log_message('debug', 'Tanggal mulai: ' . $tanggalMulai . ', Tanggal selesai: ' . $tanggalSelesai);

            // Reset pointer array untuk penggunaan selanjutnya
            reset($selectedDatesArray);

            // Simpan data izin
            $data = [
                'idizin' => $idIzin,
                'pegawai_id' => $pegawai['idpegawai'],
                'tanggalmulaiizin' => $tanggalMulai, // Gunakan tanggal terawal sebagai tanggal mulai
                'tanggalselesaiizin' => $tanggalSelesai, // Gunakan tanggal terakhir sebagai tanggal selesai
                'selected_dates' => implode(', ', $selectedDatesArray), // Simpan tanggal yang sudah diurutkan dengan format konsisten
                'jenisizin' => $this->request->getPost('jenis_izin'),
                'alasan' => $this->request->getPost('keterangan'),
                'lampiran' => $buktiName,
                'statusizin' => 3, // 3 = menunggu
                'keterangan_admin' => null
            ];

            if ($this->izinModel->insert($data)) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => true,
                        'message' => 'Pengajuan izin berhasil disimpan.',
                        'redirect' => site_url('pegawai/izin')
                    ]);
                }
                return redirect()->to('pegawai/izin')->with('success', 'Pengajuan izin berhasil disimpan.');
            } else {
                // Jika gagal, hapus file yang sudah diupload
                if ($buktiName && file_exists(ROOTPATH . 'public/uploads/izin/' . $buktiName)) {
                    unlink(ROOTPATH . 'public/uploads/izin/' . $buktiName);
                }

                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal menyimpan pengajuan izin: ' . implode(', ', $this->izinModel->errors())
                    ]);
                }
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengajuan izin: ' . implode(', ', $this->izinModel->errors()));
            }
        } catch (\Exception $e) {
            // Jika terjadi error, hapus file yang sudah diupload
            if ($buktiName && file_exists(ROOTPATH . 'public/uploads/izin/' . $buktiName)) {
                unlink(ROOTPATH . 'public/uploads/izin/' . $buktiName);
            }

            log_message('error', 'Error saat menyimpan izin: ' . $e->getMessage());

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail izin
     */
    public function show($id)
    {
        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->getPegawaiByUserId($userId);

        // Cek apakah request AJAX
        $isAjax = $this->request->getGet('ajax') == 1;

        // Log untuk debugging
        log_message('debug', "IZIN SHOW: ID={$id}, AJAX={$isAjax}");

        if (!$pegawai) {
            log_message('error', "IZIN SHOW: Pegawai tidak ditemukan untuk UserID={$userId}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data pegawai tidak ditemukan.'
                ]);
            }
            return redirect()->to('pegawai/dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }

        log_message('debug', "IZIN SHOW: Pegawai ditemukan dengan ID={$pegawai['idpegawai']}");

        // Cek jika ID berupa kode IZN (bukan ID numeric)
        if (is_numeric($id)) {
            $izin = $this->izinModel->find($id);
            log_message('debug', "IZIN SHOW: Mencari izin dengan ID numeric {$id}");
        } else {
            // Cari berdasarkan idizin (kode IZN)
            $izin = $this->izinModel->where('idizin', $id)->first();
            log_message('debug', "IZIN SHOW: Mencari izin dengan ID string {$id}");
        }

        if ($izin) {
            log_message('debug', "IZIN SHOW: Izin ditemukan: " . json_encode($izin));
        } else {
            log_message('error', "IZIN SHOW: Izin tidak ditemukan dengan ID={$id}");
        }

        // Pastikan izin milik pegawai yang sedang login
        if (!$izin || $izin['pegawai_id'] !== $pegawai['idpegawai']) {
            log_message('error', "IZIN SHOW: Izin tidak ditemukan atau bukan milik pegawai ini. ID Izin={$id}, ID Pegawai={$pegawai['idpegawai']}");
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data izin tidak ditemukan.'
                ]);
            }
            return redirect()->to('pegawai/izin')->with('error', 'Data izin tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Pengajuan Izin',
            'izin' => $izin,
            'pegawai' => $pegawai
        ];

        if ($isAjax) {
            $data['ajax'] = true;
            log_message('debug', "IZIN SHOW: Rendering view AJAX untuk ID={$id}");
            return view('pegawai/izin/show', $data);
        }

        log_message('debug', "IZIN SHOW: Rendering view normal untuk ID={$id}");
        return view('pegawai/izin/show', $data);
    }

    /**
     * Menampilkan form edit izin
     * Hanya izin yang belum disetujui yang bisa diedit
     */
    public function edit($id)
    {
        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->getPegawaiByUserId($userId);

        if (!$pegawai) {
            return redirect()->to('pegawai/dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }

        // Cek jika ID berupa kode IZN (bukan ID numeric)
        if (is_numeric($id)) {
            $izin = $this->izinModel->find($id);
        } else {
            // Cari berdasarkan idizin (kode IZN)
            $izin = $this->izinModel->where('idizin', $id)->first();
        }

        // Pastikan izin milik pegawai yang sedang login dan belum disetujui
        if (!$izin || $izin['pegawai_id'] !== $pegawai['idpegawai']) {
            return redirect()->to('pegawai/izin')->with('error', 'Data izin tidak ditemukan.');
        }

        if ($izin['statusizin'] != 3 && $izin['statusizin'] !== null) {
            return redirect()->to('pegawai/izin')->with('error', 'Hanya pengajuan dengan status menunggu yang dapat diedit.');
        }

        // Gunakan selected_dates jika tersedia, jika tidak buat dari tanggal mulai dan selesai
        $selectedDates = [];

        if (!empty($izin['selected_dates'])) {
            // Gunakan tanggal yang tersimpan di kolom selected_dates
            $selectedDates = explode(',', $izin['selected_dates']);
        } else {
            // Format tanggal menjadi array untuk flatpickr dari tanggal mulai dan selesai
            $tanggalMulai = new \DateTime($izin['tanggalmulaiizin']);
            $tanggalSelesai = new \DateTime($izin['tanggalselesaiizin']);
            $interval = $tanggalMulai->diff($tanggalSelesai);
            $durasiHari = $interval->days + 1;

            for ($i = 0; $i < $durasiHari; $i++) {
                $currentDate = clone $tanggalMulai;
                $currentDate->modify("+$i days");
                $selectedDates[] = $currentDate->format('Y-m-d');
            }
        }

        $data = [
            'title' => 'Edit Pengajuan Izin',
            'izin' => $izin,
            'pegawai' => $pegawai,
            'selectedDates' => $selectedDates,
            'validation' => \Config\Services::validation()
        ];

        return view('pegawai/izin/edit', $data);
    }

    /**
     * Memperbarui data izin
     */
    public function update($id)
    {
        // Validasi input
        $rules = [
            'tanggal_izin' => 'required',
            'jenis_izin' => 'required',
            'keterangan' => 'required',
        ];

        // Cek apakah ada file yang diupload
        $bukti = $this->request->getFile('bukti');
        if ($bukti && $bukti->isValid() && !$bukti->hasMoved()) {
            $rules['bukti'] = 'max_size[bukti,2048]|mime_in[bukti,image/png,image/jpg,image/jpeg,application/pdf]';
        }

        // Cek apakah request AJAX
        $isAjax = $this->request->isAJAX();

        if (!$this->validate($rules)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Dapatkan ID pegawai dari user yang login
        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->getPegawaiByUserId($userId);

        if (!$pegawai) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data pegawai tidak ditemukan.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Data pegawai tidak ditemukan.');
        }

        // Cari data izin yang akan diupdate
        // Cek jika ID berupa kode IZN (bukan ID numeric)
        if (is_numeric($id)) {
            $izin = $this->izinModel->find($id);
        } else {
            // Cari berdasarkan idizin (kode IZN)
            $izin = $this->izinModel->where('idizin', $id)->first();
        }

        // Pastikan izin milik pegawai yang sedang login dan belum disetujui
        if (!$izin || $izin['pegawai_id'] !== $pegawai['idpegawai']) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data izin tidak ditemukan.'
                ]);
            }
            return redirect()->to('pegawai/izin')->with('error', 'Data izin tidak ditemukan.');
        }

        if ($izin['statusizin'] != 3 && $izin['statusizin'] !== null) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Hanya pengajuan dengan status menunggu yang dapat diedit.'
                ]);
            }
            return redirect()->to('pegawai/izin')->with('error', 'Hanya pengajuan dengan status menunggu yang dapat diedit.');
        }

        // Dapatkan tanggal yang dipilih
        $tanggalIzin = $this->request->getPost('tanggal_izin');
        $selectedDatesArray = explode(',', $tanggalIzin);

        // Bersihkan array dari nilai kosong
        $selectedDatesArray = array_filter($selectedDatesArray, function ($date) {
            return !empty(trim($date));
        });

        // Validasi tanggal
        if (empty($selectedDatesArray)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Tanggal izin harus dipilih',
                    'errors' => ['tanggal_izin' => 'Tanggal izin harus dipilih']
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Tanggal izin harus dipilih.');
        }

        // Validasi maksimal 3 hari
        if (count($selectedDatesArray) > 3) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Maksimal 3 hari yang dapat dipilih',
                    'errors' => ['tanggal_izin' => 'Maksimal 3 hari yang dapat dipilih']
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Maksimal 3 hari yang dapat dipilih.');
        }

        // Urutkan tanggal
        sort($selectedDatesArray);

        // NONAKTIFKAN validasi tanggal berurutan
        $isConsecutive = true; // Anggap selalu berurutan



        // Bersihkan array dari nilai kosong dan trim spasi
        $selectedDatesArray = array_map('trim', $selectedDatesArray);
        $selectedDatesArray = array_filter($selectedDatesArray, function ($date) {
            return !empty($date);
        });

        // Urutkan tanggal yang dipilih dan simpan sebagai array baru
        sort($selectedDatesArray);

        // Debug tanggal yang dipilih
        log_message('debug', 'UPDATE: Tanggal yang dipilih (setelah sort): ' . implode(', ', $selectedDatesArray));

        // Ambil tanggal terawal dan terakhir
        $tanggalMulai = reset($selectedDatesArray); // Tanggal pertama (terawal)
        $tanggalSelesai = end($selectedDatesArray);  // Tanggal terakhir (terakhir)

        // Debug tanggal mulai dan selesai
        log_message('debug', 'UPDATE: Tanggal mulai: ' . $tanggalMulai . ', Tanggal selesai: ' . $tanggalSelesai);

        // Reset pointer array untuk penggunaan selanjutnya
        reset($selectedDatesArray);

        // Update data izin
        $data = [
            'tanggalmulaiizin' => $tanggalMulai, // Gunakan tanggal terawal sebagai tanggal mulai
            'tanggalselesaiizin' => $tanggalSelesai, // Gunakan tanggal terakhir sebagai tanggal selesai
            'selected_dates' => implode(', ', $selectedDatesArray), // Simpan tanggal yang sudah diurutkan dengan format konsisten
            'jenisizin' => $this->request->getPost('jenis_izin'),
            'alasan' => $this->request->getPost('keterangan'),
        ];

        // Upload file bukti jika ada
        if ($bukti && $bukti->isValid() && !$bukti->hasMoved()) {
            // Hapus file lama jika ada
            if (!empty($izin['lampiran']) && file_exists(ROOTPATH . 'public/uploads/izin/' . $izin['lampiran'])) {
                unlink(ROOTPATH . 'public/uploads/izin/' . $izin['lampiran']);
            }

            $newName = $bukti->getRandomName();
            $bukti->move(ROOTPATH . 'public/uploads/izin', $newName);
            $data['lampiran'] = $newName;
        }

        try {
            if ($this->izinModel->update($id, $data)) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => true,
                        'message' => 'Pengajuan izin berhasil diperbarui.',
                        'redirect' => site_url('pegawai/izin')
                    ]);
                }
                return redirect()->to('pegawai/izin')->with('success', 'Pengajuan izin berhasil diperbarui.');
            } else {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal memperbarui pengajuan izin: ' . implode(', ', $this->izinModel->errors())
                    ]);
                }
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengajuan izin: ' . implode(', ', $this->izinModel->errors()));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error saat update izin: ' . $e->getMessage());

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data izin
     * Hanya izin yang belum disetujui yang bisa dihapus
     */
    public function delete($id)
    {
        $userId = $this->session->get('user_id');
        $pegawai = $this->pegawaiModel->getPegawaiByUserId($userId);

        if (!$pegawai) {
            return redirect()->to('pegawai/dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }

        // Cari data izin yang akan dihapus
        // Cek jika ID berupa kode IZN (bukan ID numeric)
        if (is_numeric($id)) {
            $izin = $this->izinModel->find($id);
        } else {
            // Cari berdasarkan idizin (kode IZN)
            $izin = $this->izinModel->where('idizin', $id)->first();
        }

        // Pastikan izin milik pegawai yang sedang login dan belum disetujui
        if (!$izin || $izin['pegawai_id'] !== $pegawai['idpegawai']) {
            return redirect()->to('pegawai/izin')->with('error', 'Data izin tidak ditemukan.');
        }

        if ($izin['statusizin'] != 3 && $izin['statusizin'] !== null) {
            return redirect()->to('pegawai/izin')->with('error', 'Hanya pengajuan dengan status menunggu yang dapat dihapus.');
        }

        // Hapus file lampiran jika ada
        if (!empty($izin['lampiran'])) {
            $filePath = ROOTPATH . 'public/uploads/izin/' . $izin['lampiran'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus data izin
        if ($this->izinModel->delete($izin['idizin'])) {
            return redirect()->to('pegawai/izin')->with('success', 'Pengajuan izin berhasil dihapus.');
        } else {
            return redirect()->to('pegawai/izin')->with('error', 'Gagal menghapus pengajuan izin.');
        }
    }
}
