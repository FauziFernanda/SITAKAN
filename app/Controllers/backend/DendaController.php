<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\PinjamModel;

class DendaController extends BaseController
{
    /**
     * Show list of overdue (denda) peminjaman: nama siswa, judul buku, telat (hari), action
     */
    public function index()
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
}
