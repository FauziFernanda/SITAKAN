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

        // Get images for each book and normalize URLs (add full URL)
        foreach ($bukus as &$buku) {
            $images = $this->gambarModel->where('id_buku', $buku['id_buku'])->findAll();
            // Normalize image rows: add 'full_url' for direct use in views/JS
            $normalized = [];
            foreach ($images as $img) {
                $url = isset($img['url']) ? $img['url'] : '';
                // trim whitespace
                $url = trim($url);
                // if url already absolute (http/https) leave it, otherwise build full url
                if (preg_match('#^https?://#i', $url)) {
                    $img['full_url'] = $url;
                } elseif ($url !== '') {
                    // If the stored value already contains uploads/ prefix, use it as-is
                    if (preg_match('#(^|/)uploads/#i', $url)) {
                        $img['full_url'] = rtrim(base_url(), '/') . '/' . ltrim($url, '/');
                    } else {
                        // most records store only filename, so prefix with uploads/
                        $img['full_url'] = rtrim(base_url(), '/') . '/uploads/' . ltrim($url, '/');
                    }
                } else {
                    $img['full_url'] = '';
                }
                $normalized[] = $img;
            }

            $buku['gambar'] = $normalized;
            // Set the cover image path (raw) and full URL to the first image if available
            if (!empty($normalized) && !empty($normalized[0]['url'])) {
                $buku['cover_url'] = $normalized[0]['url'];
                $buku['cover_url_full'] = $normalized[0]['full_url'];
            }
            // Ensure category key is available as 'kategori' for frontend JS
            if (isset($buku['kategori_nama']) && !isset($buku['kategori'])) {
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
