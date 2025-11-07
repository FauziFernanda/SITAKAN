<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamModel extends Model
{
    protected $table = 'pinjams';
    // Sesuaikan dengan skema DB Anda: primary key di diagram adalah 'id_pinjam'
    protected $primaryKey = 'id_pinjam';
    protected $allowedFields = [
        'id_buku',
        'nama_siswa',
        'kelas',
        'tgl_pinjam',
        'tgl_kembali',
        'tgl_selesai'
    ];
    protected $useTimestamps = false;
}