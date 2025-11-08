<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> | SITAKAN Backend</title>
    <link rel="icon" href="<?= base_url('assets/img/logo.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#1E1E1E] text-gray-200 font-poppins">
    <div class="flex h-screen overflow-hidden">
        <?= $this->include('backend/header') ?>
        
        <main class="flex-1 overflow-y-auto ml-64 p-8 bg-[#1E1E1E]">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script>
    function confirmLogout(e) {
      if(e) e.preventDefault();
      Swal.fire({
        title: 'Konfirmasi Logout',
        text: "Apakah anda yakin ingin keluar?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#25622D',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Logout Berhasil',
            text: 'Anda akan dialihkan dalam beberapa saat...',
            icon: 'success',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
          }).then(() => {
            window.location.href = '<?= base_url('auth/logout') ?>';
          });
        }
      });
    }
    </script>
  <?= $this->renderSection('scripts') ?>
</body>
</html>
