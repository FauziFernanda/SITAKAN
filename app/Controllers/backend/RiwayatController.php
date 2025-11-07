<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PinjamModel;

class RiwayatController extends BaseController
{
    public function index()
    {
        $pinjamModel = new PinjamModel();
        
        // Get completed loans
        $riwayats = $pinjamModel
            ->select('pinjams.*, bukus.judul as judul_buku')
            ->join('bukus', 'bukus.id_buku = pinjams.id_buku', 'left')
            ->where('pinjams.tgl_selesai IS NOT NULL')
            ->orderBy('pinjams.tgl_kembali', 'DESC')
            ->findAll();

        // Group by tgl_kembali with proper date formatting
        $groupedRiwayats = [];
        foreach ($riwayats as $riwayat) {
            $tglKembali = $riwayat['tgl_kembali'];
            $dateKey = date('Y-m-d', strtotime($tglKembali)); // For sorting
            $dateLabel = $this->formatIndonesianDate($tglKembali); // For display
            
            if (!isset($groupedRiwayats[$dateKey])) {
                $groupedRiwayats[$dateKey] = [
                    'label' => $dateLabel,
                    'items' => []
                ];
            }
            $groupedRiwayats[$dateKey]['items'][] = $riwayat;
        }
        
        // Sort by date descending
        krsort($groupedRiwayats);

        // Return the grouped data to the view
        return view('backend/riwayat_list', [
            'groupedRiwayats' => $groupedRiwayats
        ]);
    }

    private function formatIndonesianDate($date)
    {
        $timestamp = strtotime($date);
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $months = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $day = $days[date('w', $timestamp)];
        $date = date('j', $timestamp);
        $month = $months[date('n', $timestamp) - 1];
        $year = date('Y', $timestamp);

        return "$day, $date $month $year";
    }

    public function delete($id = null)
    {
        if (!$this->request->isAJAX() || empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $pinjamModel = new PinjamModel();
        $db = \Config\Database::connect();
        
        $db->transStart();
        try {
            // Simply delete the record
            $result = $pinjamModel->delete($id);
            
            $db->transComplete();
            
            if ($result === false || $db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }
}