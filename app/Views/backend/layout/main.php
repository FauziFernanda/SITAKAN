<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $this->renderSection('title') ?> | SITAKAN Backend</title>
  <link rel="icon" href="<?= base_url('assets/img/logo.png') ?>">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-[#1E1E1E] text-gray-200 font-poppins h-screen overflow-hidden">

  <div class="backend-layout flex h-screen">
    <!-- Sidebar -->
    <?= $this->include('backend/header') ?>

    <!-- Konten utama -->
    <main class="backend-content flex-1 overflow-y-auto p-8 bg-[#1E1E1E]">
      <?= $this->renderSection('content') ?>
    </main>
  </div>
</div>

  <?= $this->renderSection('scripts') ?>

</body>
</html>
