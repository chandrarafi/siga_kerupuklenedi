<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JabatanModel;
use App\Models\BagianModel;
use CodeIgniter\HTTP\ResponseInterface;

class Jabatan extends BaseController
{
    protected $jabatanModel;
    protected $bagianModel;

    public function __construct()
    {
        $this->jabatanModel = new JabatanModel();
        $this->bagianModel = new BagianModel();
    }

    public function index()
    {
        return view('admin/jabatan/index', [
            'title' => 'Data Jabatan',
        ]);
    }

    public function getAll()
    {
        $request = $this->request;
        $postData = $request->getPost();

        $dtpostData = $postData;
        $response = array();

        ## Read value
        $draw = $dtpostData['draw'];
        $start = $dtpostData['start'];
        $rowperpage = $dtpostData['length']; // Rows display per page
        $columnIndex = $dtpostData['order'][0]['column']; // Column index
        $columnName = $dtpostData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $dtpostData['order'][0]['dir']; // asc or desc
        $searchValue = $dtpostData['search']['value']; // Search value

        ## Custom query for getting data with join
        $builder = $this->jabatanModel->db->table('jabatan');
        $builder->select('jabatan.*, bagian.namabagian');
        $builder->join('bagian', 'bagian.idbagian = jabatan.bagianid');

        ## Total number of records without filtering
        $totalRecords = $builder->countAllResults(false);

        ## Total number of records with filtering
        if ($searchValue != '') {
            $builder->groupStart()
                ->like('jabatan.namajabatan', $searchValue)
                ->orLike('bagian.namabagian', $searchValue)
                ->groupEnd();
        }
        $totalRecordwithFilter = $builder->countAllResults(false);

        ## Fetch records
        if ($columnName === 'namabagian') {
            $builder->orderBy($columnName, $columnSortOrder);
        } else {
            $builder->orderBy('jabatan.' . $columnName, $columnSortOrder);
        }

        $records = $builder->limit($rowperpage, $start)->get()->getResultArray();

        $data = array();

        foreach ($records as $record) {
            $formattedGaji = 'Rp ' . number_format($record['gajipokok'], 0, ',', '.');
            $formattedTunjangan = 'Rp ' . number_format($record['tunjangan'], 0, ',', '.');

            $data[] = array(
                "idjabatan" => $record['idjabatan'],
                "namabagian" => $record['namabagian'],
                "namajabatan" => $record['namajabatan'],
                "gajipokok" => $formattedGaji,
                "tunjangan" => $formattedTunjangan,
                "actions" => '<button type="button" class="btn btn-sm btn-warning edit-jabatan" data-id="' . $record['idjabatan'] . '"><i class="bi bi-pencil"></i></button> 
                             <button type="button" class="btn btn-sm btn-danger delete-jabatan" data-id="' . $record['idjabatan'] . '"><i class="bi bi-trash"></i></button>'
            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordwithFilter,
            "data" => $data,
            "token" => csrf_hash()
        );

        return $this->response->setJSON($response);
    }

    public function getBagian()
    {
        $bagian = $this->bagianModel->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data' => $bagian,
            'token' => csrf_hash()
        ]);
    }

    public function store()
    {
        $gajipokok = $this->request->getPost('gajipokok');
        $tunjangan = $this->request->getPost('tunjangan');

        // Pastikan nilai adalah numerik
        $gajipokok = is_numeric($gajipokok) ? $gajipokok : 0;
        $tunjangan = is_numeric($tunjangan) ? $tunjangan : 0;

        $data = [
            'bagianid' => $this->request->getPost('bagianid'),
            'namajabatan' => $this->request->getPost('namajabatan'),
            'gajipokok' => (int) $gajipokok,
            'tunjangan' => (int) $tunjangan,
        ];

        if ($this->jabatanModel->save($data)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data jabatan berhasil disimpan',
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data jabatan gagal disimpan',
                'errors' => $this->jabatanModel->errors(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function edit($id)
    {
        $jabatan = $this->jabatanModel->find($id);

        if ($jabatan) {
            return $this->response->setJSON([
                'status' => true,
                'data' => $jabatan,
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data jabatan tidak ditemukan',
                'token' => csrf_hash()
            ]);
        }
    }

    public function update($id)
    {
        $gajipokok = $this->request->getPost('gajipokok');
        $tunjangan = $this->request->getPost('tunjangan');

        // Pastikan nilai adalah numerik
        $gajipokok = is_numeric($gajipokok) ? $gajipokok : 0;
        $tunjangan = is_numeric($tunjangan) ? $tunjangan : 0;

        $data = [
            'idjabatan' => $id,
            'bagianid' => $this->request->getPost('bagianid'),
            'namajabatan' => $this->request->getPost('namajabatan'),
            'gajipokok' => (int) $gajipokok,
            'tunjangan' => (int) $tunjangan,
        ];

        if ($this->jabatanModel->save($data)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data jabatan berhasil diperbarui',
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data jabatan gagal diperbarui',
                'errors' => $this->jabatanModel->errors(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function delete($id)
    {
        if ($this->jabatanModel->delete($id)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data jabatan berhasil dihapus',
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data jabatan gagal dihapus',
                'token' => csrf_hash()
            ]);
        }
    }

    public function report()
    {
        // Get filter parameters
        $bagianId = $this->request->getGet('bagian');
        $isAjax = $this->request->getGet('ajax');

        // Get all bagian for filter dropdown
        $bagianList = $this->bagianModel->findAll();

        // Get all jabatan data with join to bagian
        $builder = $this->jabatanModel->db->table('jabatan');
        $builder->select('jabatan.*, bagian.namabagian');
        $builder->join('bagian', 'bagian.idbagian = jabatan.bagianid');

        // Apply filters if provided
        if (!empty($bagianId)) {
            $builder->where('bagian.idbagian', $bagianId);
        }

        $builder->orderBy('jabatan.namajabatan', 'ASC');
        $jabatan = $builder->get()->getResultArray();

        // Get filter name for display
        $bagian_name = '';

        if (!empty($bagianId)) {
            foreach ($bagianList as $bagian) {
                if ($bagian['idbagian'] == $bagianId) {
                    $bagian_name = $bagian['namabagian'];
                    break;
                }
            }
        }

        $data = [
            'title' => 'Laporan Data Jabatan',
            'jabatan' => $jabatan,
            'bagianList' => $bagianList,
            'filters' => [
                'bagian' => $bagianId
            ],
            'bagian_name' => $bagian_name
        ];

        // Jika request untuk cetak PDF
        if ($this->request->getGet('print') == 'true') {
            return $this->generatePDF($data);
        }

        // Jika request AJAX, kembalikan hanya bagian laporan
        if ($isAjax) {
            return view('admin/jabatan/report_partial', $data);
        }

        // Jika tidak, tampilkan dengan layout admin
        return view('admin/jabatan/report_preview', $data);
    }

    /**
     * Generate PDF report
     */
    protected function generatePDF($data)
    {
        // Tambahkan path logo
        $data['logo'] = ROOTPATH . 'public/image/logo.png';

        // Jika logo tidak ada, gunakan placeholder
        if (!file_exists($data['logo'])) {
            $data['logo'] = '';
        } else {
            // Convert logo to base64 for embedding in PDF
            $data['logo'] = 'data:image/png;base64,' . base64_encode(file_get_contents($data['logo']));
        }

        // Load PDF helper
        $pdfHelper = new \App\Helpers\PdfHelper();

        // Generate PDF
        $html = view('admin/jabatan/pdf_template', $data);

        // Filename dengan timestamp
        $filename = 'laporan_jabatan_' . date('Ymd_His') . '.pdf';

        // Generate PDF
        return $pdfHelper->generate($html, $filename, 'A4', 'portrait', [
            'attachment' => false // true untuk download, false untuk preview di browser
        ]);
    }
}
