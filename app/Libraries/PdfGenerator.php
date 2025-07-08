<?php

namespace App\Libraries;

use TCPDF;

class PdfGenerator
{
    protected $tcpdf;

    public function __construct()
    {
        // Membuat instance TCPDF
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $this->tcpdf->SetCreator('Kerupuk Len Edi');
        $this->tcpdf->SetAuthor('Kerupuk Len Edi');
        $this->tcpdf->SetTitle('Laporan');

        // Set margins
        $this->tcpdf->SetMargins(15, 15, 15);

        // Set auto page breaks
        $this->tcpdf->SetAutoPageBreak(TRUE, 15);

        // Disable header/footer
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
    }

    public function generatePegawaiReport($data)
    {
        // Add a page
        $this->tcpdf->AddPage();

        // Set font untuk seluruh dokumen
        $this->tcpdf->SetFont('helvetica', '', 10);

        // Header sederhana tanpa background
        // Logo
        $image_file = ROOTPATH . 'public/image/logo.png';
        if (file_exists($image_file)) {
            $this->tcpdf->Image($image_file, 15, 15, 25, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }

        // Header text
        $this->tcpdf->SetFont('helvetica', 'B', 14);
        $this->tcpdf->Cell(0, 7, 'SISTEM INFORMASI KERUPUK LEN EDI', 0, 1, 'C');

        $this->tcpdf->SetFont('helvetica', '', 10);
        $this->tcpdf->Cell(0, 5, 'Pauh Kambar, Kabupaten Padang Pariaman', 0, 1, 'C');
        $this->tcpdf->Cell(0, 5, 'Telp: (0751) 123456 | Email: info@kerupuklenedi.com', 0, 1, 'C');

        // Garis pemisah
        $this->tcpdf->Line(15, 35, 195, 35);

        // Judul
        $this->tcpdf->Ln(5);
        $this->tcpdf->SetFont('helvetica', 'B', 12);
        $this->tcpdf->Cell(0, 10, 'LAPORAN DATA PEGAWAI', 0, 1, 'C');

        // Informasi Filter
        if (!empty($data['filters']['bagian']) || !empty($data['filters']['jabatan']) || !empty($data['filters']['jenkel'])) {
            $this->tcpdf->SetFont('helvetica', '', 10);

            $filterText = '';

            // Filter Bagian
            if (!empty($data['filters']['bagian'])) {
                foreach ($data['bagianList'] as $bagian) {
                    if ($bagian['idbagian'] == $data['filters']['bagian']) {
                        $filterText .= 'Bagian: ' . $bagian['namabagian'] . ' ';
                        break;
                    }
                }
            }

            // Filter Jabatan
            if (!empty($data['filters']['jabatan'])) {
                foreach ($data['jabatanList'] as $jabatan) {
                    if ($jabatan['idjabatan'] == $data['filters']['jabatan']) {
                        $filterText .= '| Jabatan: ' . $jabatan['namajabatan'] . ' ';
                        break;
                    }
                }
            }

            // Filter Jenis Kelamin
            if (!empty($data['filters']['jenkel'])) {
                $filterText .= '| Jenis Kelamin: ' . $data['filters']['jenkel'];
            }

            $this->tcpdf->Cell(0, 5, $filterText, 0, 1, 'C');
            $this->tcpdf->Ln(2);
        } else {
            $this->tcpdf->Ln(2);
        }

        // Tabel sederhana
        // Lebar kolom yang sesuai
        $colWidth = array(10, 35, 45, 30, 30, 25, 25);

        // Header tabel
        $this->tcpdf->SetFillColor(240, 240, 240); // Light gray for header
        $this->tcpdf->SetTextColor(0, 0, 0); // Black text
        $this->tcpdf->SetFont('helvetica', 'B', 9);
        $this->tcpdf->Cell($colWidth[0], 7, 'No', 1, 0, 'C', true);
        $this->tcpdf->Cell($colWidth[1], 7, 'Kode Pegawai', 1, 0, 'C', true);
        $this->tcpdf->Cell($colWidth[2], 7, 'Nama Pegawai', 1, 0, 'C', true);
        $this->tcpdf->Cell($colWidth[3], 7, 'NIK', 1, 0, 'C', true);
        $this->tcpdf->Cell($colWidth[4], 7, 'Jabatan', 1, 0, 'C', true);
        $this->tcpdf->Cell($colWidth[5], 7, 'Bagian', 1, 0, 'C', true);
        $this->tcpdf->Cell($colWidth[6], 7, 'NoHP', 1, 1, 'C', true);

        // Data tabel
        $this->tcpdf->SetFont('helvetica', '', 9);
        $no = 1;

        if (!empty($data['pegawai'])) {
            foreach ($data['pegawai'] as $row) {
                $this->tcpdf->Cell($colWidth[0], 6, $no++, 1, 0, 'C');
                $this->tcpdf->Cell($colWidth[1], 6, $row['idpegawai'], 1, 0, 'C');
                $this->tcpdf->Cell($colWidth[2], 6, $row['namapegawai'], 1, 0, 'L');
                $this->tcpdf->Cell($colWidth[3], 6, $row['nik'] ?? '-', 1, 0, 'C');
                $this->tcpdf->Cell($colWidth[4], 6, $row['namajabatan'], 1, 0, 'L');
                $this->tcpdf->Cell($colWidth[5], 6, $row['namabagian'], 1, 0, 'L');
                $this->tcpdf->Cell($colWidth[6], 6, $row['nohp'] ?? '-', 1, 1, 'C');
            }
        } else {
            $totalWidth = array_sum($colWidth);
            $this->tcpdf->Cell($totalWidth, 6, 'Tidak ada data pegawai', 1, 1, 'C');
        }

        // Footer
        $this->tcpdf->Ln(10);
        $this->tcpdf->SetFont('helvetica', '', 10);
        $this->tcpdf->Cell(120, 5, '', 0, 0);
        $this->tcpdf->Cell(60, 5, 'Padang, ' . date('d-m-Y'), 0, 1, 'R');

        $this->tcpdf->Ln(15);
        $this->tcpdf->SetFont('helvetica', 'B', 10);
        $this->tcpdf->Cell(120, 5, '', 0, 0);
        $this->tcpdf->Cell(60, 5, '( __________________ )', 0, 1, 'R');
        $this->tcpdf->Cell(120, 5, '', 0, 0);
        $this->tcpdf->Cell(60, 5, 'Pimpinan', 0, 1, 'R');

        // Output
        $this->tcpdf->Output('laporan_pegawai_' . date('Ymd') . '.pdf', 'I');
        exit;
    }
}
