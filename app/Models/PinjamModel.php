<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamModel extends Model
{
    protected $table = 'pinjams';
    protected $primaryKey = 'id_pinjam';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
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