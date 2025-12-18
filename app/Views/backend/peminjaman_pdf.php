<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .main-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 16px;
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f8f8;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .footer-date {
            margin-bottom: 30px;
        }
        .footer-title {
            margin-bottom: 60px;  /* Space for signature */
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="main-title">LAPORAN DATA PEMINJAMAN BUKU</div>
        <div class="subtitle">DIPERPUSTAKAAN SDN 11 TARATAK, SURIAN</div>
    </div>

    <div class="section-title">Berikut data peminjaman buku siswa</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Judul Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($peminjamans as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= esc($p['nama_siswa'] ?? '-') ?></td>
                    <td><?= esc($p['kelas'] ?? '-') ?></td>
                    <td><?= esc($p['judul_buku'] ?? '-') ?></td>
                    <td><?= date('d-m-Y', strtotime($p['tgl_pinjam'] ?? '')) ?></td>
                    <td><?= date('d-m-Y', strtotime($p['tgl_kembali'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-date"><?= $tanggal ?></div>
        <div class="footer-title">Petugas Perpustakaan</div>
    </div>
</body>
</html>
