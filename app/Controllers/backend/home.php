<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\BukuModel;

class Home extends BaseController
{
    public function index()
    {
        // fetch up to 3 newest buku with kategori name
        $bukuModel = new BukuModel();
        $bukus = $bukuModel->select('bukus.*, kategori.jenis as kategori')
                           ->join('kategori', 'kategori.id_kategori = bukus.id_kategori', 'left')
                           ->orderBy('id_buku', 'DESC')
                           ->limit(3)
                           ->findAll();

        return view('backend/home', ['bukus' => $bukus]);
    }
}
