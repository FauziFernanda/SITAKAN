<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatModel extends Model
{
    protected $table = 'riwayat';
    protected $primaryKey = 'id_riwayat';
    // allow storing id_pinjam so we can reference the original peminjaman
    protected $allowedFields = ['id_pinjam', 'status', 'keterangan'];
    protected $useTimestamps = false;
}