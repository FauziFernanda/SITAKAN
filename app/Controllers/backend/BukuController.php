<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\BukuModel;
use App\Models\KategoriModel;
use App\Models\GambarModel;
use App\Models\PinjamModel;
use CodeIgniter\HTTP\IncomingRequest;
use Config\Services;

class BukuController extends BaseController
{
    /**
     * Endpoint AJAX untuk Infinite Scroll: /backend/buku/load?page=1&q=search
     */
    public function load()
    {
        $bukuModel = new BukuModel();
        $q = trim($this->request->getGet('q') ?? '');
        $perPage = 12;
        $page = (int) $this->request->getGet('page') ?: 1;
        if ($q !== '') {
            $bukuModel->like('judul', $q);
        }
        $bukus = $bukuModel->orderBy('id_buku', 'DESC')->paginate($perPage, 'bukus', $page);
        $pager = $bukuModel->pager;

        // Attach kategori and images
        $kategoriModel = new KategoriModel();
        $kategoris = $kategoriModel->findAll();
        $katMap = [];
        foreach ($kategoris as $k) {
            $katMap[$k['id_kategori']] = $k['jenis'];
        }
        $gambarModel = new GambarModel();
        foreach ($bukus as &$b) {
            $b['jenis'] = $katMap[$b['id_kategori']] ?? null;
            $gs = $gambarModel->where('id_buku', $b['id_buku'])->orderBy('id_gambar', 'ASC')->findAll();
            $urls = [];
            foreach ($gs as $g) {
                if (!empty($g['url'])) $urls[] = $g['url'];
            }
            $b['images'] = $urls;
            $b['url'] = $urls[0] ?? null;
        }

        return $this->response->setJSON([
            'bukus' => $bukus,
            'pager' => [
                'currentPage' => $pager->getCurrentPage('bukus'),
                'totalPages' => $pager->getPageCount('bukus'),
                'hasNext' => $pager->getCurrentPage('bukus') < $pager->getPageCount('bukus'),
            ]
        ]);
    }
    public function index()
    {
        $bukuModel = new BukuModel();

        // search query (partial match on judul)
        $q = trim($this->request->getGet('q') ?? '');
        if ($q !== '') {
            $bukuModel->like('judul', $q);
        }

        // get books ordered newest first
        $books = $bukuModel->orderBy('id_buku', 'DESC')->findAll();

        // load kategori map and gambar model to attach one image per book
        $kategoriModel = new KategoriModel();
        $kategoris = $kategoriModel->findAll();
        $katMap = [];
        foreach ($kategoris as $k) {
            $katMap[$k['id_kategori']] = $k['jenis'];
        }

        $gambarModel = new GambarModel();
        // attach kategori name and all image urls to each book
        foreach ($books as &$b) {
            $b['jenis'] = $katMap[$b['id_kategori']] ?? null;
            $gs = $gambarModel->where('id_buku', $b['id_buku'])->orderBy('id_gambar', 'ASC')->findAll();
            $urls = [];
            foreach ($gs as $g) {
                if (!empty($g['url'])) $urls[] = $g['url'];
            }
            $b['images'] = $urls;
            $b['url'] = $urls[0] ?? null;
        }

        $data['bukus'] = $books;
        $data['kategoris'] = $kategoris;
        $data['q'] = $q;

        return view('backend/buku_list', $data);
    }

