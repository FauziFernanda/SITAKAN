<?= $this->extend('backend/layout/main') ?>
<?= $this->section('title') ?>List Denda<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-8">
  <h1 class="text-4xl font-extrabold text-white tracking-widest mb-1">List Denda</h1>
  <p class="text-gray-400 mb-6">Kelola data denda peminjaman</p>
</div>

<div class="flex items-center justify-between mb-4">
  <h3 class="text-gray-300 mb-3 tracking-wide">List Denda Peminjaman</h3>
  <a href="<?= base_url('backend/denda/pdf') ?>" class="px-4 py-2 bg-[#0f7a63] text-white rounded-md font-semibold flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
    </svg>
    Export PDF
  </a>
</div>

<!-- Search form -->
<?php $s = $search ?? ['tgl_mulai' => '', 'tgl_selesai' => '', 'nama' => '', 'judul' => ''];
      $mulai_day = $mulai_month = $mulai_year = '';
      $selesai_day = $selesai_month = $selesai_year = '';
      if (!empty($s['tgl_mulai'])) {
          $d = explode('-', $s['tgl_mulai']);
          if (count($d)===3) { $mulai_year=$d[0]; $mulai_month=(int)$d[1]; $mulai_day=(int)$d[2]; }
      }
      if (!empty($s['tgl_selesai'])) {
          $d = explode('-', $s['tgl_selesai']);
          if (count($d)===3) { $selesai_year=$d[0]; $selesai_month=(int)$d[1]; $selesai_day=(int)$d[2]; }
      }
      $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
?>

<form id="searchForm" method="get" action="<?= base_url('backend/denda') ?>" class="mb-6">
  <div class="flex gap-3 flex-wrap items-end mb-3">
    <div class="flex-1">
      <label class="text-sm text-gray-400 block mb-1">Cari Nama Siswa</label>
      <input type="text" name="nama" placeholder="Nama siswa" value="<?= esc($s['nama']) ?>" class="w-full px-3 py-2 rounded bg-gray-800 text-white" />
    </div>
    <div class="flex-1">
      <label class="text-sm text-gray-400 block mb-1">Cari Judul Buku</label>
      <input type="text" name="judul" placeholder="Judul buku" value="<?= esc($s['judul']) ?>" class="w-full px-3 py-2 rounded bg-gray-800 text-white" />
    </div>
    <div class="flex-none">
      <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Cari</button>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="text-sm text-gray-400 block mb-1">Start Date</label>
      <div class="flex gap-2">
        <select id="mulai_day" class="px-3 py-2 rounded bg-gray-800 text-white text-sm" aria-label="day">
          <option value="">Hari</option>
          <?php for ($i=1;$i<=31;$i++): ?>
            <option value="<?= $i ?>" <?= ($mulai_day===$i)?'selected':'' ?> ><?= $i ?></option>
          <?php endfor; ?>
        </select>
        <select id="mulai_month" class="px-3 py-2 rounded bg-gray-800 text-white text-sm" aria-label="month">
          <option value="">Bulan</option>
          <?php foreach($months as $num => $name): ?>
            <option value="<?= $num ?>" <?= ($mulai_month===$num)?'selected':'' ?> ><?= $name ?></option>
          <?php endforeach; ?>
        </select>
        <select id="mulai_year" class="px-3 py-2 rounded bg-gray-800 text-white text-sm" aria-label="year">
          <option value="">Tahun</option>
          <?php $curY = date('Y'); for($y=$curY; $y>=$curY-20; $y--): ?>
            <option value="<?= $y ?>" <?= ($mulai_year===$y)?'selected':'' ?> ><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div id="dateError" class="mt-2 text-sm text-red-400 hidden">rate tanggal wajib di isi keduanya</div>
    </div>

    <div>
      <label class="text-sm text-gray-400 block mb-1">End Date</label>
      <div class="flex gap-2">
        <select id="selesai_day" class="px-3 py-2 rounded bg-gray-800 text-white text-sm" aria-label="day">
          <option value="">Hari</option>
          <?php for ($i=1;$i<=31;$i++): ?>
            <option value="<?= $i ?>" <?= ($selesai_day===$i)?'selected':'' ?> ><?= $i ?></option>
          <?php endfor; ?>
        </select>
        <select id="selesai_month" class="px-3 py-2 rounded bg-gray-800 text-white text-sm" aria-label="month">
          <option value="">Bulan</option>
          <?php foreach($months as $num => $name): ?>
            <option value="<?= $num ?>" <?= ($selesai_month===$num)?'selected':'' ?> ><?= $name ?></option>
          <?php endforeach; ?>
        </select>
        <select id="selesai_year" class="px-3 py-2 rounded bg-gray-800 text-white text-sm" aria-label="year">
          <option value="">Tahun</option>
          <?php $curY = date('Y'); for($y=$curY; $y>=$curY-20; $y--): ?>
            <option value="<?= $y ?>" <?= ($selesai_year===$y)?'selected':'' ?> ><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
    </div>
  </div>

  <input type="hidden" id="tgl_mulai" name="tgl_mulai" value="<?= esc($s['tgl_mulai']) ?>" />
  <input type="hidden" id="tgl_selesai" name="tgl_selesai" value="<?= esc($s['tgl_selesai']) ?>" />
</form>

