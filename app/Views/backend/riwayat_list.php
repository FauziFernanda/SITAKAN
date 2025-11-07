<?= $this->extend('backend/layout/main') ?>
<?= $this->section('title') ?>Riwayat Peminjaman<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-8">
  <h1 class="text-4xl font-extrabold text-white tracking-widest mb-1">Riwayat Peminjaman</h1>
  <p class="text-gray-400 mb-6">Data peminjaman yang telah selesai</p>
</div>

<div class="flex justify-between items-center mb-6">
  <h3 class="text-gray-300 tracking-wide">List Riwayat Peminjaman</h3>
  <a href="<?= base_url('backend/riwayat/pdf') ?>" class="px-4 py-2 bg-[#0f7a63] text-white rounded-md font-semibold flex items-center gap-2 hover:bg-[#0d6956] transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
    </svg>
    Export PDF
  </a>
</div>

  <?php $groups = $groupedRiwayats ?? null; ?>
  <?php if (empty($groups)): ?>
    <div class="border border-gray-700 rounded p-6 text-center text-gray-400 italic">Belum ada riwayat peminjaman</div>
  <?php else: ?>
    <?php foreach ($groups as $dateKey => $group): ?>
      <div class="mb-8">
        <div class="text-sm text-gray-400 mb-3"><?= esc($group['label']) ?></div>

        <table class="w-full text-sm text-left border border-gray-700 peminjaman-table">
          <thead class="bg-[#4b4b4b] text-white">
            <tr>
              <th class="px-6 py-4">Nama Siswa</th>
              <th class="px-6 py-4">Judul Buku</th>
              <th class="px-6 py-4">Tanggal Pinjam</th>
              <th class="px-6 py-4">Tanggal Kembali</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($group['items'] as $r): ?>
              <tr class="border-t border-gray-700" data-id="<?= esc($r['id_pinjam'] ?? $r['id'] ?? '') ?>">
                <td class="px-6 py-4"><?= esc($r['nama_siswa'] ?? '-') ?></td>
                <td class="px-6 py-4"><?= esc($r['judul_buku'] ?? '-') ?></td>
                <td class="px-6 py-4"><?= !empty($r['tgl_pinjam']) ? date('d/m/Y', strtotime($r['tgl_pinjam'])) : '-' ?></td>
                <td class="px-6 py-4"><?= !empty($r['tgl_selesai']) ? date('d/m/Y', strtotime($r['tgl_selesai'])) : '-' ?></td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 rounded text-sm font-medium bg-green-600 text-white">Selesai</span>
                </td>
                <td class="px-6 py-4">
                  <button class="btn-delete bg-red-700 text-white px-3 py-1 rounded" data-id="<?= esc($r['id_pinjam'] ?? $r['id'] ?? '') ?>">Hapus</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  function handleDelete(id, rowEl) {
    if (!confirm('Hapus riwayat ini?')) return;
    fetch('<?= base_url('backend/riwayat/delete') ?>/' + id, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(r => r.json()).then(data => {
      if (data.success) {
        // remove row from DOM
        if (rowEl) rowEl.remove();
        // if table becomes empty, reload page to show empty state
        setTimeout(() => { location.reload(); }, 300);
      } else {
        alert(data.message || 'Gagal menghapus data');
      }
    }).catch(() => alert('Terjadi kesalahan'));
  }

  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      const row = this.closest('tr');
      handleDelete(id, row);
    });
  });
});
</script>
<?= $this->endSection() ?>