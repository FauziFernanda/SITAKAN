-- Add missing columns to riwayat table
ALTER TABLE riwayat ADD COLUMN nama_siswa VARCHAR(100) AFTER id_pinjam;
ALTER TABLE riwayat ADD COLUMN judul VARCHAR(255) AFTER nama_siswa;
ALTER TABLE riwayat ADD COLUMN kelas VARCHAR(50) AFTER judul;
ALTER TABLE riwayat ADD COLUMN tgl_pinjam DATE AFTER kelas;
ALTER TABLE riwayat ADD COLUMN tgl_kembali DATE AFTER tgl_pinjam;
ALTER TABLE riwayat ADD COLUMN tgl_selesai DATE AFTER tgl_kembali;
