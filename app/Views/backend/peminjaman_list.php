<?= $this->extend('backend/layout/main') ?>
<?= $this->section('title') ?>List Peminjaman<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-8">
  <h1 class="text-4xl font-extrabold text-white tracking-widest mb-1">List Peminjaman</h1>
  <p class="text-gray-400 mb-6">Kelola data peminjaman dengan cepat, rapi dan terstruktur</p>
</div>

<div class="flex items-center justify-between mb-4">
  <h3 class="text-gray-300 mb-3 tracking-wide">List Data Peminjaman</h3>
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

<form id="searchForm" method="get" action="<?= base_url('backend/peminjaman') ?>" class="mb-6">
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

  <div class="grid grid-cols-2 gap-3" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem;">
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

<div>
  <h3 class="text-gray-300 mb-3 tracking-wide">List Data Peminjaman</h3>

  <?php $rows = $peminjamans ?? []; ?>
  <?php if (empty($rows)): ?>
    <div class="border border-gray-700 rounded p-6 text-center text-gray-400 italic">Data belum tersedia</div>
  <?php else: ?>
    <?php
      $currentDate = null;
      foreach ($rows as $r):
        $rowDate = isset($r['tgl_pinjam']) ? date('Y-m-d', strtotime($r['tgl_pinjam'])) : '';
        if ($rowDate !== $currentDate) {
          // close previous table if any
          if (!is_null($currentDate)) {
            echo "</tbody></table>";
          }
          // new date header
          $currentDate = $rowDate;
          $pretty = $rowDate ? date('l, d-m-Y', strtotime($rowDate)) : 'Tanggal tidak diketahui';
          echo "<div class=\"mb-4 mt-6\"><h4 class=\"text-gray-200 font-semibold\">".esc($pretty)."</h4></div>";
          echo "<table class=\"w-full text-sm text-left border border-gray-700 peminjaman-table mb-6\" style=\"table-layout: fixed;\"><thead class=\"bg-[#4b4b4b] text-white\"><tr><th class=\"px-6 py-4\" style=\"width: 20%;\">Nama Siswa</th><th class=\"px-6 py-4\" style=\"width: 40%;\">Judul Buku</th><th class=\"px-6 py-4\" style=\"width: 18%;\">Detail</th><th class=\"px-6 py-4\" style=\"width: 22%;\">Action</th></tr></thead><tbody>";
        }
    ?>
      <tr class="border-t border-gray-700">
        <?php
          // determine if this loan is overdue: due date (tgl_kembali) before today and not yet returned (tgl_selesai empty)
          $isLate = false;
          if (!empty($r['tgl_kembali']) && (empty($r['tgl_selesai']) || $r['tgl_selesai'] === '0000-00-00')) {
            try { $isLate = (new DateTime($r['tgl_kembali'])) < new DateTime('today'); } catch(Exception $e) { $isLate = false; }
          }
        ?>
        <td class="px-6 py-4 <?= $isLate ? 'text-red-600 font-semibold' : '' ?>"><?= esc($r['nama_siswa'] ?? '-') ?></td>
        <td class="px-6 py-4 <?= $isLate ? 'text-red-600 font-semibold' : '' ?>"><?= esc($r['judul_buku'] ?? '-') ?></td>
        <td class="px-6 py-4">
          <a href="#" class="btn-details no-underline"
             data-id="<?= esc($r['id_pinjam'] ?? '') ?>"
             data-nama="<?= esc($r['nama_siswa'] ?? '') ?>"
             data-kelas="<?= esc($r['kelas'] ?? '') ?>"
             data-judul="<?= esc($r['judul_buku'] ?? '') ?>"
             data-tgl_pinjam="<?= esc($r['tgl_pinjam'] ?? '') ?>"
             data-tgl_kembali="<?= esc($r['tgl_kembali'] ?? '') ?>"
             data-tgl_selesai="<?= esc($r['tgl_selesai'] ?? '') ?>"
          >View details</a>
        </td>
        <td class="px-6 py-4 flex items-center gap-2">
          <button class="action-accept btn-return" title="Selesai"
                  data-id="<?= esc($r['id_pinjam'] ?? '') ?>"
                  data-buku_id="<?= esc($r['id_buku'] ?? '') ?>"
          >
            <img src="<?= base_url('assets/icons/ceklis.png') ?>" alt="ceklis" style="width:22px;height:22px;">
          </button>
          <button class="action-delete btn-delete" title="Hapus"
                  data-id="<?= esc($r['id_pinjam'] ?? '') ?>"
                  data-buku_id="<?= esc($r['id_buku'] ?? '') ?>"
          >
            <img src="<?= base_url('assets/icons/sampah.png') ?>" alt="hapus" style="width:20px;height:20px;">
          </button>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?php // Modals and JS for details/return/delete ?>
