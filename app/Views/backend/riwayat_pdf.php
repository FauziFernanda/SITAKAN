<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Peminjaman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan Riwayat Peminjaman Buku</div>
        <div class="subtitle">Tanggal: <?= $tanggal ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Judul Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Tanggal Selesai</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($riwayats as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= esc($r['nama_siswa'] ?? '-') ?></td>
                    <td><?= esc($r['judul_buku'] ?? '-') ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tgl_kembali'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tgl_selesai'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Total Peminjaman: <?= count($riwayats) ?></p>
    </div>
</body>
</html>