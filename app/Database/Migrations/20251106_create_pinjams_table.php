<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePinjamsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'buku_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'nama_peminjam' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'kelas' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'tanggal_pinjam' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'tanggal_kembali' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => 'dipinjam',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        // Foreign key assumes the buku table is named 'bukus' with PK 'id_buku'
        $this->forge->addForeignKey('buku_id', 'bukus', 'id_buku', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pinjams');
    }

    public function down()
    {
        $this->forge->dropTable('pinjams');
    }
}
