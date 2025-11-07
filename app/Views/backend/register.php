<?= $this->extend('backend/layout/main') ?>
<?= $this->section('title') ?>Registrasi Akun<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-8">
  <h1 class="text-4xl font-extrabold text-white tracking-widest mb-1">Registrasi Akun</h1>
  <p class="text-gray-400 mb-6">Masukkan Username dan password anda</p>
  
  <a href="#modalAdd" id="btnAdd" class="inline-flex items-center gap-2 bg-[#0f7a63] text-white px-4 py-2 rounded-md font-semibold">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Tambah
  </a>
</div>

<div class="mb-6">
  <h2 class="text-2xl font-semibold text-white mb-4">List Data Akun</h2>

  <!-- global toast will show success messages in top-right -->
  <div id="globalToast" class="global-toast success" style="display:none"></div>

  <table class="w-full text-sm text-left border border-gray-700 peminjaman-table">
    <thead class="bg-[#4b4b4b] text-white">
      <tr>
        <th class="px-6 py-4">Nama</th>
        <th class="px-6 py-4">Username</th>
        <th class="px-6 py-4">Role</th>
        <th class="px-6 py-4">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr class="border-t border-gray-700">
        <td class="px-6 py-4"><?= esc($u['nama']) ?></td>
        <td class="px-6 py-4"><?= esc($u['username']) ?></td>
        <td class="px-6 py-4"><?= esc($u['role']) ?></td>
        <td class="px-6 py-4">
          <a href="<?= base_url('backend/register/edit/'.$u['id_user']) ?>" class="px-3 py-1 bg-yellow-400 text-black rounded mr-2">Edit</a>
          <button class="px-3 py-1 bg-red-600 text-white rounded btn-open-delete" data-id="<?= esc($u['id_user']) ?>" data-nama="<?= esc($u['nama']) ?>">Hapus</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal Tambah -->
