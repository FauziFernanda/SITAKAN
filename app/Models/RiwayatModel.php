<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatModel extends Model
{
    protected $table = 'riwayat';
    protected $primaryKey = 'id_riwayat';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id_pinjam',
        'nama_siswa',
        'judul',
        'kelas',
        'tgl_pinjam',
        'tgl_kembali',
        'tgl_selesai',
        'status',
        'keterangan'
    ];
    protected $useTimestamps = false;
}