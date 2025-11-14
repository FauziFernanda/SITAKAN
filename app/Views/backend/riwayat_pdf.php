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
            font-weight: bold;
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
        <div class="main-title">LAPORAN RIWAYAT SISWA MEMINJAMAN BUKU</div>
        <div class="subtitle">DIPERPUSTAKAAN SDN 11 TARATAK, SURIAN</div>
    </div>

    <div class="section-title">Riwayat Peminjaman</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($riwayats as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= esc($r['nama_siswa'] ?? '-') ?></td>
                    <td><?= esc($r['kelas'] ?? '-') ?></td>
                    <td><?= !empty($r['tgl_pinjam']) ? date('d/m/Y', strtotime($r['tgl_pinjam'])) : '-' ?></td>
                    <td><?= !empty($r['tgl_kembali']) ? date('d/m/Y', strtotime($r['tgl_kembali'])) : '-' ?></td>
                    <td><?= esc($r['status'] ?? 'Selesai') ?></td>
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