<style>
  /* mark overdue names in red */
  .denda-mark { color: #ffffffff; font-weight: 600; }
</style>

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
      <tfoot class="bg-[#3a3a3a] text-white font-bold border-t border-gray-700">
        <tr>
          <td colspan="3" class="px-6 py-4 text-center">Total Denda</td>
          <td class="px-6 py-4 text-center">Rp. <?= number_format($total_denda ?? 0, 0, ',', '.') ?></td>
        </tr>
      </tfoot>
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
      <p>Denda perhari : <span id="dd_denda_perhari" class="value"><?= 'Rp. ' . number_format($denda_perhari ?? 500, 0, ',', '.') ?></span></p>
      <p>Total denda : <span id="dd_total" class="value font-bold"></span></p>
    </div>
  </div>
</div>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
  function fmtDate(d){ if (!d) return '-'; try { const dt=new Date(d); if (isNaN(dt)) return d; return dt.getDate() + ' - ' + (dt.getMonth()+1) + ' - ' + dt.getFullYear(); } catch(e){ return d } }
  function formatRp(n){ return 'Rp. ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }

  // --- compose date selects into hidden inputs for controller ---
  function pad(n){ return (n<10? '0'+n : n); }

  const mulaiDay = document.getElementById('mulai_day');
  const mulaiMonth = document.getElementById('mulai_month');
  const mulaiYear = document.getElementById('mulai_year');
  const selesaiDay = document.getElementById('selesai_day');
  const selesaiMonth = document.getElementById('selesai_month');
  const selesaiYear = document.getElementById('selesai_year');
  const hiddenMulai = document.getElementById('tgl_mulai');
  const hiddenSelesai = document.getElementById('tgl_selesai');

  function composeDate(dayEl, monEl, yrEl, targetEl) {
    const d = dayEl.value; const m = monEl.value; const y = yrEl.value;
    if (d && m && y) {
      targetEl.value = y + '-' + (m.length===1? '0'+m : m) + '-' + pad(parseInt(d,10));
    } else {
      targetEl.value = '';
    }
  }

  function initSelectsFromHidden(hiddenEl, dayEl, monEl, yrEl){
    if (!hiddenEl || !hiddenEl.value) return;
    const parts = hiddenEl.value.split('-');
    if (parts.length===3){
      yrEl.value = parts[0];
      monEl.value = parseInt(parts[1],10);
      dayEl.value = parseInt(parts[2],10);
    }
  }

  initSelectsFromHidden(hiddenMulai, mulaiDay, mulaiMonth, mulaiYear);
  initSelectsFromHidden(hiddenSelesai, selesaiDay, selesaiMonth, selesaiYear);

  [mulaiDay, mulaiMonth, mulaiYear].forEach(el => el && el.addEventListener('change', ()=> composeDate(mulaiDay, mulaiMonth, mulaiYear, hiddenMulai)));
  [selesaiDay, selesaiMonth, selesaiYear].forEach(el => el && el.addEventListener('change', ()=> composeDate(selesaiDay, selesaiMonth, selesaiYear, hiddenSelesai)));

  // Client-side validation: require both Start and End if either is filled
  const searchForm = document.getElementById('searchForm');
  const dateError = document.getElementById('dateError');

  if (searchForm) {
    searchForm.addEventListener('submit', function(e){
      composeDate(mulaiDay, mulaiMonth, mulaiYear, hiddenMulai);
      composeDate(selesaiDay, selesaiMonth, selesaiYear, hiddenSelesai);

      const start = hiddenMulai.value.trim();
      const end = hiddenSelesai.value.trim();
      if ((start && !end) || (!start && end)) {
        e.preventDefault();
        dateError.classList.remove('hidden');
        dateError.scrollIntoView({behavior: 'smooth', block: 'center'});
        return false;
      }
      dateError.classList.add('hidden');
      return true;
    });
  }

  document.querySelectorAll('.btn-denda-details').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const nama = this.dataset.nama || '-';
      const kelas = this.dataset.kelas || '-';
      const judul = this.dataset.judul || '-';
      const tgl_pinjam = this.dataset.tgl_pinjam || '';
      const tgl_selesai = this.dataset.tgl_selesai || '';
      const telat = parseInt(this.dataset.telat_hari || '0', 10);
      const today = new Date();
      const todayStr = today.getDate() + ' - ' + (today.getMonth()+1) + ' - ' + today.getFullYear();

      document.getElementById('dd_nama').textContent = nama;
      document.getElementById('dd_kelas').textContent = kelas;
      document.getElementById('dd_judul').textContent = judul;
      document.getElementById('dd_tgl_pinjam').textContent = fmtDate(tgl_pinjam);
      document.getElementById('dd_tgl_selesai').textContent = fmtDate(tgl_selesai);
      document.getElementById('dd_tgl_kembali').textContent = todayStr;
      document.getElementById('dd_telat').textContent = telat + ' hari';
      const perhari = <?= (int)($denda_perhari ?? 500) ?>;
      document.getElementById('dd_denda_perhari').textContent = formatRp(perhari);
      document.getElementById('dd_total').textContent = formatRp(perhari * telat);

      document.getElementById('modalDetailDenda').classList.remove('hidden');
    });
  });

  document.getElementById('closeDendaDetail').addEventListener('click', function(){
    document.getElementById('modalDetailDenda').classList.add('hidden');
  });
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
