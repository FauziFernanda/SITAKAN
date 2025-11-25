<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateRiwayatSchema extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Add missing columns if they don't exist
        $columns = $db->getFieldData('riwayat');
        $columnNames = array_column($columns, 'name');
        
        if (!in_array('nama_siswa', $columnNames)) {
            $db->query('ALTER TABLE riwayat ADD COLUMN nama_siswa VARCHAR(100) AFTER id_pinjam');
        }
        if (!in_array('judul', $columnNames)) {
            $db->query('ALTER TABLE riwayat ADD COLUMN judul VARCHAR(255) AFTER nama_siswa');
        }
        if (!in_array('kelas', $columnNames)) {
            $db->query('ALTER TABLE riwayat ADD COLUMN kelas VARCHAR(50) AFTER judul');
        }
        if (!in_array('tgl_pinjam', $columnNames)) {
            $db->query('ALTER TABLE riwayat ADD COLUMN tgl_pinjam DATE AFTER kelas');
        }
        if (!in_array('tgl_kembali', $columnNames)) {
            $db->query('ALTER TABLE riwayat ADD COLUMN tgl_kembali DATE AFTER tgl_pinjam');
        }
        if (!in_array('tgl_selesai', $columnNames)) {
            $db->query('ALTER TABLE riwayat ADD COLUMN tgl_selesai DATE AFTER tgl_kembali');
        }
        
        echo "Riwayat table columns updated successfully!\n";
    }
}
