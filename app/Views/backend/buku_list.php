<?= $this->extend('backend/layout/main') ?>
<?= $this->section('title') ?>List Buku<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-8">

  <!-- Header Section -->
  <div>
    <h1 class="text-4xl font-extrabold text-white tracking-widest mb-1">List Buku</h1>
    <p class="text-gray-400 mb-6">Ayo kelola buku dengan cepat, efektif, dan tertata</p>

    <div class="flex flex-wrap gap-4">
      <button id="btnAddBuku" type="button" class="bg-[#25622D] hover:bg-[#1E4E22] text-white px-6 py-2 rounded-md flex items-center gap-2 font-semibold transition-all">
        <img src="<?= base_url('assets/icons/tambah.png') ?>" alt="tambah" class="w-4 h-4 inline-block"> Tambah Buku
      </button>
      <button id="btnOpenKategori" type="button" class="border border-gray-400 text-gray-200 px-6 py-2 rounded-md hover:bg-gray-700 font-semibold transition-all">
        Kategori
      </button>
    </div>
  </div>

  <!-- Search -->
  <div class="w-full md:w-4/4">
    <form method="get" action="<?= base_url('backend/buku_list') ?>">
   <div class="bg-[#2A2A2A] flex items-center rounded-full px-5 py-2">
  <img src="<?= base_url('assets/icons/search.png') ?>" alt="search" class="w-8 h-8 mr-4 opacity-80">
   <input name="q" type="text" placeholder="Search...." value="<?= esc($q ?? '') ?>"
     class="bg-transparent outline-none text-gray-200 w-full placeholder-gray-500">
      </div>
    </form>
  </div>

  <!-- Grid Buku -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if (!empty($bukus)): ?>
      <?php foreach ($bukus as $buku): ?>
        <div class="bg-[#2A2A2A] rounded-lg shadow-md overflow-hidden relative border border-gray-700 hover:shadow-lg hover:scale-[1.02] transition-transform duration-200">
          <?php
            $images = $buku['images'] ?? [];
            $jsonImages = htmlspecialchars(json_encode($images), ENT_QUOTES, 'UTF-8');
            $firstImg = $images[0] ?? null;
          ?>
          <div class="relative card-media" data-images="<?= $jsonImages ?>" data-current-index="0">
            <?php if ($firstImg): ?>
              <img src="<?= base_url('uploads/' . $firstImg) ?>" alt="<?= esc($buku['judul']) ?>" class="w-full h-64 object-cover card-img">
            <?php else: ?>
              <img src="<?= base_url('assets/img/default-book.png') ?>" alt="No Image" class="w-full h-64 object-cover opacity-80 card-img">
            <?php endif; ?>

            <button class="absolute top-2 right-2 bg-white/80 p-2 rounded-full hover:bg-red-600 transition flex items-center justify-center btn-delete" title="Hapus" data-id="<?= esc($buku['id_buku']) ?>" data-judul="<?= esc($buku['judul']) ?>">
              <img src="<?= base_url('assets/icons/sampah.png') ?>" alt="hapus" class="w-4 h-4">
            </button>

            <!-- nav arrows (slightly above center) - enlarged for easier tapping -->
            <button class="absolute left-3 arrow-left btn-arrow rounded-full" style="top:42%; width:36px; height:36px; padding:0; display:flex; align-items:center; justify-content:center; background:#ffffff; border:0;" title="Sebelumnya">
              <img src="<?= base_url('assets/icons/panah_kiri.png') ?>" alt="kiri" class="w-9 h-9" style="display:block;">
            </button>
            <button class="absolute right-3 arrow-right btn-arrow rounded-full" style="top:42%; width:36px; height:36px; padding:0; display:flex; align-items:center; justify-content:center; background:#ffffff; border:0;" title="Selanjutnya">
              <img src="<?= base_url('assets/icons/panah_kanan.png') ?>" alt="kanan" class="w-9 h-9" style="display:block;">
            </button>
          </div>

          <div class="p-5">
            <h3 class="font-semibold text-lg text-white mb-1"><?= esc($buku['judul']) ?></h3>
            <p class="text-sm text-gray-400 mb-2"><?= esc($buku['penulis']) ?></p>
            <p class="text-sm text-gray-400 italic mb-3"><?= esc($buku['jenis'] ?? 'Tanpa Kategori') ?></p>

            <div class="flex justify-between mt-3">
              <button class="btn-edit bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1 rounded transition"
                data-id="<?= esc($buku['id_buku']) ?>"
                data-judul="<?= esc($buku['judul']) ?>"
                data-penulis="<?= esc($buku['penulis']) ?>"
                data-penerbit="<?= esc($buku['penerbit']) ?>"
                data-tahun="<?= esc($buku['tahun_terbit']) ?>"
                data-stok="<?= esc($buku['stok']) ?>"
                data-id_kategori="<?= esc($buku['id_kategori']) ?>"
                data-images='<?= $jsonImages ?>'
              >Edit</button>
              <button class="btn-pinjam bg-green-700 hover:bg-green-800 text-white px-4 py-1 rounded transition" data-id="<?= esc($buku['id_buku']) ?>" data-judul="<?= esc($buku['judul']) ?>" data-stok="<?= esc($buku['stok']) ?>">Pinjam</button>
            </div>
          </div>
        </div>
          <!-- Modal: Edit Buku (mirrors Add Buku but for editing) -->
          <div id="modalEditBuku" class="fixed inset-0 z-50 hidden items-center justify-center">
            <div id="modalEditOverlay" class="absolute inset-0 bg-black/60 pointer-events-auto" style="z-index:50;"></div>
            <div class="relative bg-[#ECECEC] w-11/12 md:w-2/5 rounded-lg shadow-lg mx-auto text-black" style="z-index:60; position:relative; max-height:80vh; overflow:visible;">
              <button id="closeEditBuku" class="absolute -top-3 -right-3 bg-[#1E1E1E] text-white rounded-full w-8 h-8 flex items-center justify-center" style="z-index:9999">X</button>
              <div class="p-6 overflow-y-auto" style="max-height:calc(80vh - 3rem);">
                <h2 class="text-2xl font-semibold text-[#111827] mb-4 text-center">Edit Buku</h2>
                <form id="formEditBuku" action="" method="post" enctype="multipart/form-data" class="space-y-4" novalidate>
                  <?= csrf_field() ?>
                  <input type="hidden" id="edit_id_buku" name="id_buku" value="">

                  <div>
                    <label for="edit_judul" class="block text-sm text-[#374151] mb-2">Judul Buku</label>
                    <input id="edit_judul" name="judul" type="text" placeholder="Masukkan Judul Buku" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
                    <div id="error-edit-judul" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Judul Buku</div>
                  </div>

                  <div>
                    <label for="edit_id_kategori" class="block text-sm text-[#374151] mb-2">Kategori Buku</label>
                    <select id="edit_id_kategori" name="id_kategori" class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-black">
                      <option value="">-- Pilih Kategori --</option>
                      <?php if (!empty($kategoris)): foreach ($kategoris as $kat): ?>
                        <option value="<?= esc($kat['id_kategori']) ?>"><?= esc($kat['jenis']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                    <div id="error-edit-kategori" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan ketegori</div>
                  </div>

                  <div>
                    <label for="edit_penulis" class="block text-sm text-[#374151] mb-2">Penulis</label>
                    <input id="edit_penulis" name="penulis" type="text" placeholder="Masukkan Nama Penulis" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
                    <div id="error-edit-penulis" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Nama Penulis</div>
                  </div>

                  <div>
                    <label for="edit_penerbit" class="block text-sm text-[#374151] mb-2">Penerbit</label>
                    <input id="edit_penerbit" name="penerbit" type="text" placeholder="Masukkan Nama Penerbit" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
                    <div id="error-edit-penerbit" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Nama Penerbit</div>
                  </div>

                  <div>
                    <label for="edit_tahun_terbit" class="block text-sm text-[#374151] mb-2">Tahun Terbit</label>
                    <input id="edit_tahun_terbit" name="tahun_terbit" type="number" placeholder="Masukkan Tahun Terbit Buku" min="1900" max="2100" step="1" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
                    <div id="error-edit-tahun" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Tahun Terbit Buku</div>
                  </div>

                  <div>
                    <label for="edit_stok" class="block text-sm text-[#374151] mb-2">Stok</label>
                    <input id="edit_stok" name="stok" type="number" min="0" placeholder="Masukkan Stok Buku" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
                    <div id="error-edit-stok" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Stok Buku</div>
                  </div>

                  <div>
                    <label class="block text-sm text-[#374151] mb-2">Gambar Sekarang</label>
                    <div id="editPreviewContainer" class="mt-3 flex gap-3 flex-wrap"></div>
                    <p class="text-xs text-gray-500 mt-2">Opsional: Pilih 3 file jika ingin mengganti gambar.</p>
                    <input id="edit_images" name="images[]" type="file" accept="image/*" class="w-full mb-2" multiple>
                  </div>

                <div class="flex justify-end gap-3 mt-4">
                  <button type="button" id="btnCancelEdit" class="px-6 py-2 rounded border border-gray-600 bg-white text-gray-700">Cancel</button>
                  <button type="submit" id="btnSubmitEdit" class="px-6 py-2 rounded bg-[#DBA100] text-white">Update</button>
                </div>
                </form>

                <div id="bukuEditFeedback" class="mt-3 text-sm text-center hidden"></div>
              </div>
            </div>
          </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-gray-400 italic col-span-full text-center py-10">Belum ada data buku</p>
    <?php endif; ?>
  </div>
</div>
<!-- Modal: Add Kategori -->
<div id="modalKategori" class="fixed inset-0 z-50 hidden items-center justify-center">
  <!-- overlay captures clicks so background is not interactive; ensure overlay sits behind modal content -->
  <div id="modalOverlay" class="absolute inset-0 bg-black/60 pointer-events-auto" style="z-index:50;"></div>
  <div class="relative bg-[#ECECEC] w-11/12 md:w-2/5 rounded-lg shadow-lg p-6 mx-auto text-black" style="z-index:60; position:relative;">
    <button id="closeModal" class="absolute -top-3 -right-3 bg-[#1E1E1E] text-white rounded-full w-8 h-8 flex items-center justify-center">X</button>
    <h2 class="text-2xl font-semibold text-[#111827] mb-4 text-center">Add Kategori</h2>
  <form id="formKategori" action="<?= base_url('backend/kategori/create') ?>" method="post" class="space-y-4" novalidate>
      <div>
        <label for="nama_kategori" class="block text-sm text-[#374151] mb-2">Masukkan Jenis Buku</label>
   <input id="nama_kategori" name="nama_kategori" type="text" placeholder="Contoh: Matematika, Fisika, Sejarah"
     class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
      </div>

      <div id="katFeedback" class="text-red-600 text-sm mt-1 hidden"></div>

      <div class="flex justify-end gap-3 mt-4">
        <button type="button" id="btnCancel" class="px-6 py-2 rounded border border-gray-600 bg-white text-gray-700">Cancel</button>
        <button type="submit" id="btnCreate" class="px-6 py-2 rounded bg-[#25622D] text-white">Create</button>
      </div>
    </form>
    
  </div>
</div>
  <!-- Modal: Add Buku -->
  <div id="modalAddBuku" class="fixed inset-0 z-50 hidden items-center justify-center">
  <!-- overlay captures clicks so background is not interactive; ensure overlay is behind the modal content -->
  <div id="modalAddOverlay" class="absolute inset-0 bg-black/60 pointer-events-auto" style="z-index:50;"></div>
  <div class="relative bg-[#ECECEC] w-11/12 md:w-2/5 rounded-lg shadow-lg mx-auto text-black" style="z-index:60; position:relative; max-height:80vh; overflow:visible;">
      <button id="closeAddBuku" class="absolute -top-3 -right-3 bg-[#1E1E1E] text-white rounded-full w-8 h-8 flex items-center justify-center" style="z-index:9999">X</button>
      <div class="p-6 overflow-y-auto" style="max-height:calc(80vh - 3rem);">
        <h2 class="text-2xl font-semibold text-[#111827] mb-4 text-center">Add Buku</h2>
  <form id="formAddBuku" action="<?= base_url('backend/buku/create') ?>" method="post" enctype="multipart/form-data" class="space-y-4" novalidate>
          <?= csrf_field() ?>
          <div>
            <label for="judul" class="block text-sm text-[#374151] mb-2">Judul Buku</label>
            <input id="judul" name="judul" type="text" placeholder="Masukkan Judul Buku" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
            <div id="error-judul" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Judul Buku</div>
          </div>

          <div>
            <label for="id_kategori" class="block text-sm text-[#374151] mb-2">Kategori Buku</label>
            <select id="id_kategori" name="id_kategori" class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-black">
              <option value="">-- Pilih Kategori --</option>
              <?php if (!empty($kategoris)): foreach ($kategoris as $kat): ?>
                <option value="<?= esc($kat['id_kategori']) ?>"><?= esc($kat['jenis']) ?></option>
              <?php endforeach; endif; ?>
            </select>
            <div id="error-kategori" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan ketegori</div>
          </div>

          <div>
            <label for="penulis" class="block text-sm text-[#374151] mb-2">Penulis</label>
            <input id="penulis" name="penulis" type="text" placeholder="Masukkan Nama Penulis" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
            <div id="error-penulis" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Nama Penulis</div>
          </div>

          <div>
            <label for="penerbit" class="block text-sm text-[#374151] mb-2">Penerbit</label>
            <input id="penerbit" name="penerbit" type="text" placeholder="Masukkan Nama Penerbit" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
            <div id="error-penerbit" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Nama Penerbit</div>
          </div>

          <div>
            <label for="tahun_terbit" class="block text-sm text-[#374151] mb-2">Tahun Terbit</label>
            <input id="tahun_terbit" name="tahun_terbit" type="number" placeholder="Masukkan Tahun Terbit Buku" min="1900" max="2100" step="1" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
            <div id="error-tahun" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Tahun Terbit Buku</div>
          </div>

          <div>
            <label for="stok" class="block text-sm text-[#374151] mb-2">Stok</label>
            <input id="stok" name="stok" type="number" min="1" placeholder="Masukkan Stok Buku" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
            <div id="error-stok" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Stok Buku</div>
          </div>

          <div>
            <label class="block text-sm text-[#374151] mb-2">Image (3 files required)</label>
            <input id="images" name="images[]" type="file" accept="image/*" class="w-full mb-2" multiple>

            <div id="previewContainer" class="mt-3 flex gap-3 flex-wrap"></div>
          </div>

          <div class="flex justify-end gap-3 mt-4">
            <button type="button" id="btnCancelAdd" class="px-6 py-2 rounded border border-gray-600 bg-white text-gray-700">Cancel</button>
            <button type="submit" id="btnSubmitAdd" class="px-6 py-2 rounded bg-[#25622D] text-white">Submit</button>
          </div>
        </form>

        <div id="bukuFeedback" class="mt-3 text-sm text-center hidden"></div>
      </div>
    </div>
  </div>
  <!-- Borrow Modal -->
  <div id="modalPinjam" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div id="modalPinjamOverlay" class="absolute inset-0 bg-black/60 pointer-events-auto" style="z-index:50;"></div>
    <div class="relative bg-[#ECECEC] w-11/12 md:w-2/5 rounded-lg shadow-lg p-6 mx-auto text-black" style="z-index:60; position:relative;">
      <button id="closePinjam" class="absolute -top-3 -right-3 bg-[#1E1E1E] text-white rounded-full w-8 h-8 flex items-center justify-center">X</button>
      <h2 class="text-2xl font-semibold text-[#111827] mb-4 text-center">Form Peminjaman</h2>
      <form id="formPinjam" action="<?= base_url('backend/peminjaman/create') ?>" method="post" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" id="pinjam_id_buku" name="buku_id" value="">

        <div>
          <label for="pinjam_judul" class="block text-sm text-[#374151] mb-2">Judul Buku</label>
          <input id="pinjam_judul" name="judul_display" type="text" disabled class="w-full px-4 py-3 rounded bg-gray-200 border border-gray-300 outline-none text-black placeholder-gray-400">
        </div>

        <div>
          <label for="nama_siswa" class="block text-sm text-[#374151] mb-2">Nama Lengkap</label>
          <input id="nama_siswa" name="nama_peminjam" type="text" placeholder="Masukkan Nama Lengkap" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
          <div id="error-nama" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Nama Lengkap</div>
        </div>

        <div>
          <label for="kelas" class="block text-sm text-[#374151] mb-2">Kelas</label>
          <input id="kelas" name="kelas" type="text" placeholder="Masukkan Kelas" class="w-full px-4 py-3 rounded bg-white border border-gray-300 outline-none text-black placeholder-gray-400">
          <div id="error-kelas" class="text-red-600 text-sm mt-1 hidden">Mohon masukkan Kelas</div>
        </div>

        <div>
          <label for="tanggal_kembali" class="block text-sm text-[#374151] mb-2">Batas Pengembalian (maks 7 hari)</label>
          <div class="flex gap-4">
            <div class="flex-1">
              <label class="text-xs text-gray-600">Tanggal</label>
              <input type="number" min="1" max="31" placeholder="DD" class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-black">
            </div>
            <div class="flex-1">  
              <label class="text-xs text-gray-600">Bulan</label>
              <select class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-black">
                <option value="1">Januari</option>
                <option value="2">Februari</option>
                <option value="3">Maret</option>
                <option value="4">April</option>
                <option value="5">Mei</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">Agustus</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
              </select>
            </div>
            <div class="flex-1">
              <label class="text-xs text-gray-600">Tahun</label>  
              <input type="number" min="2023" max="2100" placeholder="YYYY" value="2025" class="w-full px-4 py-3 rounded bg-white border border-gray-300 text-black">
            </div>
          </div>
          <input id="tanggal_kembali" name="tanggal_kembali" type="hidden">
          <div id="error-tanggal" class="text-red-600 text-sm mt-1 hidden"></div>
        </div>

        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-600">Stok tersedia: <span id="pinjam_stok" class="font-semibold">-</span></div>
          <div class="flex gap-3">
            <button type="button" id="btnCancelPinjam" class="px-6 py-2 rounded border border-gray-600 bg-white text-gray-700">Batal</button>
            <button type="submit" id="btnSubmitPinjam" class="px-6 py-2 rounded bg-[#25622D] text-white">Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Toast helper (global) -->
  <script>
    window.showToast = function(message, type){
      let container = document.getElementById('toastContainer');
      if(!container){
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.position = 'fixed';
        container.style.top = '16px';
        container.style.right = '16px';
        container.style.zIndex = 99999;
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '8px';
        document.body.appendChild(container);
      }

      const toast = document.createElement('div');
      toast.className = 'toast-item';
      toast.style.minWidth = '220px';
      toast.style.maxWidth = '360px';
      toast.style.padding = '10px 14px';
      toast.style.borderRadius = '8px';
      toast.style.boxShadow = '0 6px 18px rgba(0,0,0,0.25)';
      toast.style.color = '#ffffff';
      toast.style.fontSize = '14px';
      toast.style.display = 'flex';
      toast.style.alignItems = 'center';
      toast.style.justifyContent = 'space-between';

      if(type === 'success'){
        toast.style.background = '#16a34a';
      } else if(type === 'error'){
        toast.style.background = '#dc2626';
      } else {
        toast.style.background = '#374151';
      }

      const text = document.createElement('div');
      text.textContent = message;

      const btn = document.createElement('button');
      btn.textContent = '✕';
      btn.style.background = 'transparent';
      btn.style.border = 'none';
      btn.style.color = 'rgba(255,255,255,0.95)';
      btn.style.cursor = 'pointer';
      btn.style.marginLeft = '12px';

      btn.addEventListener('click', function(){ toast.remove(); });

      toast.appendChild(text);
      toast.appendChild(btn);
      container.appendChild(toast);

      setTimeout(()=> { try{ toast.remove(); }catch(e){} }, 4000);
    }
  </script>
  <!-- JS: Edit Buku modal open/populate/submit -->
  <script>
    (function(){
      const baseUrl = '<?= base_url() ?>';
      const editModal = document.getElementById('modalEditBuku');
      const closeEdit = document.getElementById('closeEditBuku');
      const cancelEdit = document.getElementById('btnCancelEdit');
      const form = document.getElementById('formEditBuku');
      const previewContainer = document.getElementById('editPreviewContainer');
      const imagesInput = document.getElementById('edit_images');

      function openEdit(){ editModal.classList.remove('hidden'); editModal.classList.add('flex'); }
      function closeEditModal(){ editModal.classList.remove('flex'); editModal.classList.add('hidden'); form.reset(); previewContainer.innerHTML = ''; }

      document.querySelectorAll('.btn-edit').forEach(function(btn){
        btn.addEventListener('click', function(){
          const id = btn.getAttribute('data-id');
          const judul = btn.getAttribute('data-judul') || '';
          const penulis = btn.getAttribute('data-penulis') || '';
          const penerbit = btn.getAttribute('data-penerbit') || '';
          const tahun = btn.getAttribute('data-tahun') || '';
          const stok = btn.getAttribute('data-stok') || '';
          const id_kategori = btn.getAttribute('data-id_kategori') || '';
          const imagesJson = btn.getAttribute('data-images') || '[]';

          // populate form
          document.getElementById('edit_id_buku').value = id;
          document.getElementById('edit_judul').value = judul;
          document.getElementById('edit_penulis').value = penulis;
          document.getElementById('edit_penerbit').value = penerbit;
          document.getElementById('edit_tahun_terbit').value = tahun;
          document.getElementById('edit_stok').value = stok;
          document.getElementById('edit_id_kategori').value = id_kategori;

          // set form action to update route
          form.action = baseUrl + '/backend/buku/update/' + id;

          // render existing images as previews
          previewContainer.innerHTML = '';
          try{
            const imgs = JSON.parse(imagesJson || '[]');
            imgs.forEach(function(u){
              const wrap = document.createElement('div'); wrap.style.width = '84px'; wrap.style.height = '84px'; wrap.className = 'relative';
              const img = document.createElement('img'); img.src = baseUrl + '/uploads/' + u; img.style.width='100%'; img.style.height='100%'; img.style.objectFit='cover'; img.style.borderRadius='6px';
              wrap.appendChild(img);
              previewContainer.appendChild(wrap);
            });
          }catch(e){ previewContainer.innerHTML = ''; }

          openEdit();
        });
      });

      if(closeEdit) closeEdit.addEventListener('click', closeEditModal);
      if(cancelEdit) cancelEdit.addEventListener('click', closeEditModal);

      // submit handler: similar validation to add but allows optional images
      if(form){
        form.addEventListener('submit', function(e){
          e.preventDefault();
          // basic validation
          const judul = document.getElementById('edit_judul').value.trim();
          const penulis = document.getElementById('edit_penulis').value.trim();
          const penerbit = document.getElementById('edit_penerbit').value.trim();
          const tahun = document.getElementById('edit_tahun_terbit').value.trim();
          const stok = document.getElementById('edit_stok').value.trim();

          if(!judul || !penulis || !penerbit || !tahun || stok === ''){
            showToast('Semua field kecuali gambar wajib diisi', 'error');
            return;
          }

          const submitBtn = document.getElementById('btnSubmitEdit');
          submitBtn.disabled = true; submitBtn.textContent = 'Updating...';

          const fd = new FormData(form);
          // append files if selected
          if(imagesInput && imagesInput.files && imagesInput.files.length > 0){
            for(const f of Array.from(imagesInput.files)) fd.append('images[]', f, f.name);
          }

          fetch(form.action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
              if(data && data.success){
                showToast(data.message || 'Buku berhasil diupdate', 'success');
                setTimeout(()=> { closeEditModal(); location.reload(); }, 800);
              } else {
                showToast((data && data.message) ? data.message : 'Gagal mengupdate buku', 'error');
                submitBtn.disabled = false; submitBtn.textContent = 'Update';
              }
            }).catch(err => {
              console.error(err);
              showToast('Gagal mengupdate buku (server error)', 'error');
              submitBtn.disabled = false; submitBtn.textContent = 'Update';
            });
        });
      }
    })();
  </script>

  <!-- JS: modal open/close & submit handling -->
  <script>
    (function(){
      const btnOpen = document.getElementById('btnOpenKategori');
      const modal = document.getElementById('modalKategori');
      const overlay = document.getElementById('modalOverlay');
      const closeBtn = document.getElementById('closeModal');
      const cancelBtn = document.getElementById('btnCancel');
      const form = document.getElementById('formKategori');
      const feedback = document.getElementById('katFeedback');

      function openModal(){
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        feedback.classList.add('hidden');
        const input = document.getElementById('nama_kategori');
        if(input) input.focus();
      }

      function closeModal(){
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        form.reset();
      }

  if(btnOpen) btnOpen.addEventListener('click', openModal);
  // overlay intentionally does NOT close the modal when clicked
  // clicking outside is disabled per UX request; overlay absorbs clicks
  if(closeBtn) closeBtn.addEventListener('click', closeModal);
  if(cancelBtn) cancelBtn.addEventListener('click', closeModal);

      // Handle submit with fetch; fallback to normal POST if fetch fails
      if(form){
        form.addEventListener('submit', function(e){
          e.preventDefault();
          const url = form.action || window.location.href;
          const nama = document.getElementById('nama_kategori').value.trim();
          if(!nama){
            feedback.textContent = 'Nama kategori wajib diisi.';
            feedback.classList.remove('hidden');
            feedback.classList.add('text-red-600');
            return;
          }

          // optimistic UI: disable button
          const submitBtn = document.getElementById('btnCreate');
          submitBtn.disabled = true; submitBtn.textContent = 'Creating...';

          fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ nama_kategori: nama })
          }).then(resp => {
            // always try to parse JSON, even for 4xx/5xx so we can show server messages
            return resp.json().then(data => ({ ok: resp.ok, status: resp.status, data })).catch(() => ({ ok: resp.ok, status: resp.status, data: {} }));
          }).then(({ok, data, status}) => {
            if (ok) {
              showToast((data && data.message) ? data.message : 'Data berhasil ditambahkan', 'success');
              setTimeout(() => { closeModal(); location.reload(); }, 900);
            } else {
              // server returned 4xx/5xx, show its message if present
              showToast((data && data.message) ? data.message : 'Data gagal ditambahkan', 'error');
              submitBtn.disabled = false; submitBtn.textContent = 'Create';
            }
          }).catch(err => {
            console.error('Fetch error:', err);
            showToast('Data gagal ditambahkan (server error)', 'error');
            submitBtn.disabled = false; submitBtn.textContent = 'Create';
          });
        });
      }
    })();
  </script>
  <!-- Delete confirmation modal -->
  <div id="modalDelete" class="fixed inset-0 z-60 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60" style="z-index:60;"></div>
    <div class="bg-white rounded-lg p-6 w-11/12 md:w-96" style="z-index:70; position:relative; color:#000000;">
      <h3 class="text-lg font-semibold mb-3">Konfirmasi Hapus</h3>
      <p id="deleteMessage" class="mb-4">Apakah Anda yakin ingin menghapus buku ini?</p>
      <div class="flex justify-end gap-3">
        <button id="cancelDelete" type="button" class="px-4 py-2 rounded border">Batal</button>
        <form id="deleteForm" method="post" action="" style="display:inline-block">
          <?= csrf_field() ?>
          <button id="confirmDelete" type="submit" class="px-4 py-2 rounded bg-red-600 text-white">Hapus</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const baseUrl = '<?= base_url() ?>';

      // Carousel: handle arrow clicks per card
      document.querySelectorAll('.card-media').forEach(function(card){
        const imgEl = card.querySelector('.card-img');
        let images = [];
        try { images = JSON.parse(card.getAttribute('data-images') || '[]'); } catch(e){ images = []; }
        card.dataset.currentIndex = card.dataset.currentIndex || 0;

        const updateImage = function(idx){
          idx = (idx + images.length) % images.length;
          card.dataset.currentIndex = idx;
          if(images.length && imgEl){ imgEl.src = baseUrl + '/uploads/' + images[idx]; }
        };

        const left = card.querySelector('.arrow-left');
        const right = card.querySelector('.arrow-right');
        if(left){ left.addEventListener('click', function(e){ e.preventDefault(); const i = parseInt(card.dataset.currentIndex || 0); updateImage(i - 1); }); }
        if(right){ right.addEventListener('click', function(e){ e.preventDefault(); const i = parseInt(card.dataset.currentIndex || 0); updateImage(i + 1); }); }
      });

      // Delete modal handling
      const modalDelete = document.getElementById('modalDelete');
      const deleteForm = document.getElementById('deleteForm');
      const deleteMessage = document.getElementById('deleteMessage');
      const cancelDelete = document.getElementById('cancelDelete');

      document.querySelectorAll('.btn-delete').forEach(function(btn){
        btn.addEventListener('click', function(){
          const id = btn.getAttribute('data-id');
          const judul = btn.getAttribute('data-judul') || 'buku ini';
          // set form action
          deleteForm.action = baseUrl + '/backend/buku/delete/' + id;
          deleteMessage.textContent = 'Apakah Anda yakin ingin menghapus "' + judul + '"?';
          modalDelete.classList.remove('hidden'); modalDelete.classList.add('flex');
          // store last clicked delete button so we can remove its card after successful delete
          window.__lastDeleteBtn = btn;
        });
      });

      if(cancelDelete){ cancelDelete.addEventListener('click', function(){ modalDelete.classList.remove('flex'); modalDelete.classList.add('hidden'); }); }

      // intercept delete form submit to use AJAX and show toast
      if(deleteForm){
        deleteForm.addEventListener('submit', function(e){
          e.preventDefault();
          const url = deleteForm.action;
          const fd = new FormData(deleteForm);
          fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
              if(data && data.success){
                showToast(data.message || 'Data berhasil dihapus', 'success');
                // hide modal
                modalDelete.classList.remove('flex'); modalDelete.classList.add('hidden');
                // remove card from DOM if we have reference
                try{
                  if(window.__lastDeleteBtn){
                    const card = window.__lastDeleteBtn.closest('.rounded-lg');
                    if(card) card.remove();
                    window.__lastDeleteBtn = null;
                  } else {
                    // fallback: reload
                    setTimeout(()=> location.reload(), 600);
                  }
                }catch(err){ setTimeout(()=> location.reload(), 600); }
              } else {
                showToast((data && data.message) ? data.message : 'Data gagal dihapus', 'error');
              }
            }).catch(err => {
              console.error(err);
              showToast('Data gagal dihapus (server error)', 'error');
            });
        });
      }
    })();
  </script>
  <script>
    (function(){
      const btnAdd = document.getElementById('btnAddBuku');
      const modal = document.getElementById('modalAddBuku');
      const overlay = document.getElementById('modalAddOverlay');
      const closeBtn = document.getElementById('closeAddBuku');
      const cancelBtn = document.getElementById('btnCancelAdd');
      const form = document.getElementById('formAddBuku');
      const feedback = document.getElementById('bukuFeedback');

  // persistent DataTransfer must be declared before submit handler so
  // it's available when counting files. Declare it here (top of scope).
  let dtImages = new DataTransfer();

      function openAdd(){
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        feedback.classList.add('hidden');
        const first = document.getElementById('judul'); if(first) first.focus();
      }
      function closeAdd(){
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        form.reset();
      }

  if(btnAdd) btnAdd.addEventListener('click', openAdd);
  // overlay intentionally does NOT close the add modal when clicked
  // this prevents accidental dismissal and blocks clicks to the page
  if(closeBtn) closeBtn.addEventListener('click', closeAdd);
  if(cancelBtn) cancelBtn.addEventListener('click', closeAdd);

      if(form){
        // helper to clear all inline field errors
        function clearFieldErrors(){
          ['judul','kategori','penulis','penerbit','tahun','stok'].forEach(id => {
            const el = document.getElementById('error-' + id);
            if(el) el.classList.add('hidden');
          });
        }

        form.addEventListener('submit', function(e){
          e.preventDefault();

          clearFieldErrors();
          feedback.classList.add('hidden');

          // client-side per-field validation
          let invalid = false;
          const judulEl = document.getElementById('judul');
          const katEl = document.getElementById('id_kategori');
          const penulisEl = document.getElementById('penulis');
          const penerbitEl = document.getElementById('penerbit');
          const tahunEl = document.getElementById('tahun_terbit');
          const stokEl = document.getElementById('stok');

          if(!judulEl || judulEl.value.trim() === ''){
            const e = document.getElementById('error-judul'); if(e) e.classList.remove('hidden'); invalid = true;
          }
          if(!katEl || katEl.value === ''){
            const e = document.getElementById('error-kategori'); if(e) e.classList.remove('hidden'); invalid = true;
          }
          if(!penulisEl || penulisEl.value.trim() === ''){
            const e = document.getElementById('error-penulis'); if(e) e.classList.remove('hidden'); invalid = true;
          }
          if(!penerbitEl || penerbitEl.value.trim() === ''){
            const e = document.getElementById('error-penerbit'); if(e) e.classList.remove('hidden'); invalid = true;
          }
          if(!tahunEl || tahunEl.value.trim() === ''){
            const e = document.getElementById('error-tahun'); if(e) e.classList.remove('hidden'); invalid = true;
          }
          if(!stokEl || stokEl.value.trim() === ''){
            const e = document.getElementById('error-stok'); if(e) e.classList.remove('hidden'); invalid = true;
          }

          if(invalid){
            // focus first invalid field
            const firstErr = document.querySelector('#error-judul:not(.hidden),#error-kategori:not(.hidden),#error-penulis:not(.hidden),#error-penerbit:not(.hidden),#error-tahun:not(.hidden),#error-stok:not(.hidden)');
            if(firstErr){
              const map = { 'error-judul':'judul','error-kategori':'id_kategori','error-penulis':'penulis','error-penerbit':'penerbit','error-tahun':'tahun_terbit','error-stok':'stok' };
              const target = document.getElementById(map[firstErr.id]);
              if(target) target.focus();
            }
            return;
          }

          // DEBUG: log current file lists to troubleshoot why selectedCount may be 0
          try{
            console.debug('DEBUG submit: dtImages.files.length=', dtImages && dtImages.files ? dtImages.files.length : 'no-dtImages');
            const imgs = document.getElementById('images');
            console.debug('DEBUG submit: imagesInput.files.length=', imgs && imgs.files ? imgs.files.length : 'no-input');
            if(dtImages && dtImages.files){ for(const f of Array.from(dtImages.files)){ console.debug('DEBUG dt file:', f.name, f.size, f.lastModified); } }
            if(imgs && imgs.files){ for(const f of Array.from(imgs.files)){ console.debug('DEBUG input file:', f.name, f.size, f.lastModified); } }
          }catch(err){ console.debug('DEBUG submit logging error', err); }

          // ensure at least 3 files are selected (count from persistent dtImages)
          const imagesInputLocal = document.getElementById('images');
          const previewEl = document.getElementById('previewContainer');
          const dtLen = (typeof dtImages !== 'undefined' && dtImages && dtImages.files) ? dtImages.files.length : 0;
          const inputLen = (imagesInputLocal && imagesInputLocal.files) ? imagesInputLocal.files.length : 0;
          const previewLen = (previewEl && previewEl.querySelectorAll) ? previewEl.querySelectorAll('img').length : 0;
          const selectedCount = Math.max(dtLen, inputLen, previewLen);
          if (selectedCount < 3) {
            const kurang = 3 - selectedCount;
            feedback.classList.remove('hidden'); feedback.classList.add('text-red-600');
            feedback.textContent = 'Gambar Kurang : butuh ' + kurang + ' gambar';
            return;
          }

          const submitBtn = document.getElementById('btnSubmitAdd');
          submitBtn.disabled = true; submitBtn.textContent = 'Saving...';

          // build FormData and append files from dtImages so files are actually sent
          const fd = new FormData(form);
          if (typeof dtImages !== 'undefined' && dtImages && dtImages.files && dtImages.files.length > 0) {
            for (const f of Array.from(dtImages.files)) {
              fd.append('images[]', f, f.name);
            }
          } else if (imagesInputLocal && imagesInputLocal.files && imagesInputLocal.files.length > 0) {
            // fallback to native input files if dtImages is empty
            for (const f of Array.from(imagesInputLocal.files)) {
              fd.append('images[]', f, f.name);
            }
          }

          fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          }).then(r => r.json()).then(data => {
            if (data && data.success) {
              showToast(data.message || 'Data berhasil ditambahkan', 'success');
              setTimeout(() => { closeAdd(); location.reload(); }, 800);
            } else {
              showToast((data && data.message) ? data.message : 'Data gagal ditambahkan', 'error');
              submitBtn.disabled = false; submitBtn.textContent = 'Submit';
            }
          }).catch(err => {
            console.error(err);
            showToast('Data gagal ditambahkan (server error)', 'error');
            submitBtn.disabled = false; submitBtn.textContent = 'Submit';
          });
        });

        // clear individual errors when user types/changes
        const mappings = [
          ['judul','error-judul','input'],
          ['id_kategori','error-kategori','change'],
          ['penulis','error-penulis','input'],
          ['penerbit','error-penerbit','input'],
          ['tahun_terbit','error-tahun','input'],
          ['stok','error-stok','input']
        ];
        mappings.forEach(([fieldId, errId, ev]) => {
          const fld = document.getElementById(fieldId);
          const err = document.getElementById(errId);
          if(fld && err){ fld.addEventListener(ev, () => { err.classList.add('hidden'); feedback.classList.add('hidden'); }); }
        });
      }

      // Image preview handling for a multi-file input with incremental adds,
      // duplicate checks and max(3) enforcement.
      const previewContainer = document.getElementById('previewContainer');
      const imagesInput = document.getElementById('images');

  // Use a persistent DataTransfer to keep files across separate selections

      function showImageFeedback(msg){
        if(!feedback) return;
        feedback.classList.remove('hidden');
        feedback.classList.add('text-red-600');
        feedback.textContent = msg;
      }
      function clearImageFeedback(){
        if(!feedback) return;
        feedback.classList.add('hidden');
        feedback.classList.remove('text-red-600');
        feedback.classList.remove('text-green-600');
        feedback.textContent = '';
      }

      function renderPreviews(fileList){
        previewContainer.innerHTML = '';
        fileList.forEach((file, idx) => {
          const wrap = document.createElement('div');
          wrap.id = 'preview-' + idx;
          wrap.className = 'relative';
          wrap.style.width = '84px';
          wrap.style.height = '84px';

          const img = document.createElement('img');
          img.style.width = '100%';
          img.style.height = '100%';
          img.style.objectFit = 'cover';
          img.style.borderRadius = '6px';
          img.src = URL.createObjectURL(file);

          const btn = document.createElement('button');
          btn.type = 'button';
          btn.textContent = '✕';
          btn.title = 'Hapus gambar';
          btn.className = 'absolute';
          btn.style.top = '-8px';
          btn.style.right = '-8px';
          btn.style.background = '#271211ff';
          btn.style.color = '#fff';
          btn.style.border = 'none';
          btn.style.width = '22px';
          btn.style.height = '22px';
          btn.style.borderRadius = '999px';
          btn.style.cursor = 'pointer';

          btn.addEventListener('click', function(){
            // remove this file from dtImages and update the input
            const newDt = new DataTransfer();
            const current = Array.from(dtImages.files);
            current.forEach((f, i) => { if (i !== idx) newDt.items.add(f); });
            dtImages = newDt;
            imagesInput.files = dtImages.files;
            renderPreviews(Array.from(dtImages.files));
            clearImageFeedback();
          });

          wrap.appendChild(img);
          wrap.appendChild(btn);
          previewContainer.appendChild(wrap);
        });
      }

      if(imagesInput){
        // When the user selects files, append them to the persistent DataTransfer
        imagesInput.addEventListener('change', function(e){
          const newFiles = Array.from(e.target.files || []);

          // If already at max, show message and ignore new selection
          if (dtImages.files.length >= 3) {
            showImageFeedback('Jumlah gambar maksimum');
            // keep native input value so files remain available as fallback
            return;
          }

          let addedAny = false;
          for (const file of newFiles) {
            // detect duplicates by name+size+lastModified
            const isDup = Array.from(dtImages.files).some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);
            if (isDup) {
              showImageFeedback('Gambar yang dinputkan sama');
              continue;
            }

            if (dtImages.files.length >= 3) {
              showImageFeedback('Jumlah gambar maksimum');
              break;
            }

            dtImages.items.add(file);
            addedAny = true;
          }

          imagesInput.files = dtImages.files;
          renderPreviews(Array.from(dtImages.files));
          console.debug('DEBUG change: dtImages.files.length=', dtImages.files.length);
          // keep native input value as a fallback in case dtImages is not accessible

          if (addedAny) clearImageFeedback();
        });
      }
    })();
  </script>
  <script>
    (function(){
      const baseUrl = '<?= base_url() ?>';
      const modal = document.getElementById('modalPinjam');
      const overlay = document.getElementById('modalPinjamOverlay');
      const closeBtn = document.getElementById('closePinjam');
      const cancelBtn = document.getElementById('btnCancelPinjam');
      const form = document.getElementById('formPinjam');
      const judulField = document.getElementById('pinjam_judul');
      const idField = document.getElementById('pinjam_id_buku');
      const stokSpan = document.getElementById('pinjam_stok');
      const errNama = document.getElementById('error-nama');
      const errKelas = document.getElementById('error-kelas');
      const errTanggal = document.getElementById('error-tanggal');

      function openModal(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
      function closeModal(){ modal.classList.remove('flex'); modal.classList.add('hidden'); form.reset(); }

      document.querySelectorAll('.btn-pinjam').forEach(function(btn){
        btn.addEventListener('click', function(){
          const id = this.getAttribute('data-id');
          const judul = this.getAttribute('data-judul');
          const stok = parseInt(this.getAttribute('data-stok') || '0', 10);

          idField.value = id;
          judulField.value = judul;
          stokSpan.textContent = stok;
          
          // Set today as default in date inputs
          setDefaultDate();
          
          // clear errors
          [errNama, errKelas, errTanggal].forEach(e => e && e.classList.add('hidden'));

          // store reference to the originating button so we can update its data-stok after success
          window.__lastPinjamButton = this;

          openModal();
        });
      });

      if(closeBtn) closeBtn.addEventListener('click', closeModal);
      if(cancelBtn) cancelBtn.addEventListener('click', closeModal);

      // validate date on change
      const tanggalInput = document.getElementById('tanggal_kembali');
      if(tanggalInput){
        tanggalInput.addEventListener('change', function(){
          errTanggal.classList.add('hidden');
          const v = this.value;
          if(!v) return;
          const today = new Date();
          today.setHours(0,0,0,0);
          const sel = new Date(v + 'T00:00:00');
          const diff = Math.round((sel - today) / (1000*60*60*24));
          if(diff > 7){ errTanggal.classList.remove('hidden'); }
        });
      }

      // Set up date inputs
      const dateInputs = {
        day: modal.querySelector('input[placeholder="DD"]'),
        month: modal.querySelector('select'),
        year: modal.querySelector('input[placeholder="YYYY"]')
      };

      // Initialize current date as default
      const setDefaultDate = () => {
        const today = new Date();
        if (dateInputs.day) dateInputs.day.value = today.getDate();
        if (dateInputs.month) dateInputs.month.value = today.getMonth() + 1;
        if (dateInputs.year) dateInputs.year.value = today.getFullYear();
      };

      if(form){
        form.addEventListener('submit', function(e){
          e.preventDefault();
          // clear
          [errNama, errKelas, errTanggal].forEach(e => e && e.classList.add('hidden'));

          const id = idField.value;
          const nama = (document.getElementById('nama_siswa') || {}).value || '';
          const kelas = (document.getElementById('kelas') || {}).value || '';
          
          // Get date from separate inputs
          const day = dateInputs.day ? dateInputs.day.value : '';
          const month = dateInputs.month ? dateInputs.month.value : '';
          const year = dateInputs.year ? dateInputs.year.value : '';
          
          // Validate required fields
          let invalid = false;
          if(!nama.trim()){ errNama.classList.remove('hidden'); invalid = true; }
          if(!kelas.trim()){ errKelas.classList.remove('hidden'); invalid = true; }
          if(!day || !month || !year){ 
            errTanggal.textContent = 'Mohon lengkapi tanggal pengembalian';
            errTanggal.classList.remove('hidden'); 
            invalid = true; 
          }

          // Construct and validate date
          const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
          const selectedDate = new Date(dateStr + 'T00:00:00');
          
          // Set the hidden input value
          document.getElementById('tanggal_kembali').value = dateStr;

          const today = new Date();
          today.setHours(0,0,0,0);
          
          // Validate date logic
          if(selectedDate < today){
            errTanggal.textContent = 'Tanggal tidak valid';
            errTanggal.classList.remove('hidden');
            invalid = true;
          } else {
            const diff = Math.round((selectedDate - today) / (1000 * 60 * 60 * 24));
            if(diff > 7){
              errTanggal.textContent = 'Melebihi batas peminjaman';
              errTanggal.classList.remove('hidden');
              invalid = true;
            }
          }

          if(invalid) return;

          // check stock from displayed span
          const stokAvail = parseInt(stokSpan.textContent || '0', 10);
          if(stokAvail <= 0){ showToast('Stok tidak cukup', 'error'); return; }

          // submit
          const submitBtn = document.getElementById('btnSubmitPinjam');
          submitBtn.disabled = true; submitBtn.textContent = 'Memproses...';

          const fd = new FormData(form);

          fetch(baseUrl + '/backend/peminjaman/create', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
              if(data && data.success){
                showToast(data.message || 'Peminjaman berhasil', 'success');
                // decrement stok in originating button if present
                try{
                  if(window.__lastPinjamButton){
                    const btn = window.__lastPinjamButton;
                    const cur = parseInt(btn.getAttribute('data-stok') || '0', 10);
                    const next = Math.max(0, cur - 1);
                    btn.setAttribute('data-stok', next);
                    // update displayed stok in modal
                    stokSpan.textContent = next;
                    // optionally update any visible stock element (not present currently)
                    window.__lastPinjamButton = null;
                  }
                }catch(e){/*ignore*/}

                setTimeout(()=> { closeModal(); submitBtn.disabled = false; submitBtn.textContent = 'Submit'; location.reload(); }, 800);
              } else {
                // Clear previous errors
                [errNama, errKelas, errTanggal].forEach(e => e && e.classList.add('hidden'));

                // Display specific errors from the backend response
                let displayErrorMessage = (data && data.message) ? data.message : 'gagal menambahkan data';
                if (data && data.errors) {
                  if (data.errors.general) {
                    displayErrorMessage = data.errors.general;
                  }
                  if (data.errors.tanggal_kembali) {
                    errTanggal.textContent = data.errors.tanggal_kembali;
                    errTanggal.classList.remove('hidden');
                    displayErrorMessage = data.errors.tanggal_kembali; // Prioritize specific date error for toast
                  }
                  if (data.errors.buku_id) {
                    displayErrorMessage = data.errors.buku_id;
                  }
                  if (data.errors.system) {
                    displayErrorMessage = data.errors.system;
                  }
                }
                showToast(displayErrorMessage, 'error');
                submitBtn.disabled = false; submitBtn.textContent = 'Submit';
              }
            }).catch(err => {
              console.error(err);
              showToast('gagal menambahkan data', 'error');
              submitBtn.disabled = false; submitBtn.textContent = 'Submit';
            });
        });
      }
    })();
  </script>
<?= $this->endSection() ?>
