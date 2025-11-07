<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\BukuModel; // Tambahkan ini
use App\Models\PinjamModel; // Tambahkan ini

class PinjamanController extends BaseController
{
    public function index()
    {
        $pinjamModel = new PinjamModel();
        // Order by insertion (newest first). We don't have automatic timestamps, so use primary key id_pinjam DESC
        $peminjamans = $pinjamModel
            ->select('pinjams.*, pinjams.nama_siswa as nama_siswa, bukus.judul as judul_buku')
            ->join('bukus', 'bukus.id_buku = pinjams.id_buku', 'left')
            ->where('(pinjams.tgl_selesai IS NULL OR pinjams.tgl_selesai = "" OR pinjams.tgl_selesai = "0000-00-00")', null, false)
            ->orderBy('pinjams.id_pinjam', 'DESC')
            ->findAll();

        // mark overdue entries (denda) so frontend can style them
        $today = new \DateTime('today');
        foreach ($peminjamans as &$row) {
            $tgl_kembali = $row['tgl_kembali'] ?? null;
            $tgl_selesai = $row['tgl_selesai'] ?? null;
            $notReturned = !isset($tgl_selesai) || $tgl_selesai === null || trim($tgl_selesai) === '' || $tgl_selesai === '0000-00-00';
            $isOverdue = false;
            if ($notReturned && !empty($tgl_kembali)) {
                try {
                    $due = new \DateTime($tgl_kembali);
                    if ($due < $today) {
                        $isOverdue = true;
                    }
                } catch (\Exception $e) {
                    $isOverdue = false;
                }
            }
            $row['is_denda'] = $isOverdue ? 1 : 0;
        }

        return view('backend/peminjaman_list', ['peminjamans' => $peminjamans]);
    }

