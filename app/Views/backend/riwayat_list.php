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
  <!-- Search form (moved above list header) -->
  <?php $s = $search ?? ['judul'=>'','nama'=>'','tgl_pinjam'=>'','tgl_kembali'=>''];
        // parse existing dates into components (expects YYYY-MM-DD)
        $pinjam_day = $pinjam_month = $pinjam_year = '';
        $kembali_day = $kembali_month = $kembali_year = '';
        if (!empty($s['tgl_pinjam'])) {
            $d = explode('-', $s['tgl_pinjam']);
            if (count($d)===3) { $pinjam_year=$d[0]; $pinjam_month=(int)$d[1]; $pinjam_day=(int)$d[2]; }
        }
        if (!empty($s['tgl_kembali'])) {
            $d = explode('-', $s['tgl_kembali']);
            if (count($d)===3) { $kembali_year=$d[0]; $kembali_month=(int)$d[1]; $kembali_day=(int)$d[2]; }
        }
        $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  ?>

  <form id="searchForm" method="get" action="<?= base_url('backend/riwayat') ?>" class="mb-6">
    <div class="flex gap-3 flex-wrap items-end">
      <div class="flex-1">
        <label class="text-sm text-gray-400 block mb-1">Cari (Judul / Nama)</label>
        <div class="flex gap-3">
          <input type="text" name="judul" placeholder="Judul buku" value="<?= esc($s['judul']) ?>" class="w-1/2 px-3 py-2 rounded bg-gray-800 text-white" />
          <input type="text" name="nama" placeholder="Nama siswa" value="<?= esc($s['nama']) ?>" class="w-1/2 px-3 py-2 rounded bg-gray-800 text-white" />
        </div>
      </div>
      <div class="flex-none">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Cari</button>
      </div>
    </div>

    <div class="mt-3 grid grid-cols-2 gap-3">
      <div>
        <label class="text-sm text-gray-400 block mb-1">Start Date</label>
        <div class="flex gap-2">
          <select id="pinjam_day" class="px-3 py-2 rounded bg-gray-800 text-white" aria-label="day">
            <option value="">Hari</option>
            <?php for ($i=1;$i<=31;$i++): ?>
              <option value="<?= $i ?>" <?= ($pinjam_day===$i)?'selected':'' ?> ><?= $i ?></option>
            <?php endfor; ?>
          </select>
          <select id="pinjam_month" class="px-3 py-2 rounded bg-gray-800 text-white" aria-label="month">
            <option value="">Bulan</option>
            <?php foreach($months as $num => $name): ?>
              <option value="<?= $num ?>" <?= ($pinjam_month===$num)?'selected':'' ?> ><?= $name ?></option>
            <?php endforeach; ?>
          </select>
          <select id="pinjam_year" class="px-3 py-2 rounded bg-gray-800 text-white" aria-label="year">
            <option value="">Tahun</option>
            <?php $curY = date('Y'); for($y=$curY; $y>=$curY-20; $y--): ?>
              <option value="<?= $y ?>" <?= ($pinjam_year===$y)?'selected':'' ?> ><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div id="dateError" class="mt-2 text-sm text-red-400 hidden">rate tanggal wajib di isi keduanya</div>
      </div>

      <div>
        <label class="text-sm text-gray-400 block mb-1">End Date</label>
        <div class="flex gap-2">
          <select id="kembali_day" class="px-3 py-2 rounded bg-gray-800 text-white" aria-label="day">
            <option value="">Hari</option>
            <?php for ($i=1;$i<=31;$i++): ?>
              <option value="<?= $i ?>" <?= ($kembali_day===$i)?'selected':'' ?> ><?= $i ?></option>
            <?php endfor; ?>
          </select>
          <select id="kembali_month" class="px-3 py-2 rounded bg-gray-800 text-white" aria-label="month">
            <option value="">Bulan</option>
            <?php foreach($months as $num => $name): ?>
              <option value="<?= $num ?>" <?= ($kembali_month===$num)?'selected':'' ?> ><?= $name ?></option>
            <?php endforeach; ?>
          </select>
          <select id="kembali_year" class="px-3 py-2 rounded bg-gray-800 text-white" aria-label="year">
            <option value="">Tahun</option>
            <?php $curY = date('Y'); for($y=$curY; $y>=$curY-20; $y--): ?>
              <option value="<?= $y ?>" <?= ($kembali_year===$y)?'selected':'' ?> ><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

    </div>

    <!-- hidden inputs to send composed YYYY-MM-DD to controller -->
    <input type="hidden" id="tgl_pinjam" name="tgl_pinjam" value="<?= esc($s['tgl_pinjam']) ?>" />
    <input type="hidden" id="tgl_kembali" name="tgl_kembali" value="<?= esc($s['tgl_kembali']) ?>" />
  </form>

  <div id="notification" class="fixed top-4 right-4 text-white px-6 py-3 rounded-lg transform transition-all duration-300 translate-x-full z-50 min-w-[300px] text-base font-medium
      <?php if (session()->getFlashdata('success')): ?>
          bg-green-500
      <?php elseif (session()->getFlashdata('error')): ?>
          bg-red-500
      <?php endif; ?>
  ">

      <?php if ($message = session()->getFlashdata('success')): ?>
      <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span id="notificationMessage"><?= esc($message) ?></span>
      </div>
      <?php endif; ?>

      <?php if ($message = session()->getFlashdata('error')): ?>
      <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 101.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <span id="notificationMessage"><?= esc($message) ?></span>
      </div>
      <?php endif; ?>
  </div>

  <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-40">
    <div class="bg-white rounded-lg w-96">
      <div class="p-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus</h3>
      </div>
      <div class="p-4">
        <p class="text-gray-600">Apakah Anda yakin ingin menghapus riwayat peminjaman ini?</p>
      </div>
      <div class="flex justify-end gap-2 p-4 border-t border-gray-200">
        <button id="cancelBtn" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded">
          Batal
        </button>
        <button id="deleteBtn" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded">
          Hapus
        </button>
      </div>
    </div>
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
              <tr class="border-t border-gray-700">
                <td class="px-6 py-4"><?= esc($r['nama_siswa'] ?? '-') ?></td>
                <td class="px-6 py-4"><?= esc($r['judul'] ?? '-') ?></td>
                <td class="px-6 py-4"><?= !empty($r['tgl_pinjam']) ? date('d/m/Y', strtotime($r['tgl_pinjam'])) : '-' ?></td>
                <td class="px-6 py-4"><?= !empty($r['tgl_kembali']) ? date('d/m/Y', strtotime($r['tgl_kembali'])) : '-' ?></td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 rounded text-sm font-medium bg-green-600 text-white">Selesai</span>
                </td>
                <td class="px-6 py-4">
                  <button type="button" 
                          class="btn-delete bg-red-700 text-white px-3 py-1 rounded"
                          data-id="<?= esc($r['id_riwayat']) ?>">
                    Hapus
                  </button>
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
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal');
    const notification = document.getElementById('notification');
    const deleteBtn = document.getElementById('deleteBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    let activeRow = null;
    let activeId = null;

    // Tampilkan notifikasi jika ada flash message
    // LOGIKA INI SEKARANG BENAR, karena 'notification' hanya akan punya 'textContent'
    // jika PHP mencetak pesan flashdata di dalamnya.
    if (notification.textContent.trim()) {
        notification.classList.remove('translate-x-full');
        setTimeout(() => {
            notification.classList.add('translate-x-full');
        }, 3000);
    }

    // (Kode 'showNotification' Anda sebelumnya tidak terpakai, jadi saya hapus agar bersih)
    // (Jika Anda pakai AJAX nanti, fungsi itu bisa ditambahkan lagi)

    // Fungsi untuk menampilkan modal
    function showModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // Fungsi untuk menyembunyikan modal
    function hideModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        activeRow = null;
        activeId = null;
    }

    // Event listener untuk tombol hapus
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            activeId = this.dataset.id;
            activeRow = this.closest('tr');
            showModal();
        });
    });

    // Event listener untuk tombol batal
    cancelBtn.addEventListener('click', hideModal);

    // Event listener untuk klik di luar modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });

    // Event listener untuk tombol konfirmasi hapus
    deleteBtn.addEventListener('click', function() {
        if (!activeId) return;
        
        window.location.href = '<?= base_url('backend/riwayat/delete') ?>/' + activeId;
    });

    // --- compose date selects into hidden inputs for controller ---
    function pad(n){ return (n<10? '0'+n : n); }

    const pinjamDay = document.getElementById('pinjam_day');
    const pinjamMonth = document.getElementById('pinjam_month');
    const pinjamYear = document.getElementById('pinjam_year');
    const kembaliDay = document.getElementById('kembali_day');
    const kembaliMonth = document.getElementById('kembali_month');
    const kembaliYear = document.getElementById('kembali_year');
    const hiddenPinjam = document.getElementById('tgl_pinjam');
    const hiddenKembali = document.getElementById('tgl_kembali');

    function composeDate(dayEl, monEl, yrEl, targetEl) {
      const d = dayEl.value; const m = monEl.value; const y = yrEl.value;
      if (d && m && y) {
        targetEl.value = y + '-' + (m.length===1? '0'+m : m) + '-' + pad(parseInt(d,10));
      } else {
        targetEl.value = '';
      }
    }

    // initialize: if hidden inputs have values, pre-select selects
    function initSelectsFromHidden(hiddenEl, dayEl, monEl, yrEl){
      if (!hiddenEl || !hiddenEl.value) return;
      const parts = hiddenEl.value.split('-');
      if (parts.length===3){
        yrEl.value = parts[0];
        monEl.value = parseInt(parts[1],10);
        dayEl.value = parseInt(parts[2],10);
      }
    }

    initSelectsFromHidden(hiddenPinjam, pinjamDay, pinjamMonth, pinjamYear);
    initSelectsFromHidden(hiddenKembali, kembaliDay, kembaliMonth, kembaliYear);

    [pinjamDay, pinjamMonth, pinjamYear].forEach(el => el && el.addEventListener('change', ()=> composeDate(pinjamDay, pinjamMonth, pinjamYear, hiddenPinjam)));
    [kembaliDay, kembaliMonth, kembaliYear].forEach(el => el && el.addEventListener('change', ()=> composeDate(kembaliDay, kembaliMonth, kembaliYear, hiddenKembali)));

    // Client-side validation: require both Start and End if either is filled
    const searchForm = document.getElementById('searchForm');
    const dateError = document.getElementById('dateError');

    if (searchForm) {
      searchForm.addEventListener('submit', function(e){
        // ensure composed hidden inputs are up-to-date
        composeDate(pinjamDay, pinjamMonth, pinjamYear, hiddenPinjam);
        composeDate(kembaliDay, kembaliMonth, kembaliYear, hiddenKembali);

        const start = hiddenPinjam.value.trim();
        const end = hiddenKembali.value.trim();
        if ((start && !end) || (!start && end)) {
          e.preventDefault();
          dateError.classList.remove('hidden');
          // scroll to error for visibility
          dateError.scrollIntoView({behavior: 'smooth', block: 'center'});
          return false;
        }
        // no validation error -> ensure error hidden
        dateError.classList.add('hidden');
        return true;
      });
    }

});
</script>
<?= $this->endSection() ?>