<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PinjamModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class RiwayatController extends BaseController
{
    public function pdf()
    {
        $pinjamModel = new PinjamModel();
        
        // Get all completed loans
        $riwayats = $pinjamModel
            ->select('pinjams.*, bukus.judul as judul_buku')
            ->join('bukus', 'bukus.id_buku = pinjams.id_buku', 'left')
            ->where('pinjams.tgl_selesai IS NOT NULL')
            ->orderBy('pinjams.tgl_selesai', 'DESC')
            ->findAll();

        // Format today's date in Indonesian
        $timestamp = time();
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $day = $days[date('w', $timestamp)];
        $date = date('j', $timestamp);
        $month = $months[date('n', $timestamp) - 1];
        $year = date('Y', $timestamp);
        
        $tanggal = "$day, $date $month $year";

        // Load view into variable
        $html = view('backend/riwayat_pdf', [
            'riwayats' => $riwayats,
            'tanggal' => $tanggal
        ]);

        // Create PDF using Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream PDF to browser with download prompt
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment;filename="Laporan_Riwayat_Peminjaman.pdf"')
            ->setBody($dompdf->output());
    }

    public function index()
    {
        $pinjamModel = new PinjamModel();
        
        // Hanya ambil peminjaman yang sudah selesai (tgl_selesai terisi)
        $riwayats = $pinjamModel
            ->select('pinjams.*, bukus.judul as judul_buku')
            ->join('bukus', 'bukus.id_buku = pinjams.id_buku', 'left')
            ->where('pinjams.tgl_selesai IS NOT NULL')
            ->where('pinjams.tgl_selesai !=', '0000-00-00')
            ->orderBy('pinjams.tgl_selesai', 'DESC')
            ->findAll();

        // Group by tgl_selesai
        $groupedRiwayats = [];
        foreach ($riwayats as $riwayat) {
            $tglSelesai = $riwayat['tgl_selesai'];
            $dateKey = date('Y-m-d', strtotime($tglSelesai)); // For sorting
            $dateLabel = $this->formatIndonesianDate($tglSelesai); // For display

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