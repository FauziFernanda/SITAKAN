<aside class="fixed left-0 top-0 bottom-0 w-64 bg-[#181818] text-gray-300 flex flex-col justify-between shadow-lg">
  <div>
    <!-- Logo SITAKAN -->
    <div class="flex items-center gap-3 p-6 border-b border-gray-700">
      <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" class="h-7">
      <h1 class="text-lg font-bold tracking-wide text-white">SITAKAN</h1>
    </div>

    <!-- Menu Navigasi -->
      <ul class="mt-4 space-y-1">
      <li>
        <a href="<?= base_url('backend/home') ?>" class="sidebar-link <?= (uri_string() == 'backend/home') ? 'active' : '' ?>">
          <img src="<?= base_url('assets/icons/home.png') ?>" alt="Home" class="w-5 h-5 mr-3">
          Home
        </a>
      </li>
      <li>
        <a href="<?= base_url('backend/buku_list') ?>" class="sidebar-link <?= (uri_string() == 'backend/buku_list') ? 'active' : '' ?>">
          <img src="<?= base_url('assets/icons/list_buku.png') ?>" alt="List Buku" class="w-5 h-5 mr-3">
        List Buku
        </a>

      </li>
      <li>
        <a href="<?= base_url('backend/peminjaman') ?>" class="sidebar-link <?= (uri_string() == 'backend/peminjaman') ? 'active' : '' ?>">
          <img src="<?= base_url('assets/icons/list_peminjaman.png') ?>" alt="List Peminjaman" class="w-5 h-5 mr-3">
          List Peminjaman
        </a>
      </li>
      <li>
        <a href="<?= base_url('backend/denda') ?>" class="sidebar-link <?= (uri_string() == 'backend/denda') ? 'active' : '' ?>">
          <img src="<?= base_url('assets/icons/list_denda.png') ?>" alt="List Denda" class="w-5 h-5 mr-3">
          List Denda
        </a>
      </li>
      <li>
        <a href="<?= base_url('backend/riwayat') ?>" class="sidebar-link <?= (uri_string() == 'backend/riwayat') ? 'active' : '' ?>">
          <img src="<?= base_url('assets/icons/riwayat.png') ?>" alt="Riwayat" class="w-5 h-5 mr-3">
          Riwayat
        </a>
      </li>
      <?php if(session()->get('role') === 'admin'): ?>
      <li>
        <a href="<?= base_url('backend/register') ?>" class="sidebar-link <?= (uri_string() == 'backend/register') ? 'active' : '' ?>">
          <img src="<?= base_url('assets/icons/register.png') ?>" alt="Register" class="w-6 h-9 mr-2">
          Register
        </a>
      </li>
      <?php endif; ?>
    </ul>
  </div>

  <!-- Bagian bawah sidebar -->
  <div class="p-6 border-t border-gray-700">
    <div class="flex items-center gap-3 mb-4">
      <img src="<?= base_url('assets/icons/profile.png') ?>" alt="Profile" class="w-6 h-6">
      <span class="text-sm font-medium text-white"><?= session()->get('nama') ?? 'User' ?></span>
    </div>

    <a href="#" onclick="confirmLogout(event)" class="logout-btn flex items-center justify-center gap-2">
      <img src="<?= base_url('assets/icons/logout.png') ?>" alt="Logout" class="w-5 h-5">
      Log out
    </a>
  </div>
</aside>
