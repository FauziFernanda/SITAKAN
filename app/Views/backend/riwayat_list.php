<?= $this->extend('backend/layout/main') ?>
<?= $this->section('title') ?>Riwayat Peminjaman<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-8">
  <h1 class="text-4xl font-extrabold text-white tracking-widest mb-1">Riwayat Peminjaman</h1>
  <p class="text-gray-400 mb-6">Data peminjaman yang telah selesai</p>
</div>

<div>
  <h3 class="text-gray-300 mb-3 tracking-wide">List Riwayat Peminjaman</h3>

  <?php $rows = $riwayats ?? []; ?>
  <?php if (empty($rows)): ?>
    <div class="border border-gray-700 rounded p-6 text-center text-gray-400 italic">Belum ada riwayat peminjaman</div>
  <?php else: ?>
    <table class="w-full text-sm text-left border border-gray-700 peminjaman-table">
      <thead class="bg-[#4b4b4b] text-white">
        <tr>
          <th class="px-6 py-4">Nama Siswa</th>
          <th class="px-6 py-4">Judul Buku</th>
          <th class="px-6 py-4">Tanggal Pinjam</th>
          <th class="px-6 py-4">Tanggal Kembali</th>
          <th class="px-6 py-4">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr class="border-t border-gray-700">
            <td class="px-6 py-4"><?= esc($r['nama_siswa'] ?? '-') ?></td>
            <td class="px-6 py-4"><?= esc($r['judul_buku'] ?? '-') ?></td>
            <td class="px-6 py-4"><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
            <td class="px-6 py-4"><?= date('d/m/Y', strtotime($r['tgl_selesai'])) ?></td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 rounded text-sm font-medium bg-green-600 text-white">Selesai</span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>