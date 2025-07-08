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

        // Filter data
        $status = $request->getGet('status');
        $search = $request->getGet('search');

        // Query builder
        $db = \Config\Database::connect();
        $builder = $db->table('izin');
        $builder->select('izin.*, pegawai.namapegawai, pegawai.nik');
        $builder->join('pegawai', 'pegawai.idpegawai = izin.pegawai_id');

        // Filter berdasarkan status
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

        // Filter berdasarkan pencarian
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
        // Cek apakah request AJAX
        $isAjax = $this->request->getGet('ajax') == 1;

        // Log untuk debugging
        log_message('debug', "ADMIN IZIN SHOW: ID={$id}, AJAX={$isAjax}");

        try {
            // Gunakan getIzinWithPegawai karena sudah mendukung pencarian berdasarkan ID atau idizin
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
        // Cek apakah request AJAX
        $isAjax = $this->request->isAJAX();

        log_message('debug', "ADMIN IZIN APPROVE: ID={$id}, AJAX={$isAjax}");

        // Cek jika ID berupa kode IZN (bukan ID numeric)
        if (is_numeric($id)) {
            $izin = $this->izinModel->find($id);
            log_message('debug', "ADMIN IZIN APPROVE: Mencari izin dengan ID numeric {$id}");
        } else {
            // Cari berdasarkan idizin (kode IZN)
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
        // Cek apakah request AJAX
        $isAjax = $this->request->isAJAX();

        log_message('debug', "ADMIN IZIN REJECT: ID={$id}, AJAX={$isAjax}");

        // Cek jika ID berupa kode IZN (bukan ID numeric)
        if (is_numeric($id)) {
            $izin = $this->izinModel->find($id);
            log_message('debug', "ADMIN IZIN REJECT: Mencari izin dengan ID numeric {$id}");
        } else {
            // Cari berdasarkan idizin (kode IZN)
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

        // Filter data
        $startDate = $request->getGet('start_date') ?? date('Y-m-01'); // Default: awal bulan ini
        $endDate = $request->getGet('end_date') ?? date('Y-m-d'); // Default: hari ini
        $status = $request->getGet('status') ?? ''; // Default: semua status
        $pegawaiId = $request->getGet('pegawai_id') ?? ''; // Default: semua pegawai

        // Query builder
        $db = \Config\Database::connect();
        $builder = $db->table('izin');
        $builder->select('izin.*, pegawai.namapegawai, pegawai.nik');
        $builder->join('pegawai', 'pegawai.idpegawai = izin.pegawai_id');

        // Filter berdasarkan tanggal
        $builder->where('tanggalmulaiizin >=', $startDate);
        $builder->where('tanggalselesaiizin <=', $endDate);

        // Filter berdasarkan status
        if ($status !== '') {
            $builder->where('statusizin', $status);
        }

        // Filter berdasarkan pegawai
        if ($pegawaiId !== '') {
            $builder->where('pegawai_id', $pegawaiId);
        }

        $builder->orderBy('izin.created_at', 'DESC');
        $izinList = $builder->get()->getResultArray();

        $data = [
            'title' => 'Laporan Pengajuan Izin',
            'izin_list' => $izinList,
            'pegawai_list' => $this->pegawaiModel->findAll(),
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'pegawai_id' => $pegawaiId
            ]
        ];

        return view('admin/izin/report', $data);
    }

    /**
     * Export laporan izin ke Excel
     */
    public function export()
    {
        // Redirect kembali ke halaman report dengan pesan
        return redirect()->to('admin/izin/report')->with('error', 'Fitur export belum tersedia. Silakan instal library PhpSpreadsheet terlebih dahulu.');
    }
}
