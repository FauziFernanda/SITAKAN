<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\BukuModel;

class ViewBukuController extends BaseController
{
    public function index()
    {
        $bukuModel = new BukuModel();

        // optionally support simple query param search ?q=...
        $q = $this->request->getGet('q');
        if ($q) {
            // basic search on judul, penulis, penerbit
            $bukus = $bukuModel->like('judul', $q)
                ->orLike('penulis', $q)
                ->orLike('penerbit', $q)
                ->orderBy('id_buku', 'DESC')
                ->findAll();
        } else {
            $bukus = $bukuModel->orderBy('id_buku', 'DESC')->findAll();
        }

        return view('frontend/list_buku', ['bukus' => $bukus, 'q' => $q]);
    }
}
