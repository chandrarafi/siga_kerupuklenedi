<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $title = 'Dashboard';
        return view('admin/dashboard', compact('title'));
    }


    public function users()
    {
        $title = 'User Management';
        return view('admin/users/index', compact('title'));
    }

    public function getUsers()
    {
        $request = $this->request;


        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $search = $request->getGet('search')['value'] ?? '';
        $order = $request->getGet('order') ?? [];
        $roleFilter = $request->getGet('role') ?? '';
        $statusFilter = $request->getGet('status') ?? '';

        $orderColumn = $order[0]['column'] ?? 0;
        $orderDir = $order[0]['dir'] ?? 'asc';


        $columns = ['id', 'username', 'email', 'name', 'role', 'status', 'last_login'];
        $orderBy = $columns[$orderColumn] ?? 'id';


        $builder = $this->userModel->builder();


        if (!empty($search)) {
            $builder->groupStart()
                ->like('username', $search)
                ->orLike('email', $search)
                ->orLike('name', $search)
                ->orLike('role', $search)
                ->groupEnd();
        }


        if (!empty($roleFilter)) {
            $builder->where('role', $roleFilter);
        }


        if (!empty($statusFilter)) {
            $builder->where('status', $statusFilter);
        }


        $totalRecords = $this->userModel->countAll();


        $filteredRecords = $builder->countAllResults(false);


        $data = $builder->orderBy($orderBy, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();


        $response = [
            'draw' => $request->getGet('draw') ?? 1,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }

    protected function handleUserSave($data, $isNew = true)
    {
        if ($this->userModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $isNew ? 'User berhasil ditambahkan' : 'User berhasil diperbarui'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status' => 'error',
            'message' => $isNew ? 'Gagal menambahkan user' : 'Gagal memperbarui user',
            'errors' => $this->userModel->errors()
        ]);
    }

    public function addUser()
    {
        return $this->handleUserSave($this->request->getPost(), true);
    }

    public function createUser()
    {
        return $this->handleUserSave($this->request->getJSON(true), true);
    }

    public function getUser($id = null)
    {
        $data = $this->userModel->find($id);

        if ($data) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
        }

        return $this->response->setStatusCode(404)->setJSON([
            'status' => 'error',
            'message' => 'User tidak ditemukan'
        ]);
    }

    public function updateUser($id = null)
    {
        $data = $this->request->getPost();


        if (!empty($id)) {
            $data['id'] = $id;
        } elseif (!empty($data['id'])) {
            $id = $data['id'];
        }


        if (empty($id)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'ID user tidak valid',
                'errors' => ['id' => 'ID user tidak ditemukan']
            ]);
        }


        $existingUser = $this->userModel->find($id);
        if (!$existingUser) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'User tidak ditemukan',
                'errors' => ['id' => 'User dengan ID tersebut tidak ditemukan']
            ]);
        }

        return $this->handleUserSave($data, false);
    }

    public function deleteUser($id = null)
    {
        if ($this->userModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status' => 'error',
            'message' => 'Gagal menghapus user'
        ]);
    }

    public function getRoles()
    {

        $roles = ['admin', 'pimpinan', 'pegawai'];

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $roles
        ]);
    }
}
