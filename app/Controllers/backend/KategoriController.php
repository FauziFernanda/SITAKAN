<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

class KategoriController extends BaseController
{
    public function create()
    {
        $model = new KategoriModel();

        // accept JSON or form-data
        try {
            $data = $this->request->getJSON(true);
        } catch (\Throwable $e) {
            // getJSON throws when request body isn't valid JSON (e.g. multipart/form-data)
            $data = [];
        }

        if (empty($data)) {
            // fallback to post
            $jenis = $this->request->getPost('nama_kategori');
            $data = ['jenis' => $jenis];
        }

        $jenis = isset($data['nama_kategori']) ? trim($data['nama_kategori']) : (isset($data['jenis']) ? trim($data['jenis']) : '');

        if (empty($jenis)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Nama kategori wajib diisi.']);
        }
        // Check duplicate (case-insensitive)
        try {
            $exists = $model->where('LOWER(jenis)', strtolower($jenis))->first();
        } catch (\Exception $e) {
            // Fallback to simple equality if DB does not support LOWER in where through model
            $exists = $model->where('jenis', $jenis)->first();
        }

        if ($exists) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Kategori Sudah ada']);
        }

        try {
            $insertId = $model->insert(['jenis' => $jenis]);
            if ($insertId === false) {
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal menyimpan kategori.']);
            }

            return $this->response->setStatusCode(201)->setJSON(['success' => true, 'message' => 'Kategori berhasil dibuat.', 'id' => $insertId]);
        } catch (\Exception $e) {
            // log error and return generic message
            log_message('error', 'Kategori create error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Server error saat menyimpan kategori.']);
        }
    }
}
