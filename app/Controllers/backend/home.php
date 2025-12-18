<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\BukuModel;
use App\Models\PinjamModel;

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

        // Get top 3 most borrowed books from riwayat table
        $pinjamModel = new PinjamModel();
        $allRiwayat = $pinjamModel
            ->select('bukus.judul, bukus.penulis, COUNT(*) as jml_pinjam')
            ->join('bukus', 'bukus.id_buku = pinjams.id_buku', 'left')
            ->where('pinjams.tgl_selesai IS NOT NULL AND pinjams.tgl_selesai != "" AND pinjams.tgl_selesai != "0000-00-00"', null, false)
            ->groupBy('pinjams.id_buku')
            ->orderBy('jml_pinjam', 'DESC')
            ->limit(3)
            ->findAll();

        return view('backend/home', ['bukus' => $bukus, 'topBooks' => $allRiwayat]);
    }
}