    public function create()
    {
        // Handle borrow submission (expects POST)
        $request = $this->request;
        if (!$request->is('post')) {
            return redirect()->back();
        }

    $buku_id = $request->getPost('buku_id');
    // Trim whitespace from string inputs - cast to string to avoid trim(null) warnings
    $nama_peminjam = trim((string) $request->getPost('nama_peminjam'));
    $kelas = trim((string) $request->getPost('kelas'));
        $tanggal_kembali = $request->getPost('tanggal_kembali');

        // basic validation
        if (empty($buku_id) || empty($nama_peminjam) || empty($kelas) || empty($tanggal_kembali)) {
            return $this->response->setJSON([
                'success' => false, 
                'errors' => [
                    'general' => 'Mohon lengkapi semua field'
                ]
            ]);
        }

        // check date limit: must be within 7 days from today
        $today = new \DateTime('today');
        try {
            $ret = new \DateTime($tanggal_kembali);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'errors' => [
                    'tanggal_kembali' => 'Format tanggal tidak valid'
                ]
            ]);
        }

        $diff = (int)$today->diff($ret)->format('%r%a');
        if ($diff > 7) {
            return $this->response->setJSON([
                'success' => false, 
                'errors' => [
                    'tanggal_kembali' => 'Melebihi batas peminjaman'
                ]
            ]);
        }
        if ($diff < 0) {
            return $this->response->setJSON([
                'success' => false, 
                'errors' => [
                    'tanggal_kembali' => 'Tanggal tidak valid'
                ]
            ]);
        }

        // check stock and decrement
        $bukuModel = new BukuModel(); // Use the imported model
        $buku = $bukuModel->find($buku_id);
        if (!$buku) {
            return $this->response->setJSON([ // This error should ideally not happen if buku_id comes from a valid source
                'success' => false, 
                'errors' => [
                    'buku_id' => 'Buku tidak ditemukan'
                ]
            ]);
        }
        $stok = (int)$buku['stok'];
        if ($stok <= 0) {
            return $this->response->setJSON([
                'success' => false, 
                'errors' => [
                    'buku_id' => 'Stok tidak cukup'
                ]
            ]);
        }

        // insert peminjaman
        $pinjamModel = new PinjamModel(); // Instantiate PinjamModel
        $db = \Config\Database::connect();

        // Note: adapt to DB schema used in your project (fields mapped below).
        // If the DB schema differs, we return a generic error message so UI shows a consistent message.
        $db->transStart();
        try {
            // Map form fields to your DB column names (as per your ER diagram)
            $pinjamData = [
                'id_buku' => $buku_id,
                'nama_siswa' => $nama_peminjam,
                'kelas' => $kelas,
                'tgl_pinjam' => $today->format('Y-m-d'),
                'tgl_kembali' => $ret->format('Y-m-d'),
                // 'tgl_selesai' can be left NULL initially
            ];
            $pinjamModel->insert($pinjamData); // Use model for insertion

            // decrement stok
            $bukuModel->update($buku_id, ['stok' => $stok - 1]);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'gagal menambahkan data'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Peminjaman berhasil'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            // Return a generic message to the frontend per your request
            return $this->response->setJSON([
                'success' => false,
                'message' => 'gagal menambahkan data'
            ]);
        }
    }

    /**
     * Mark a peminjaman as returned (set tgl_selesai = today) and restore buku stock if needed.
     */
    public function return($id = null)
    {
        $request = $this->request;
        if (!$request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (empty($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing id']);
        }

        $pinjamModel = new PinjamModel();
        $bukuModel = new BukuModel();
        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $pinjam = $pinjamModel->find($id);
            if (!$pinjam) {
                $db->transComplete();
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            // If the loan exists, restore stock if it was not already finished
            // treat NULL, empty string, or '0000-00-00' as not-yet-returned
            $notReturned = !isset($pinjam['tgl_selesai']) || $pinjam['tgl_selesai'] === null || trim($pinjam['tgl_selesai']) === '' || $pinjam['tgl_selesai'] === '0000-00-00';
            if ($notReturned) {
                // restore book stock
                $buku = $bukuModel->find($pinjam['id_buku']);
                if ($buku) {
                    $stok = (int)$buku['stok'];
                    $bukuModel->update($buku['id_buku'], ['stok' => $stok + 1]);
                }
            }

            // Update tgl_selesai instead of deleting the record
            $today = date('Y-m-d');
            $pinjamModel->update($id, ['tgl_selesai' => $today]);

            // Insert a row into riwayat table to record the return (if not already present)
            $riwayatModel = new \App\Models\RiwayatModel();
            // check if an entry already exists for this id_pinjam
            $exists = $riwayatModel->where('id_pinjam', $id)->first();
            if (!$exists) {
                $riwayatModel->insert([
                    'id_pinjam' => $id,
                    'status' => 'selesai',
                    'keterangan' => 'Good'
                ]);
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui data']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Peminjaman berhasil dikembalikan']);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }

    /**
     * Delete peminjaman and restore stock if needed.
     */
    public function delete($id = null)
    {
        $request = $this->request;
        if (!$request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (empty($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing id']);
        }

        $pinjamModel = new PinjamModel();
        $bukuModel = new BukuModel();
    $db = \Config\Database::connect();
        $db->transStart();
        try {
            $pinjam = $pinjamModel->find($id);
            if (!$pinjam) {
                $db->transComplete();
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            // if the loan was not marked finished, restore stock
            $notReturned = !isset($pinjam['tgl_selesai']) || $pinjam['tgl_selesai'] === null || trim($pinjam['tgl_selesai']) === '' || $pinjam['tgl_selesai'] === '0000-00-00';
            if ($notReturned) {
                $buku = $bukuModel->find($pinjam['id_buku']);
                if ($buku) {
                    $stok = (int)$buku['stok'];
                    $bukuModel->update($buku['id_buku'], ['stok' => $stok + 1]);
                }
            }

            // delete peminjaman record
            $pinjamModel->delete($id);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Peminjaman dihapus']);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    }
}
