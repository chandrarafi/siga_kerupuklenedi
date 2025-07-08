<?php

namespace App\Helpers;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfHelper
{
    protected $dompdf;
    protected $options;

    public function __construct()
    {
        $this->options = new Options();
        $this->options->set('isHtml5ParserEnabled', true);
        $this->options->set('isRemoteEnabled', true);
        $this->options->set('defaultFont', 'Arial');
        $this->options->set('isFontSubsettingEnabled', true);
        $this->options->set('isPhpEnabled', true);

        $this->dompdf = new Dompdf($this->options);
    }

    /**
     * Generate PDF from HTML
     *
     * @param string $html HTML content
     * @param string $filename Filename for the PDF
     * @param string $paperSize Paper size (A4, Letter, etc)
     * @param string $orientation Page orientation (portrait or landscape)
     * @param array $options Additional options
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function generate($html, $filename, $paperSize = 'A4', $orientation = 'portrait', $options = [])
    {
        // Set default options
        $defaultOptions = [
            'attachment' => false // false untuk preview di browser, true untuk download
        ];

        // Merge options
        $options = array_merge($defaultOptions, $options);

        // Inisialisasi Dompdf
        $dompdfOptions = new \Dompdf\Options();
        $dompdfOptions->set('isRemoteEnabled', true);
        $dompdfOptions->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($dompdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paperSize, $orientation);
        $dompdf->render();

        // Output PDF
        $dompdf->stream($filename, [
            'Attachment' => $options['attachment']
        ]);
        exit();
    }

    /**
     * Get PDF as string
     *
     * @param string $html HTML content
     * @param string $paper Paper size (default: A4)
     * @param string $orientation Paper orientation (default: portrait)
     * @return string
     */
    public function output($html, $paper = 'A4', $orientation = 'portrait')
    {
        // Set paper size and orientation
        $this->dompdf->setPaper($paper, $orientation);

        // Load HTML content
        $this->dompdf->loadHtml($html);

        // Render PDF (generate)
        $this->dompdf->render();

        // Return PDF as string
        return $this->dompdf->output();
    }
}
