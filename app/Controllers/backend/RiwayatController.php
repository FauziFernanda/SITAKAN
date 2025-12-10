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

        // Respect optional date-range filters passed as GET params. If both
        // start and end are provided we filter by tgl_pinjam between them.
        $tgl_pinjam = trim((string) $this->request->getGet('tgl_pinjam'));
        $tgl_kembali = trim((string) $this->request->getGet('tgl_kembali'));
        $judul = trim((string) $this->request->getGet('judul'));
        $nama = trim((string) $this->request->getGet('nama'));

        $query = $riwayatModel;
        if ($judul !== '') {
            $query = $query->like('judul', $judul);
        }
        if ($nama !== '') {
            $query = $query->like('nama_siswa', $nama);
        }

        if ($tgl_pinjam !== '' && $tgl_kembali !== '') {
            $query = $query->where('tgl_pinjam >=', $tgl_pinjam)
                           ->where('tgl_pinjam <=', $tgl_kembali);
        }

        // Fetch rows (if no filters provided this returns all)
        $riwayats = $query->orderBy('tgl_selesai', 'DESC')->findAll();

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
        $perPage = 15;
        $page = (int) $this->request->getGet('page') ?: 1;
        // Search filters
        $judul = trim((string) $this->request->getGet('judul'));
        $nama = trim((string) $this->request->getGet('nama'));
        $tgl_pinjam = trim((string) $this->request->getGet('tgl_pinjam'));
        $tgl_kembali = trim((string) $this->request->getGet('tgl_kembali'));

        // Build query with optional filters
        $query = $riwayatModel;

        // If only one of the date range inputs is provided, reject and ask user to fill both.
        if ((($tgl_pinjam === '') && ($tgl_kembali !== '')) || (($tgl_pinjam !== '') && ($tgl_kembali === ''))) {
            session()->setFlashdata('error', 'rate tanggal wajib di isi keduanya');
            // Preserve GET parameters when redirecting back so selects remain filled
            $qs = http_build_query($this->request->getGet());
            return redirect()->to(base_url('backend/riwayat') . ($qs ? ('?' . $qs) : ''));
        }
        if ($judul !== '') {
            $query = $query->like('judul', $judul);
        }
        if ($nama !== '') {
            $query = $query->like('nama_siswa', $nama);
        }
        // Interpret the date inputs as a Start (tgl_pinjam) and End (tgl_kembali)
        // and filter records by tgl_pinjam within the provided range (inclusive).
        if ($tgl_pinjam !== '' && $tgl_kembali !== '') {
            // both provided: tgl_pinjam between start and end
            $query = $query->where('tgl_pinjam >=', $tgl_pinjam)
                           ->where('tgl_pinjam <=', $tgl_kembali);
        } elseif ($tgl_pinjam !== '') {
            // only start provided: tgl_pinjam on/after start
            $query = $query->where('tgl_pinjam >=', $tgl_pinjam);
        } elseif ($tgl_kembali !== '') {
            // only end provided: tgl_pinjam on/before end
            $query = $query->where('tgl_pinjam <=', $tgl_kembali);
        }

        $riwayats = $query->orderBy('tgl_selesai', 'DESC')->paginate($perPage, 'riwayats', $page);
        $pager = $riwayatModel->pager;

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
        krsort($groupedRiwayats);

        return view('backend/riwayat_list', [
            'groupedRiwayats' => $groupedRiwayats,
            'pager' => $pager,
            'perPage' => $perPage,
            'page' => $page,
            // echo back search values so view can keep them
            'search' => [
                'judul' => $judul,
                'nama' => $nama,
                'tgl_pinjam' => $tgl_pinjam,
                'tgl_kembali' => $tgl_kembali,
            ],
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