<div id="modalAdd" class="custom-modal-overlay hidden">
  <div class="custom-modal" style="max-width:520px;">
    <div class="modal-header bg-[#0f7a63] text-white">
      <div class="modal-title">Tambah Akun</div>
      <button id="closeAdd" class="modal-close" aria-label="Tutup">X</button>
    </div>
    <div class="modal-body">
      <?php if (!isset($editUser)): ?>
        <form method="post" action="<?= base_url('backend/register/create') ?>">
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="nama" 
                   value="<?= esc(old('nama')) ?>" 
                   class="w-full p-2 rounded border border-gray-300 focus:border-[#0f7a63] focus:ring focus:ring-[#0f7a63] focus:ring-opacity-50" />
            <?php if (session()->has('errors') && empty(old('nama'))): ?>
              <p class="mt-1 text-sm text-red-500">Masukkan nama lengkap</p>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" name="username" 
                   value="<?= esc(old('username')) ?>" 
                   class="w-full p-2 rounded border border-gray-300 focus:border-[#0f7a63] focus:ring focus:ring-[#0f7a63] focus:ring-opacity-50" />
            <?php if (session()->has('errors') && empty(old('username'))): ?>
              <p class="mt-1 text-sm text-red-500">Masukkan username</p>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" 
                   class="w-full p-2 rounded border border-gray-300 focus:border-[#0f7a63] focus:ring focus:ring-[#0f7a63] focus:ring-opacity-50" />
            <?php if (session()->has('errors') && empty(old('password'))): ?>
              <p class="mt-1 text-sm text-red-500">Masukkan password</p>
            <?php endif; ?>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role" class="w-full p-2 rounded border border-gray-300 focus:border-[#0f7a63] focus:ring focus:ring-[#0f7a63] focus:ring-opacity-50">
              <option value="pustakawan" <?= old('role') == 'pustakawan' ? 'selected' : '' ?>>Pustakawan</option>
              <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" id="cancelAdd" 
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded font-medium">
              Batal
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-[#0f7a63] hover:bg-[#0c6552] text-white rounded font-medium">
              Tambah
            </button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="custom-modal-overlay hidden">
  <div class="custom-modal" style="max-width:520px;">
    <div class="modal-header bg-yellow-400 text-white">
      <div class="modal-title">Edit Akun</div>
      <button id="closeEdit" class="modal-close" aria-label="Tutup">X</button>
    </div>
    <div class="modal-body">
      <?php if (isset($editUser)): ?>
        <form method="post" action="<?= base_url('backend/register/update/'.$editUser['id_user']) ?>">
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="nama" 
                   value="<?= esc(old('nama', $editUser['nama'])) ?>" 
                   class="w-full p-2 rounded border border-gray-300 focus:border-yellow-400 focus:ring focus:ring-yellow-400 focus:ring-opacity-50" />
            <?php if (session()->has('errors') && empty(old('nama'))): ?>
              <p class="mt-1 text-sm text-red-500">Masukkan nama lengkap</p>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" name="username" 
                   value="<?= esc(old('username', $editUser['username'])) ?>" 
                   class="w-full p-2 rounded border border-gray-300 focus:border-yellow-400 focus:ring focus:ring-yellow-400 focus:ring-opacity-50" />
            <?php if (session()->has('errors') && empty(old('username'))): ?>
              <p class="mt-1 text-sm text-red-500">Masukkan username</p>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password (kosongkan bila tidak diubah)</label>
            <input type="password" name="password" 
                   class="w-full p-2 rounded border border-gray-300 focus:border-yellow-400 focus:ring focus:ring-yellow-400 focus:ring-opacity-50" />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role" class="w-full p-2 rounded border border-gray-300 focus:border-yellow-400 focus:ring focus:ring-yellow-400 focus:ring-opacity-50">
              <option value="pustakawan" <?= old('role', $editUser['role']) == 'pustakawan' ? 'selected' : '' ?>>Pustakawan</option>
              <option value="admin" <?= old('role', $editUser['role']) == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" id="cancelEdit" 
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded font-medium">
              Batal
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded font-medium">
              Update
            </button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal Delete -->
<div id="modalDelete" class="custom-modal-overlay hidden">
  <div class="custom-modal" style="max-width:420px;">
    <div class="modal-header">
      <div class="modal-title">Konfirmasi Hapus</div>
      <button id="closeDelete" class="modal-close" aria-label="Tutup">X</button>
    </div>
    <div class="modal-body">
      <p>Apakah Anda yakin ingin menghapus akun <strong id="del_name"></strong>?</p>
      <form id="formDelete" method="post" action="">
        <?= csrf_field() ?>
        <div class="flex justify-end gap-2 mt-4">
          <button type="button" id="cancelDelete" class="px-3 py-2 rounded border">Batal</button>
          <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Modal handling (Add/Edit)
  const modal = document.getElementById('<?= isset($editUser) ? "modalEdit" : "modalAdd" ?>');
  
  // Open add modal button
  document.getElementById('btnAdd')?.addEventListener('click', function(e){ 
    e.preventDefault(); 
    modal.classList.remove('hidden'); 
  });

  // Close modal buttons
  document.getElementById('<?= isset($editUser) ? "closeEdit" : "closeAdd" ?>')?.addEventListener('click', function(){ 
    modal.classList.add('hidden'); 
  });
  document.getElementById('<?= isset($editUser) ? "cancelEdit" : "cancelAdd" ?>')?.addEventListener('click', function(){ 
    modal.classList.add('hidden'); 
  });
  
  // Delete modal handling
  const modalDelete = document.getElementById('modalDelete');
  const delName = document.getElementById('del_name');
  const formDelete = document.getElementById('formDelete');
  
  document.querySelectorAll('.btn-open-delete').forEach(btn => {
    btn.addEventListener('click', function(){
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-nama');
      delName.textContent = name;
      formDelete.action = '<?= base_url('backend/register/delete/') ?>' + id;
      modalDelete.classList.remove('hidden');
    });
  });
  
  document.getElementById('closeDelete')?.addEventListener('click', function(){ 
    modalDelete.classList.add('hidden'); 
  });
  document.getElementById('cancelDelete')?.addEventListener('click', function(){ 
    modalDelete.classList.add('hidden'); 
  });

  // Success toast handling
  const toast = document.getElementById('globalToast');
  <?php if (session()->getFlashdata('success')): ?>
    if (toast) {
      toast.textContent = <?= json_encode(session()->getFlashdata('success')) ?>;
      toast.style.display = 'block';
      setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }
  <?php endif; ?>

  // Auto-open modal for validation errors or edit mode
  <?php if (!empty(session()->getFlashdata('errors')) || isset($editUser)): ?>
    modal?.classList.remove('hidden');
  <?php endif; ?>
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