    /**
     * Create a new buku with 3 images. Expects multipart/form-data.
     */
    public function create()
    {
        // Quick DB connection check to return a helpful JSON error when DB is unreachable or misconfigured
        try {
            $dbCheck = \Config\Database::connect();
        } catch (\Throwable $e) {
            // Return JSON so the frontend can show a clear message instead of failing silently
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Koneksi database gagal: ' . $e->getMessage()
            ]);
        }
        // validate basic fields
        $post = $this->request->getPost();
        $judul = trim($post['judul'] ?? '');
        $id_kategori = $post['id_kategori'] ?? null;
        $penulis = trim($post['penulis'] ?? '');
        $penerbit = trim($post['penerbit'] ?? '');
        $tahun_terbit = trim($post['tahun_terbit'] ?? '');
        $stok = (int) ($post['stok'] ?? 0);

        if ($judul === '' || !$id_kategori || $penulis === '' || $penerbit === '' || $tahun_terbit === '' || $stok <= 0) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Semua field wajib diisi dan stok harus > 0.']);
        }

        // require three files from multi-file input 'images[]'
        $allFiles = $this->request->getFiles();
        $images = [];
        if (isset($allFiles['images']) && is_array($allFiles['images'])) {
            $images = $allFiles['images'];
        }

        if (count($images) < 3) {
            $kurang = 3 - count($images);
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Gambar Kurang : butuh ' . $kurang . ' gambar lagi']);
        }

        // basic file validation for the first 3 files
        $files = array_slice($images, 0, 3);
        foreach ($files as $f) {
            if (!$f->isValid() || $f->getError() !== UPLOAD_ERR_OK) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Salah satu file tidak valid.']);
            }
        }

        $bukuModel = new BukuModel();
        $gambarModel = new GambarModel();

        // check duplicate: same judul, penulis and penerbit
        $exists = $bukuModel->where('judul', $judul)
            ->where('penulis', $penulis)
            ->where('penerbit', $penerbit)
            ->first();
        if ($exists) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Data sudah ada']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $bukuData = [
                'id_kategori' => $id_kategori,
                'judul' => $judul,
                'penulis' => $penulis,
                'penerbit' => $penerbit,
                'tahun_terbit' => $tahun_terbit,
                'stok' => $stok,
            ];

            $insertId = $bukuModel->insert($bukuData);
            if ($insertId === false) {
                throw new \Exception('Gagal menyimpan buku');
            }

            // move files and create gambar records
            $savedFiles = [];
            $uploadPath = FCPATH . 'uploads';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            foreach ($files as $f) {
                $newName = $f->getRandomName();
                // move to public/uploads
                $f->move($uploadPath, $newName);
                $savedFiles[] = $newName;
                $gambarModel->insert(['id_buku' => $insertId, 'url' => $newName]);
            }

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new \Exception('Transaksi gagal');
            }

            return $this->response->setStatusCode(201)->setJSON(['success' => true, 'message' => 'Buku berhasil ditambahkan.']);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Buku create error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Server error saat menyimpan buku.']);
        }
    }

    /**
     * Update existing buku. Accepts multipart/form-data. If images[] provided (3 files), replace images.
     */
    public function update($id = null)
    {
        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'ID buku tidak diberikan.']);
        }

        try {
            $dbCheck = \Config\Database::connect();
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Koneksi database gagal: ' . $e->getMessage()]);
        }

        $post = $this->request->getPost();
        $judul = trim($post['judul'] ?? '');
        $id_kategori = $post['id_kategori'] ?? null;
        $penulis = trim($post['penulis'] ?? '');
        $penerbit = trim($post['penerbit'] ?? '');
        $tahun_terbit = trim($post['tahun_terbit'] ?? '');
        $stok = (int) ($post['stok'] ?? 0);

        if ($judul === '' || !$id_kategori || $penulis === '' || $penerbit === '' || $tahun_terbit === '' || $stok < 0) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Semua field wajib diisi dan stok >= 0.']);
        }

        $bukuModel = new BukuModel();
        $gambarModel = new GambarModel();

        $existing = $bukuModel->find($id);
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Buku tidak ditemukan.']);
        }

        // check duplicate (exclude current id)
        $exists = $bukuModel->where('judul', $judul)
            ->where('penulis', $penulis)
            ->where('penerbit', $penerbit)
            ->where('id_buku !=', $id)
            ->first();
        if ($exists) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Data sudah ada untuk buku lain.']);
        }

        $allFiles = $this->request->getFiles();
        $images = [];
        if (isset($allFiles['images']) && is_array($allFiles['images'])) {
            // Filter out empty/no-file uploads (UPLOAD_ERR_NO_FILE)
            foreach ($allFiles['images'] as $f) {
                // If file has no upload (user didn't choose), skip it
                try {
                    $err = $f->getError();
                } catch (\Throwable $e) {
                    $err = UPLOAD_ERR_NO_FILE;
                }
                if ($err === UPLOAD_ERR_NO_FILE) continue;
                $images[] = $f;
            }
        }

        // if user actually uploaded files, require exactly 3 (or at least 3)
        if (count($images) > 0 && count($images) < 3) {
            $kurang = 3 - count($images);
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Gambar Kurang : butuh ' . $kurang . ' gambar lagi']);
        }

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $updateData = [
                'id_kategori' => $id_kategori,
                'judul' => $judul,
                'penulis' => $penulis,
                'penerbit' => $penerbit,
                'tahun_terbit' => $tahun_terbit,
                'stok' => $stok,
            ];

            $ok = $bukuModel->update($id, $updateData);
            if ($ok === false) {
                throw new \Exception('Gagal mengupdate buku');
            }

            // if new images provided, replace existing ones
            if (count($images) > 0) {
                // validate files
                $files = array_slice($images, 0, 3);
                foreach ($files as $f) {
                    if (!$f->isValid() || $f->getError() !== UPLOAD_ERR_OK) {
                        throw new \Exception('Salah satu file tidak valid.');
                    }
                }

                // delete existing gambar records and files
                $gambars = $gambarModel->where('id_buku', $id)->findAll();
                foreach ($gambars as $g) {
                    $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . ($g['url'] ?? '');
                    if (!empty($g['url']) && file_exists($path)) {
                        @unlink($path);
                    }
                }
                $gambarModel->where('id_buku', $id)->delete();

                // move and insert new files
                $uploadPath = FCPATH . 'uploads';
                if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);
                foreach ($files as $f) {
                    $newName = $f->getRandomName();
                    $f->move($uploadPath, $newName);
                    $gambarModel->insert(['id_buku' => $id, 'url' => $newName]);
                }
            }

            $db->transComplete();
            if (!$db->transStatus()) {
                throw new \Exception('Transaksi gagal');
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Buku berhasil diupdate.']);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Buku update error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Server error saat mengupdate buku.']);
        }
    }

    /**
     * Delete buku and its images (POST)
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back();
        }
        
        // prevent deletion when there are active peminjaman for this book
        $pinjamModel = new PinjamModel();
        $bookId = (int) $id;
        $active = $pinjamModel
            ->where('id_buku', $bookId)
            ->where("(tgl_selesai IS NULL OR tgl_selesai = '' OR tgl_selesai = '0000-00-00')", null, false)
            ->first();

        if ($active) {
            // If AJAX request, return JSON with appropriate message
            if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return $this->response->setStatusCode(409)->setJSON(['success' => false, 'message' => 'Buku dalam peminjaman']);
            }
            // otherwise redirect back with flash message
            return redirect()->back()->with('error', 'Buku dalam peminjaman');
        }

        $bukuModel = new BukuModel();
        $gambarModel = new GambarModel();

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            // delete gambar files
            $gambars = $gambarModel->where('id_buku', $id)->findAll();
            foreach ($gambars as $g) {
                $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . ($g['url'] ?? '');
                if (!empty($g['url']) && file_exists($path)) {
                    @unlink($path);
                }
            }

            // delete gambar records
            $gambarModel->where('id_buku', $id)->delete();

            // delete buku
            $bukuModel->delete($id);

            $db->transComplete();
            if (!$db->transStatus()) {
                throw new \Exception('Transaksi gagal saat menghapus');
            }

            // if request is AJAX/JSON return JSON, else redirect back with flash
            if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return $this->response->setJSON(['success' => true, 'message' => 'Buku berhasil dihapus.']);
            }

            return redirect()->back()->with('success', 'Buku berhasil dihapus.');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Buku delete error: ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal menghapus buku.']);
            }
            return redirect()->back()->with('error', 'Gagal menghapus buku.');
        }
    }
}
