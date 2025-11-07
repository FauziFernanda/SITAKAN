<?= $this->extend('backend/layout/main') ?>
<?= $this->section('title') ?>List Peminjaman<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-8">
  <h1 class="text-4xl font-extrabold text-white tracking-widest mb-1">List Peminjaman</h1>
  <p class="text-gray-400 mb-6">Kelola data peminjaman dengan cepat, rapi dan terstruktur</p>
</div>

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
          echo "<table class=\"w-full text-sm text-left border border-gray-700 peminjaman-table mb-6\"><thead class=\"bg-[#4b4b4b] text-white\"><tr><th class=\"px-6 py-4\">Nama Siswa</th><th class=\"px-6 py-4\">Judul Buku</th><th class=\"px-6 py-4\">Detail</th><th class=\"px-6 py-4\">Action</th></tr></thead><tbody>";
        }
    ?>
      <tr class="border-t border-gray-700">
        <td class="px-6 py-4"><?= esc($r['nama_siswa'] ?? '-') ?></td>
        <td class="px-6 py-4"><?= esc($r['judul_buku'] ?? '-') ?></td>
        <td class="px-6 py-4">
          <a href="#" class="text-white font-semibold btn-details"
             data-id="<?= esc($r['id_pinjam'] ?? '') ?>"
             data-nama="<?= esc($r['nama_siswa'] ?? '') ?>"
             data-kelas="<?= esc($r['kelas'] ?? '') ?>"
             data-judul="<?= esc($r['judul_buku'] ?? '') ?>"
             data-tgl_pinjam="<?= esc($r['tgl_pinjam'] ?? '') ?>"
             data-tgl_kembali="<?= esc($r['tgl_kembali'] ?? '') ?>"
             data-tgl_selesai="<?= esc($r['tgl_selesai'] ?? '') ?>"
          >View details</a>
        </td>
        <td class="px-6 py-4">
          <button class="action-accept mr-2 btn-return" title="Selesai"
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
      // Use tgl_selesai if present and valid; otherwise fall back to the original 'batas pengembalian' (tgl_kembali)
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
    fetch('<?= base_url('backend/peminjaman/return') ?>/' + pendingReturnId, { method: 'POST', body: fd, headers: {'X-Requested-With':'XMLHttpRequest'} })
      .then(r => r.json())
      .then(j => {
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
      .catch(()=> showToast('Terjadi kesalahan', false));
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
