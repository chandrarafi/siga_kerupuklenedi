<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\PegawaiModel;
use CodeIgniter\HTTP\ResponseInterface;

class Absensi extends BaseController
{
    protected $absensiModel;
    protected $pegawaiModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->pegawaiModel = new PegawaiModel();
    }

    public function index()
    {
        $tanggal = $this->request->getGet('tanggal');

        $query = $this->absensiModel->select('absensi.*, pegawai.namapegawai')
            ->join('pegawai', 'pegawai.idpegawai = absensi.idpegawai');

        // Jika tanggal diisi, filter berdasarkan tanggal
        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }

        // Urutkan berdasarkan tanggal terbaru
        $query->orderBy('tanggal', 'DESC');

        $absensi = $query->findAll();

        $data = [
            'title' => 'Data Absensi',
            'tanggal' => $tanggal ?? date('Y-m-d'),
            'absensi' => $absensi
        ];

        return view('admin/absensi/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Data Absensi',
            'pegawai' => $this->pegawaiModel->findAll()
        ];

        return view('admin/absensi/create', $data);
    }

    public function store()
    {
        // Validasi input
        $rules = [
            'idpegawai' => 'required',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[hadir,sakit,izin,alpa]',
            'jammasuk' => 'permit_empty',
            'jamkeluar' => 'permit_empty',
            'keterangan' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idpegawai = $this->request->getPost('idpegawai');
        $tanggal = $this->request->getPost('tanggal');

        // Cek apakah sudah ada absensi untuk pegawai pada tanggal tersebut
        if ($this->absensiModel->isAbsenExists($idpegawai, $tanggal)) {
            session()->setFlashdata('error', 'Pegawai sudah melakukan absensi pada tanggal ini');
            return redirect()->back()->withInput();
        }

        $data = [
            'idpegawai' => $idpegawai,
            'tanggal' => $tanggal,
            'jammasuk' => $this->request->getPost('jammasuk'),
            'jamkeluar' => $this->request->getPost('jamkeluar'),
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'longitude_masuk' => $this->request->getPost('longitude'),
            'latitude_masuk' => $this->request->getPost('latitude'),
        ];

        if ($this->absensiModel->insert($data)) {
            session()->setFlashdata('success', 'Data absensi berhasil ditambahkan');
            return redirect()->to('admin/absensi');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data absensi');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id = null)
    {
        $absensi = $this->absensiModel->find($id);

        if (!$absensi) {
            session()->setFlashdata('error', 'Data absensi tidak ditemukan');
            return redirect()->to('admin/absensi');
        }

        $data = [
            'title' => 'Edit Data Absensi',
            'absensi' => $absensi,
            'pegawai' => $this->pegawaiModel->findAll()
        ];

        return view('admin/absensi/edit', $data);
    }

    public function update($id = null)
    {
        // Validasi input
        $rules = [
            'idpegawai' => 'required',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[hadir,sakit,izin,alpa]',
            'jammasuk' => 'permit_empty',
            'jamkeluar' => 'permit_empty',
            'keterangan' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idpegawai = $this->request->getPost('idpegawai');
        $tanggal = $this->request->getPost('tanggal');

        // Cek apakah data yang diupdate adalah milik pegawai yang sama dan tanggal yang sama
        $existingAbsensi = $this->absensiModel->find($id);

        // Jika pegawai atau tanggal berubah, cek apakah sudah ada absensi untuk pegawai pada tanggal tersebut
        if (($existingAbsensi['idpegawai'] != $idpegawai || $existingAbsensi['tanggal'] != $tanggal) &&
            $this->absensiModel->isAbsenExists($idpegawai, $tanggal)
        ) {
            session()->setFlashdata('error', 'Pegawai sudah melakukan absensi pada tanggal ini');
            return redirect()->back()->withInput();
        }

        $data = [
            'idpegawai' => $idpegawai,
            'tanggal' => $tanggal,
            'jammasuk' => $this->request->getPost('jammasuk'),
            'jamkeluar' => $this->request->getPost('jamkeluar'),
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'longitude_masuk' => $this->request->getPost('longitude'),
            'latitude_masuk' => $this->request->getPost('latitude'),
        ];

        if ($this->absensiModel->update($id, $data)) {
            session()->setFlashdata('success', 'Data absensi berhasil diperbarui');
            return redirect()->to('admin/absensi');
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui data absensi');
            return redirect()->back()->withInput();
        }
    }

    public function delete($id = null)
    {
        if ($this->absensiModel->delete($id)) {
            session()->setFlashdata('success', 'Data absensi berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data absensi');
        }

        return redirect()->to('admin/absensi');
    }

    // API untuk absensi pegawai (akan diimplementasikan nanti)
    public function apiAbsen()
    {
        // Validasi API key atau token

        // Proses absensi
        $idpegawai = $this->request->getVar('idpegawai');
        $tanggal = date('Y-m-d');
        $jammasuk = date('H:i:s');
        $status = 'hadir';
        $longitude_masuk = $this->request->getVar('longitude');
        $latitude_masuk = $this->request->getVar('latitude');

        // Cek apakah sudah absen hari ini
        if ($this->absensiModel->isAbsenExists($idpegawai, $tanggal)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda sudah melakukan absensi hari ini'
            ]);
        }

        $data = [
            'idpegawai' => $idpegawai,
            'tanggal' => $tanggal,
            'jammasuk' => $jammasuk,
            'status' => $status,
            'longitude_masuk' => $longitude_masuk,
            'latitude_masuk' => $latitude_masuk,
        ];

        if ($this->absensiModel->insert($data)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Absensi berhasil'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal melakukan absensi'
            ]);
        }
    }

    public function apiPulang()
    {
        // Validasi API key atau token

        // Proses absensi pulang
        $idpegawai = $this->request->getVar('idpegawai');
        $tanggal = date('Y-m-d');
        $jamkeluar = date('H:i:s');

        // Cek apakah sudah absen masuk hari ini
        $absensi = $this->absensiModel->where('idpegawai', $idpegawai)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$absensi) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda belum melakukan absensi masuk hari ini'
            ]);
        }

        if ($this->absensiModel->update($absensi['idabsensi'], ['jamkeluar' => $jamkeluar])) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Absensi pulang berhasil'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal melakukan absensi pulang'
            ]);
        }
    }
}
