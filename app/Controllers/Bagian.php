<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BagianModel;
use CodeIgniter\HTTP\ResponseInterface;

class Bagian extends BaseController
{
    protected $bagianModel;

    public function __construct()
    {
        $this->bagianModel = new BagianModel();
    }

    public function index()
    {
        return view('admin/bagian/index', [
            'title' => 'Data Bagian',
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

        ## Total number of records without filtering
        $totalRecords = $this->bagianModel->countAll();

        ## Total number of records with filtering
        $totalRecordwithFilter = $this->bagianModel->like('namabagian', $searchValue)->countAllResults();

        ## Fetch records
        $records = $this->bagianModel->select('*')
            ->like('namabagian', $searchValue)
            ->orderBy($columnName, $columnSortOrder)
            ->findAll($rowperpage, $start);

        $data = array();

        foreach ($records as $record) {
            $data[] = array(
                "idbagian" => $record['idbagian'],
                "namabagian" => $record['namabagian'],
                "actions" => '<button type="button" class="btn btn-sm btn-warning edit-bagian" data-id="' . $record['idbagian'] . '"><i class="bi bi-pencil"></i></button> 
                             <button type="button" class="btn btn-sm btn-danger delete-bagian" data-id="' . $record['idbagian'] . '"><i class="bi bi-trash"></i></button>'
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

    public function store()
    {
        $data = [
            'namabagian' => $this->request->getPost('namabagian')
        ];

        if ($this->bagianModel->save($data)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data bagian berhasil disimpan',
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data bagian gagal disimpan',
                'errors' => $this->bagianModel->errors(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function edit($id)
    {
        $bagian = $this->bagianModel->find($id);

        if ($bagian) {
            return $this->response->setJSON([
                'status' => true,
                'data' => $bagian,
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data bagian tidak ditemukan',
                'token' => csrf_hash()
            ]);
        }
    }

    public function update($id)
    {
        $data = [
            'idbagian' => $id,
            'namabagian' => $this->request->getPost('namabagian')
        ];

        if ($this->bagianModel->save($data)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data bagian berhasil diperbarui',
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data bagian gagal diperbarui',
                'errors' => $this->bagianModel->errors(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function delete($id)
    {
        if ($this->bagianModel->delete($id)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data bagian berhasil dihapus',
                'token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data bagian gagal dihapus',
                'token' => csrf_hash()
            ]);
        }
    }
}
