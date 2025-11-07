<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PinjamModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class DendaController extends BaseController
{
    /**
     * Show list of overdue (denda) peminjaman: nama siswa, judul buku, telat (hari), action
     */
    public function index()
    {
        $pinjamModel = new PinjamModel();

        return $this->renderDendaList();
    }

    private function renderDendaList()
    {
        $pinjamModel = new PinjamModel();

        // select only peminjaman that are not returned and past their due date
        $rows = $pinjamModel
            ->select('pinjams.*, bukus.judul as judul_buku')
            ->join('bukus', 'bukus.id_buku = pinjams.id_buku', 'left')
            ->where("(pinjams.tgl_selesai IS NULL OR pinjams.tgl_selesai = '' OR pinjams.tgl_selesai = '0000-00-00')", null, false)
            ->where('pinjams.tgl_kembali <', date('Y-m-d'))
            ->orderBy('pinjams.tgl_kembali', 'ASC')
            ->findAll();

        // compute days late
        $today = new \DateTime('today');
        foreach ($rows as &$r) {
            $r['telat_hari'] = 0;
            if (!empty($r['tgl_kembali'])) {
                try {
                    $due = new \DateTime($r['tgl_kembali']);
                    $diff = $due->diff($today);
                    $r['telat_hari'] = (int)$diff->format('%r%a');
                    if ($r['telat_hari'] < 0) $r['telat_hari'] = 0; // safeguard
                } catch (\Exception $e) {
                    $r['telat_hari'] = 0;
                }
            }
        }

        return view('backend/denda_list', ['dendas' => $rows]);
    }

    public function pdf()
    {
        // Get denda data using the same logic
        $pinjamModel = new PinjamModel();
        
        $rows = $pinjamModel
            ->select('pinjams.*, bukus.judul as judul_buku')
            ->join('bukus', 'bukus.id_buku = pinjams.id_buku', 'left')
            ->where("(pinjams.tgl_selesai IS NULL OR pinjams.tgl_selesai = '' OR pinjams.tgl_selesai = '0000-00-00')", null, false)
            ->where('pinjams.tgl_kembali <', date('Y-m-d'))
            ->orderBy('pinjams.tgl_kembali', 'ASC')
            ->findAll();

        // compute days late and denda
        $today = new \DateTime('today');
        foreach ($rows as &$r) {
            $r['telat_hari'] = 0;
            if (!empty($r['tgl_kembali'])) {
                try {
                    $due = new \DateTime($r['tgl_kembali']);
                    $diff = $due->diff($today);
                    $r['telat_hari'] = (int)$diff->format('%r%a');
                    if ($r['telat_hari'] < 0) $r['telat_hari'] = 0;
                } catch (\Exception $e) {
                    $r['telat_hari'] = 0;
                }
            }
        }

        // Format today's date in Indonesian
        $timestamp = time();
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $day = $days[date('w', $timestamp)];
        $date = date('j', $timestamp);
        $month = $months[date('n', $timestamp) - 1];
        $year = date('Y', $timestamp);
        
        $tanggal = "$day, $date $month $year";

        // Load the PDF view
        $html = view('backend/denda_pdf', [
            'dendas' => $rows,
            'tanggal' => $tanggal
        ]);

        // Generate PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream PDF to browser
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment;filename="Laporan_Denda_Perpustakaan.pdf"')
            ->setBody($dompdf->output());
    }
}