<?= $this->section('scripts') ?>
<!-- Detail modal -->
<div id="modalDetailPeminjam" class="custom-modal-overlay hidden">
  <div class="custom-modal">
    <div class="modal-header">
      <div class="modal-title">Detail Peminjam</div>
      <button id="closeDetail" class="modal-close" aria-label="Tutup">X</button>
    </div>
    <div class="modal-body">
      <p>Nama Siswa: <span id="d_nama" class="value"></span></p>
      <p>Kelas: <span id="d_kelas" class="value"></span></p>
      <p>Judul Buku: <span id="d_judul" class="value"></span></p>
      <p>Tanggal Pinjam: <span id="d_tgl_pinjam" class="value"></span></p>
      <p>Tanggal Selesai: <span id="d_tgl_selesai" class="value"></span></p>
    </div>
  </div>
</div>

<!-- Confirm return modal -->
<div id="modalConfirmReturn" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg w-96 p-6 relative">
    <h3 class="text-lg font-bold mb-4">Konfirmasi Pengembalian</h3>
    <p>Apakah Anda yakin ingin menandai peminjaman ini sebagai selesai dan mengembalikan stok buku?</p>
      <div class="mt-4 text-right">
    <button id="cancelReturn" class="mr-2 px-4 py-1">Batal</button>
    <!-- keep button background neutral; make text black per request -->
    <button id="confirmReturn" class="px-4 py-1 rounded border text-black">Konfirmasi</button>
      </div>
  </div>
</div>

<!-- Confirm delete modal -->
<div id="modalConfirmDelete" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg w-96 p-6 relative">
    <h3 class="text-lg font-bold mb-4">Konfirmasi Hapus</h3>
    <p>Jika dihapus, stok buku akan dikembalikan. Lanjutkan menghapus peminjaman ini?</p>
    <div class="mt-4 text-right">
  <button id="cancelDelete" class="mr-2 px-4 py-1">Batal</button>
  <!-- neutral background, black text -->
  <button id="confirmDelete" class="px-4 py-1 rounded border text-black">Hapus</button>
    </div>
  </div>
</div>

<script>
// expose CSRF token name/value for fetch requests
const csrfName = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';

