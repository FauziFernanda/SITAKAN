<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Models\BukuModel;
use App\Models\GambarModel;

class ViewBukuController extends BaseController
{
    public function index()
    {
        $bukuModel = new BukuModel();
        $q = $this->request->getGet('q');
        if ($q) {
            $bukus = $bukuModel->like('judul', $q)
                ->orLike('penulis', $q)
                ->orLike('penerbit', $q)
                ->orderBy('id_buku', 'DESC')
                ->findAll();
        } else {
            $bukus = $bukuModel->orderBy('id_buku', 'DESC')->findAll();
        }

        // Load gambar for the listed bukus (first image per buku)
        $covers = [];
        if (!empty($bukus)) {
            $ids = array_column($bukus, 'id_buku');
            $gambarModel = new GambarModel();
            $gambarRows = $gambarModel->whereIn('id_buku', $ids)->orderBy('id_gambar', 'ASC')->findAll();
            foreach ($gambarRows as $g) {
                $bid = $g['id_buku'];
                // Only set the first image per book
                if (!isset($covers[$bid])) {
                    $url = $g['url'] ?? '';
                    if (empty($url)) {
                        $covers[$bid] = '';
                    } elseif (strpos($url, 'http') === 0 || strpos($url, '/') === 0) {
                        $covers[$bid] = $url;
                    } else {
                        $covers[$bid] = base_url('uploads/' . $url);
                    }
                }
            }
        }

        // Attach cover to each buku row for convenience in view
        foreach ($bukus as &$bk) {
            $bk['cover'] = $covers[$bk['id_buku']] ?? '';
        }

        return view('frontend/list_buku', ['bukus' => $bukus, 'q' => $q]);
    }
}
