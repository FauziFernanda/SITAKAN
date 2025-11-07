<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatModel extends Model
{
    protected $table = 'riwayat';
    protected $primaryKey = 'id_riwayat';
    protected $allowedFields = ['status', 'keterangan'];
    protected $useTimestamps = false;
}