document.addEventListener('DOMContentLoaded', function(){
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

  // Details modal
  document.querySelectorAll('.btn-details').forEach(function(el){
    el.addEventListener('click', function(e){
      e.preventDefault();
      document.getElementById('d_nama').textContent = this.dataset.nama || '-';
      document.getElementById('d_kelas').textContent = this.dataset.kelas || '-';
      document.getElementById('d_judul').textContent = this.dataset.judul || '-';
      // format dates to a friendly display (dd - mm - yyyy) or show '-' if missing
      function fmt(d){
        if (!d) return '-';
        try{ const dt = new Date(d); if (isNaN(dt)) return d; return dt.getDate() + ' - ' + (dt.getMonth()+1) + ' - ' + dt.getFullYear(); }catch(e){return d}
      }
      document.getElementById('d_tgl_pinjam').textContent = fmt(this.dataset.tgl_pinjam);
      let shownFinish = this.dataset.tgl_selesai;
      if (!shownFinish || shownFinish === '0000-00-00') {
        shownFinish = this.dataset.tgl_kembali;
      }
      document.getElementById('d_tgl_selesai').textContent = fmt(shownFinish);
      document.getElementById('modalDetailPeminjam').classList.remove('hidden');
    });
  });
  document.getElementById('closeDetail').addEventListener('click', function(){
    document.getElementById('modalDetailPeminjam').classList.add('hidden');
  });

  // small in-place toast element and helper
  // create toast element (CSS-driven styling)
  const globalToast = document.createElement('div');
  globalToast.id = 'globalToast';
  globalToast.className = 'global-toast';
  document.body.appendChild(globalToast);
  function showToast(msg, success = true){
    globalToast.textContent = msg || (success? 'Berhasil' : 'Gagal');
    globalToast.classList.remove('success','error');
    globalToast.classList.add(success? 'success' : 'error');
    globalToast.style.display = 'block';
    clearTimeout(globalToast._hideTimer);
    globalToast._hideTimer = setTimeout(()=> { globalToast.style.display = 'none'; globalToast.classList.remove('success','error'); }, 2000);
  }

  // Return flow (remove row on success)
  let pendingReturnId = null;
  let pendingReturnBtn = null;
  document.querySelectorAll('.btn-return').forEach(function(btn){
    btn.addEventListener('click', function(){
      pendingReturnId = this.dataset.id;
      pendingReturnBtn = this;
      document.getElementById('modalConfirmReturn').classList.remove('hidden');
    });
  });
  document.getElementById('cancelReturn').addEventListener('click', function(){ pendingReturnId = null; pendingReturnBtn = null; document.getElementById('modalConfirmReturn').classList.add('hidden'); });
  document.getElementById('confirmReturn').addEventListener('click', function(){
    if (!pendingReturnId) return;
    const fd = new FormData();
    fd.append(csrfName, csrfHash);
    fetch('<?= base_url('backend/peminjaman/complete') ?>/' + pendingReturnId, { method: 'POST', body: fd, headers: {'X-Requested-With':'XMLHttpRequest'} })
      .then(r => r.json())
      .then(j => {
        console.log('Complete response:', j); // DEBUG
        showToast(j.message || (j.success? 'Berhasil' : 'Gagal'), !!j.success);
        if (j.success) {
          // remove the row in-place
          try{
            if (pendingReturnBtn) {
              const tr = pendingReturnBtn.closest('tr');
              if (tr) tr.parentNode.removeChild(tr);
            }
          }catch(e){}
        }
      })
      .catch((err)=> {
        console.error('Fetch error:', err); // DEBUG
        showToast('Terjadi kesalahan', false);
      });
    document.getElementById('modalConfirmReturn').classList.add('hidden'); pendingReturnId = null; pendingReturnBtn = null;
  });

  // Delete flow
  let pendingDeleteId = null;
  document.querySelectorAll('.btn-delete').forEach(function(btn){
    btn.addEventListener('click', function(){
      pendingDeleteId = this.dataset.id;
      pendingDeleteBtn = this;
      document.getElementById('modalConfirmDelete').classList.remove('hidden');
    });
  });
  let pendingDeleteBtn = null;
  document.getElementById('cancelDelete').addEventListener('click', function(){ pendingDeleteId = null; pendingDeleteBtn = null; document.getElementById('modalConfirmDelete').classList.add('hidden'); });
  document.getElementById('confirmDelete').addEventListener('click', function(){
    if (!pendingDeleteId) return;
    const fd = new FormData();
    fd.append(csrfName, csrfHash);
    fetch('<?= base_url('backend/peminjaman/delete') ?>/' + pendingDeleteId, { method: 'POST', body: fd, headers: {'X-Requested-With':'XMLHttpRequest'} })
      .then(r => r.json())
      .then(j => {
        showToast(j.message || (j.success? 'Berhasil' : 'Gagal'), !!j.success);
        if (j.success) {
          try{
            if (pendingDeleteBtn) {
              const tr = pendingDeleteBtn.closest('tr');
              if (tr) tr.parentNode.removeChild(tr);
            }
          }catch(e){}
        }
      })
      .catch(()=> showToast('Terjadi kesalahan', false));
    document.getElementById('modalConfirmDelete').classList.add('hidden'); pendingDeleteId = null; pendingDeleteBtn = null;
  });

  // (showToast defined above and uses the CSS-driven #globalToast)
});
</script>

<?= $this->endSection() ?>
