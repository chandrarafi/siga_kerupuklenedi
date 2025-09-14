<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\PegawaiModel;
use App\Models\JabatanModel;
use App\Models\BagianModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Absensi extends BaseController
{
    protected $absensiModel;
    protected $pegawaiModel;
    protected $jabatanModel;
    protected $bagianModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->jabatanModel = new JabatanModel();
        $this->bagianModel = new BagianModel();
    }

    public function index()
    {
        $tanggal = $this->request->getGet('tanggal');

        $query = $this->absensiModel->select('absensi.*, pegawai.namapegawai')
            ->join('pegawai', 'pegawai.idpegawai = absensi.idpegawai');


        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }


        $query->orderBy('tanggal', 'DESC');

        $absensi = $query->findAll();

        $data = [
            'title' => 'Data Absensi',
            'tanggal' => $tanggal ?? date('Y-m-d'),
            'absensi' => $absensi
        ];

        return view('admin/absensi/index', $data);
    }

    public function report()
    {

        $pegawaiId = $this->request->getGet('pegawai');
        $status = $this->request->getGet('status');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');
        $isPrint = $this->request->getGet('print');
        $isAjax = $this->request->getGet('ajax');


        $filters = [
            'pegawai' => $pegawaiId,
            'status' => $status,
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir
        ];


        if (!$isPrint && !$isAjax && !$pegawaiId && !$status && !$tanggalAwal && !$tanggalAkhir) {
            $data = [
                'title' => 'Laporan Data Absensi',
                'filters' => $filters,
                'pegawaiList' => $this->pegawaiModel->findAll()
            ];
            return view('admin/absensi/report_preview', $data);
        }


        $query = $this->absensiModel
            ->select('absensi.*, pegawai.namapegawai, jabatan.namajabatan')
            ->join('pegawai', 'pegawai.idpegawai = absensi.idpegawai')
            ->join('jabatan', 'jabatan.idjabatan = pegawai.jabatanid');


        if ($pegawaiId) {
            $query->where('absensi.idpegawai', $pegawaiId);
        }

        if ($status) {
            $query->where('absensi.status', $status);
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $query->where('absensi.tanggal >=', $tanggalAwal);
            $query->where('absensi.tanggal <=', $tanggalAkhir);
        } elseif ($tanggalAwal) {
            $query->where('absensi.tanggal', $tanggalAwal);
        }


        $query->orderBy('absensi.tanggal', 'DESC');

        $absensi = $query->findAll();


        $pegawai_name = '';
        if ($pegawaiId) {
            $pegawai = $this->pegawaiModel->find($pegawaiId);
            if ($pegawai) {
                $pegawai_name = $pegawai['namapegawai'];
            }
        }

        $data = [
            'title' => 'Laporan Data Absensi',
            'absensi' => $absensi,
            'filters' => $filters,
            'pegawai_name' => $pegawai_name
        ];


        if ($isPrint) {
            return $this->generatePdf($data);
        }


        if ($isAjax) {
            return view('admin/absensi/report_partial', $data);
        }


        $data['pegawaiList'] = $this->pegawaiModel->findAll();
        return view('admin/absensi/report_preview', $data);
    }

    private function generatePdf($data)
    {

        $data['logo'] = ROOTPATH . 'public/image/logo.png';


        if (!file_exists($data['logo'])) {
            $data['logo'] = '';
        } else {

            $data['logo'] = 'data:image/png;base64,' . base64_encode(file_get_contents($data['logo']));
        }


        $pdfHelper = new \App\Helpers\PdfHelper();


        $html = view('admin/absensi/pdf_template', $data);


        $filename = 'Laporan_Absensi_' . date('Y-m-d_H-i-s') . '.pdf';


        return $pdfHelper->generate($html, $filename, 'A4', 'landscape', [
            'attachment' => false // true untuk download, false untuk preview di browser
        ]);
    }
}
