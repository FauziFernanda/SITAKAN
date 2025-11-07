<?= $this->extend('backend/layout/main') ?>
<?= $this->section('title') ?>List Denda<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-8">
  <h1 class="text-4xl font-extrabold text-white tracking-widest mb-1">List Denda</h1>
  <p class="text-gray-400 mb-6">Kelola data denda peminjaman</p>
</div>

<div class="flex items-center justify-between mb-4">
  <h3 class="text-gray-300 mb-3 tracking-wide">List Denda Peminjaman</h3>
  <a id="exportPdf" href="#" class="px-4 py-2 bg-[#0f7a63] text-white rounded-md font-semibold">Export PDF</a>
</div>

<?php $rows = $dendas ?? []; ?>
  <?php if (empty($rows)): ?>
    <div class="border border-gray-700 rounded p-6 text-center text-gray-400 italic">Belum ada peminjam yang terlambat</div>
  <?php else: ?>
    <table class="w-full text-sm text-left border border-gray-700 peminjaman-table mb-6">
      <thead class="bg-[#4b4b4b] text-white"><tr>
        <th class="px-6 py-4">Nama Siswa</th>
        <th class="px-6 py-4">Judul Buku</th>
        <th class="px-6 py-4">Telat (hari)</th>
        <th class="px-6 py-4">Action</th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr class="border-t border-gray-700">
          <td class="px-6 py-4"><span class="denda-mark"><?= esc($r['nama_siswa'] ?? '-') ?></span></td>
          <td class="px-6 py-4"><span class="denda-mark"><?= esc($r['judul_buku'] ?? '-') ?></span></td>
          <td class="px-6 py-4"><?= esc($r['telat_hari'] ?? 0) ?> hari</td>
          <td class="px-6 py-4">
            <a href="#" class="text-white font-semibold btn-denda-details"
               data-id="<?= esc($r['id_pinjam'] ?? '') ?>"
               data-nama="<?= esc($r['nama_siswa'] ?? '') ?>"
               data-kelas="<?= esc($r['kelas'] ?? '') ?>"
               data-judul="<?= esc($r['judul_buku'] ?? '') ?>"
               data-tgl_pinjam="<?= esc($r['tgl_pinjam'] ?? '') ?>"
               data-tgl_selesai="<?= esc($r['tgl_kembali'] ?? '') ?>"
               data-telat_hari="<?= esc($r['telat_hari'] ?? 0) ?>"
            >Details</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
 
<!-- Detail modal for denda -->
<div id="modalDetailDenda" class="custom-modal-overlay hidden">
  <div class="custom-modal" style="max-width:520px;">
    <div class="modal-header">
      <div class="modal-title">Detail Peminjam</div>
      <button id="closeDendaDetail" class="modal-close" aria-label="Tutup">X</button>
    </div>
    <div class="modal-body">
      <p>Nama Siswa : <span id="dd_nama" class="value"></span></p>
      <p>Kelas : <span id="dd_kelas" class="value"></span></p>
      <p>Judul Buku : <span id="dd_judul" class="value"></span></p>
      <p>Tanggal Pinjam : <span id="dd_tgl_pinjam" class="value"></span></p>
      <p>Tanggal Selesai : <span id="dd_tgl_selesai" class="value"></span></p>
      <p>Tanggal Kembali : <span id="dd_tgl_kembali" class="value"></span></p>
      <p>Telat : <span id="dd_telat" class="value"></span></p>
      <p>Denda perhari : <span id="dd_denda_perhari" class="value">Rp. 500</span></p>
      <p>Total denda : <span id="dd_total" class="value font-bold"></span></p>
    </div>
  </div>
</div>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
  function fmtDate(d){ if (!d) return '-'; try { const dt=new Date(d); if (isNaN(dt)) return d; return dt.getDate() + ' - ' + (dt.getMonth()+1) + ' - ' + dt.getFullYear(); } catch(e){ return d } }
  function formatRp(n){ return 'Rp. ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }

  document.querySelectorAll('.btn-denda-details').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const nama = this.dataset.nama || '-';
      const kelas = this.dataset.kelas || '-';
      const judul = this.dataset.judul || '-';
      const tgl_pinjam = this.dataset.tgl_pinjam || '';
      // tgl_selesai in this view we use the stored due-date (tgl_kembali)
      const tgl_selesai = this.dataset.tgl_selesai || '';
      const telat = parseInt(this.dataset.telat_hari || '0', 10);
      const today = new Date();
      const todayStr = today.getDate() + ' - ' + (today.getMonth()+1) + ' - ' + today.getFullYear();

      document.getElementById('dd_nama').textContent = nama;
      document.getElementById('dd_kelas').textContent = kelas;
      document.getElementById('dd_judul').textContent = judul;
      document.getElementById('dd_tgl_pinjam').textContent = fmtDate(tgl_pinjam);
      document.getElementById('dd_tgl_selesai').textContent = fmtDate(tgl_selesai);
      // tanggal kembali refers to the actual return day (today) as requested
      document.getElementById('dd_tgl_kembali').textContent = todayStr;
      document.getElementById('dd_telat').textContent = telat + ' hari';
      const perhari = 500;
      document.getElementById('dd_denda_perhari').textContent = formatRp(perhari);
      document.getElementById('dd_total').textContent = formatRp(perhari * telat);

      document.getElementById('modalDetailDenda').classList.remove('hidden');
    });
  });

  document.getElementById('closeDendaDetail').addEventListener('click', function(){
    document.getElementById('modalDetailDenda').classList.add('hidden');
  });

  // Export PDF placeholder: you can wire a real endpoint later
  document.getElementById('exportPdf').addEventListener('click', function(e){ e.preventDefault(); alert('Export PDF belum diimplementasikan.'); });
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
