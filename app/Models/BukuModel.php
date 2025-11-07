<?php

namespace App\Models;

use CodeIgniter\Model;

class BukuModel extends Model
{
    protected $table = 'bukus';
    protected $primaryKey = 'id_buku';
    protected $allowedFields = [
        'id_kategori',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'stok'
    ];
}
