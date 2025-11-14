<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\RiwayatModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class RiwayatController extends BaseController
{
    public function pdf()
    {
        $riwayatModel = new RiwayatModel();
        
        // Get all riwayat data
        $riwayats = $riwayatModel
            ->orderBy('tgl_selesai', 'DESC')
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
        $riwayatModel = new RiwayatModel();
        
        // Get all riwayat data
        $riwayats = $riwayatModel
            ->orderBy('tgl_selesai', 'DESC')
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

        // Debug data sebelum dikirim ke view
        foreach ($riwayats as $r) {
            log_message('debug', 'Data riwayat: ' . json_encode($r));
        }

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
        // Debug print
        log_message('debug', 'Request ID: ' . $id);
        log_message('debug', 'Request Method: ' . $this->request->getMethod());
        log_message('debug', 'Raw Request Body: ' . json_encode($this->request->getRawInput()));
        
        if (empty($id)) {
            log_message('error', 'Invalid ID received');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID riwayat tidak valid'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $riwayatModel = new RiwayatModel();
            $riwayat = $riwayatModel->find($id);

            if (!$riwayat) {
                log_message('error', 'Riwayat not found with ID: ' . $id);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data riwayat tidak ditemukan'
                ]);
            }

            // Debug log
            log_message('debug', 'Found riwayat: ' . json_encode($riwayat));

            // Delete the record
            $result = $riwayatModel->delete($id);
            
            if ($result) {
                $db->transCommit();
                log_message('info', 'Successfully deleted riwayat with ID: ' . $id);
                session()->setFlashdata('success', 'Data berhasil dihapus');
                return redirect()->back();
            } else {
                $db->transRollback();
                log_message('error', 'Failed to delete riwayat with ID: ' . $id);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus data'
                ]);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Exception when deleting: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ]);
        }
    }
}