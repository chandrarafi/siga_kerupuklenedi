<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OfficeSettingModel;
use App\Models\AbsensiSettingModel;

class Settings extends BaseController
{
    protected $officeSettingModel;
    protected $absensiSettingModel;

    public function __construct()
    {
        $this->officeSettingModel = new OfficeSettingModel();
        $this->absensiSettingModel = new AbsensiSettingModel();
    }

    public function officeLocation()
    {
        $data = [
            'title' => 'Pengaturan Lokasi Kantor',
            'setting' => $this->officeSettingModel->getOfficeSetting()
        ];

        return view('admin/settings/office_location', $data);
    }

    public function saveOfficeLocation()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid request'
            ]);
        }

        $data = json_decode($this->request->getBody(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Format JSON tidak valid: ' . json_last_error_msg()
            ]);
        }


        $rules = [
            'name' => 'required',
            'address' => 'required',
            'latitude' => 'required|decimal',
            'longitude' => 'required|decimal',
            'radius' => 'required|integer|greater_than[0]'
        ];

        if (!$this->validateData($data, $rules)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $this->validator->getErrors())
            ]);
        }

        try {

            $existing = $this->officeSettingModel->first();

            if ($existing) {
                $this->officeSettingModel->update($existing['id'], $data);
            } else {
                $this->officeSettingModel->insert($data);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Pengaturan lokasi kantor berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function absensiSettings()
    {
        $data = [
            'title' => 'Pengaturan Jam Absensi',
            'setting' => $this->absensiSettingModel->first()
        ];

        return view('admin/settings/absensi_settings', $data);
    }

    public function saveAbsensiSettings()
    {
        $rules = [
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'jam_masuk' => $this->request->getPost('jam_masuk'),
            'jam_pulang' => $this->request->getPost('jam_pulang'),
        ];

        $setting = $this->absensiSettingModel->first();

        if ($setting) {
            $this->absensiSettingModel->update($setting['id'], $data);
        } else {
            $this->absensiSettingModel->insert($data);
        }

        session()->setFlashdata('success', 'Pengaturan jam absensi berhasil disimpan');
        return redirect()->to('admin/settings/absensi-settings');
    }
}
