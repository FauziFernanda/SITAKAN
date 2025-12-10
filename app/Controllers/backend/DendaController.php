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
        $tgl_mulai = trim((string) $this->request->getGet('tgl_mulai'));
        $tgl_selesai = trim((string) $this->request->getGet('tgl_selesai'));
        $nama = trim((string) $this->request->getGet('nama'));
        $judul = trim((string) $this->request->getGet('judul'));
        
        if ((($tgl_mulai === '') && ($tgl_selesai !== '')) || (($tgl_mulai !== '') && ($tgl_selesai === ''))) {
            session()->setFlashdata('error', 'rate tanggal wajib di isi keduanya');
            $qs = http_build_query($this->request->getGet());
            return redirect()->to(base_url('backend/denda') . ($qs ? ('?' . $qs) : ''));
        }

        return $this->renderDendaList($tgl_mulai, $tgl_selesai, $nama, $judul);
    }

    private function renderDendaList($tgl_mulai = '', $tgl_selesai = '', $nama = '', $judul = '')
    {
        $pinjamModel = new PinjamModel();

        $query = $pinjamModel
            ->select('pinjams.*, bukus.judul as judul_buku')
            ->join('bukus', 'bukus.id_buku = pinjams.id_buku', 'left')
            ->where("(pinjams.tgl_selesai IS NULL OR pinjams.tgl_selesai = '' OR pinjams.tgl_selesai = '0000-00-00')", null, false)
            ->where('pinjams.tgl_kembali <', date('Y-m-d'));
        
        if ($tgl_mulai !== '' && $tgl_selesai !== '') {
            $query = $query->where('pinjams.tgl_kembali >=', $tgl_mulai)
                           ->where('pinjams.tgl_kembali <=', $tgl_selesai);
        } elseif ($tgl_mulai !== '') {
            $query = $query->where('pinjams.tgl_kembali >=', $tgl_mulai);
        } elseif ($tgl_selesai !== '') {
            $query = $query->where('pinjams.tgl_kembali <=', $tgl_selesai);
        }
        
        if ($nama !== '') {
            $query = $query->like('pinjams.nama_siswa', $nama);
        }
        
        if ($judul !== '') {
            $query = $query->like('bukus.judul', $judul);
        }
        
        $rows = $query->orderBy('pinjams.tgl_kembali', 'ASC')->findAll();

        // compute days late and per-row fine, also accumulate total
        $today = new \DateTime('today');
        // per-day fine (Rupiah). If you later want to make this configurable,
        // move it to a config file or database setting.
        $perhari = 500;
        $total = 0;
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
            $r['denda'] = ($r['telat_hari'] ?? 0) * $perhari;
            $total += $r['denda'];
        }

        return view('backend/denda_list', [
            'dendas' => $rows, 
            'total_denda' => $total, 
            'denda_perhari' => $perhari,
            'search' => ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai, 'nama' => $nama, 'judul' => $judul]
        ]);
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

        // compute days late and per-row fine, and total
        $today = new \DateTime('today');
        $perhari = 500;
        $total = 0;
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
            $r['denda'] = ($r['telat_hari'] ?? 0) * $perhari;
            $total += $r['denda'];
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

        // Load the PDF view (include totals)
        $html = view('backend/denda_pdf', [
            'dendas' => $rows,
            'tanggal' => $tanggal,
            'total_denda' => $total,
            'denda_perhari' => $perhari,
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
