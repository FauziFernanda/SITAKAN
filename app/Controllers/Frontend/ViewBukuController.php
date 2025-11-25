<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\BukuModel;
use App\Models\GambarModel;

class ViewBukuController extends BaseController
{
    protected $bukuModel;
    protected $gambarModel;

    public function __construct()
    {
        $this->bukuModel = new BukuModel();
        $this->gambarModel = new GambarModel();
    }

    public function index()
    {
        // optionally support simple query param search ?q=...
        $q = $this->request->getGet('q');
        
        if ($q) {
            // basic search on judul, penulis, penerbit
            $bukus = $this->bukuModel->select('bukus.*, kategori.jenis as kategori_nama')
                ->join('kategori', 'kategori.id_kategori = bukus.id_kategori', 'left')
                ->like('judul', $q)
                ->orLike('penulis', $q)
                ->orLike('penerbit', $q)
                ->orderBy('bukus.id_buku', 'DESC')
                ->findAll();
        } else {
            $bukus = $this->bukuModel->select('bukus.*, kategori.jenis as kategori_nama')
                ->join('kategori', 'kategori.id_kategori = bukus.id_kategori', 'left')
                ->orderBy('bukus.id_buku', 'DESC')
                ->findAll();
        }

        // Prefetch images for all books in a single query to avoid N+1 queries
        $bukusById = [];
        $ids = [];
        foreach ($bukus as $b) {
            $bukusById[$b['id_buku']] = $b;
            $ids[] = $b['id_buku'];
        }

        $imagesGrouped = [];
        if (! empty($ids)) {
            $images = $this->gambarModel->whereIn('id_buku', $ids)->findAll();
            foreach ($images as $img) {
                $imgUrl = isset($img['url']) ? trim($img['url']) : '';
                if (preg_match('#^https?://#i', $imgUrl)) {
                    $img['full_url'] = $imgUrl;
                } elseif ($imgUrl !== '') {
                    if (preg_match('#(^|/)uploads/#i', $imgUrl)) {
                        $img['full_url'] = rtrim(base_url(), '/') . '/' . ltrim($imgUrl, '/');
                    } else {
                        $img['full_url'] = rtrim(base_url(), '/') . '/uploads/' . ltrim($imgUrl, '/');
                    }
                } else {
                    $img['full_url'] = '';
                }

                $imagesGrouped[$img['id_buku']][] = $img;
            }
        }

        // Merge images back into the buku list
        foreach ($bukus as &$buku) {
            $normalized = $imagesGrouped[$buku['id_buku']] ?? [];
            $buku['gambar'] = $normalized;
            if (! empty($normalized) && ! empty($normalized[0]['url'])) {
                $buku['cover_url'] = $normalized[0]['url'];
                $buku['cover_url_full'] = $normalized[0]['full_url'];
            }
            if (isset($buku['kategori_nama']) && ! isset($buku['kategori'])) {
                $buku['kategori'] = $buku['kategori_nama'];
            }
        }

        return view('frontend/list_buku', ['bukus' => $bukus, 'q' => $q]);
    }

    public function detail($id_buku)
    {
        // fetch book with its category name so the frontend receives 'kategori'
        $buku = $this->bukuModel
            ->select('bukus.*, kategori.jenis as kategori')
            ->join('kategori', 'kategori.id_kategori = bukus.id_kategori', 'left')
            ->where('bukus.id_buku', $id_buku)
            ->first();

        if ($buku) {
            $images = $this->gambarModel->where('id_buku', $id_buku)->findAll();
            $normalized = [];
            foreach ($images as $img) {
                $url = isset($img['url']) ? trim($img['url']) : '';
                if (preg_match('#^https?://#i', $url)) {
                    $img['full_url'] = $url;
                } elseif ($url !== '') {
                    if (preg_match('#(^|/)uploads/#i', $url)) {
                        $img['full_url'] = rtrim(base_url(), '/') . '/' . ltrim($url, '/');
                    } else {
                        $img['full_url'] = rtrim(base_url(), '/') . '/uploads/' . ltrim($url, '/');
                    }
                } else {
                    $img['full_url'] = '';
                }
                $normalized[] = $img;
            }
            $buku['gambar'] = $normalized;
            return $this->response->setJSON($buku);
        }
        return $this->response->setStatusCode(404, 'Book not found');
    }
}
