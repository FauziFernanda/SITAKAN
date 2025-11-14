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
              <th class="px-6 py-4">Kelas</th>
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
                <td class="px-6 py-4"><?= esc($r['kelas'] ?? '-') ?></td>
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
});
</script>
<?= $this->endSection() ?>