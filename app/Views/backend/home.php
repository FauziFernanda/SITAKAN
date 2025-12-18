<?= $this->extend('backend/layout/main') ?>
<?= $this->section('title') ?>Home<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="flex flex-col lg:flex-row items-center justify-between mb-10">
  <div class="lg:w-1/2 space-y-2">
    <p class="text-gray-400 tracking-widest uppercase text-sm">Welcome to SITAKAN</p>
    <h1 class="text-5xl font-extrabold text-white mt-2 tracking-widest ">SDN 11</h1>
    <h2 class="text-5xl font-extrabold text-white tracking-widest  ">TARATAK, SURIAN</h2>
    <p class="text-gray-400 mt-3 tracking-wide">Ayo kelola buku dengan mudah, cepat, dan cerdas.</p>
    <div class="mt-6 flex gap-4">
      <a href="<?= base_url('backend/buku_list') ?>" class="bg-green-700 text-white px-6 py-3 rounded-md hover:bg-green-800 tracking-wide">Kelola Buku</a>
      <a href="<?= base_url('backend/peminjaman') ?>" class="border border-gray-400 text-gray-200 px-6 py-3 rounded-md hover:bg-gray-700 tracking-wide">Peminjam</a>
    </div>
  </div>

  <!-- Gambar kanan -->
  <div class="mt-8 lg:mt-0 lg:w-1/2 flex justify-center lg:justify-end">
    <img src="<?= base_url('assets/img/buku.png') ?>" alt="Ilustrasi Buku" class="w-80 drop-shadow-lg lg:translate-x-[-20px]">
  </div>
</div>

<div class="-mt-8">
  <h3 class="text-gray-300 mb-3 tracking-wide">3 Buku Terlaris</h3>
  <table class="w-full text-sm text-left border border-gray-700 tracking-wide mb-8">
    <thead class="bg-[#2A2A2A] text-gray-300">
      <tr>
        <th class="px-6 py-3">Judul Buku</th>
        <th class="px-6 py-3">Penulis</th>
        <th class="px-6 py-3">Jumlah Pinjam</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($topBooks) && is_array($topBooks)): ?>
        <?php foreach ($topBooks as $b): ?>
          <tr class="border-t border-gray-700">
            <td class="px-6 py-3"><?= esc($b['judul']) ?></td>
            <td class="px-6 py-3"><?= esc($b['penulis']) ?></td>
            <td class="px-6 py-3"><?= esc($b['jml_pinjam']) ?> kali</td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr class="border-t border-gray-700">
          <td class="px-6 py-3 text-gray-400 italic" colspan="3">Belum ada data peminjaman</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <h3 class="text-gray-300 mb-3 tracking-wide">List Buku</h3>
  <table class="w-full text-sm text-left border border-gray-700 tracking-wide">
    <thead class="bg-[#2A2A2A] text-gray-300">
      <tr>
        <th class="px-6 py-3">Judul Buku</th>
        <th class="px-6 py-3">Kategori</th>
        <th class="px-6 py-3">Penulis</th>
        <th class="px-6 py-3">Stok</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($bukus) && is_array($bukus)): ?>
        <?php foreach ($bukus as $b): ?>
          <tr class="border-t border-gray-700">
            <td class="px-6 py-3"><?= esc($b['judul']) ?></td>
            <td class="px-6 py-3"><?= esc($b['kategori'] ?? 'Tanpa Kategori') ?></td>
            <td class="px-6 py-3"><?= esc($b['penulis']) ?></td>
            <td class="px-6 py-3"><?= esc($b['stok']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr class="border-t border-gray-700">
          <td class="px-6 py-3 text-gray-400 italic" colspan="4">Belum ada data buku</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>
