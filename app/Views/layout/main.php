<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> | SITAKAN</title>
    
    <link rel="icon" href="<?= base_url('assets/img/logo.png') ?>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-white text-gray-800 font-poppins">

    <!-- Header (Sticky + Shadow) -->
    <?php $uri = service('uri'); ?>
    <header class="fixed top-0 left-0 w-full bg-gray-50 shadow-md z-50 flex justify-between items-center px-6 md:px-10 py-6 transition-all duration-300">
        <div class="flex items-center space-x-4">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo SITAKAN" class="h-12">
            <h2 class="text-green-700 text-3xl font-bold tracking-wide">SITAKAN</h2>
        </div>

        <nav class="hidden md:flex space-x-12 text-lg">
            <a href="<?= base_url('/') ?>" 
               class="<?= ($uri->getSegment(1) == '' ? 'active' : '') ?> hover:text-green-700 transition">Home</a>
            <a href="<?= base_url('/list-buku') ?>" 
               class="<?= ($uri->getSegment(1) == 'list-buku' ? 'active' : '') ?> hover:text-green-700 transition">List Buku</a>
            <a href="<?= base_url('/peraturan') ?>" 
               class="<?= ($uri->getSegment(1) == 'peraturan' ? 'active' : '') ?> hover:text-green-700 transition">Peraturan</a>
            <a href="<?= base_url('/jadwal') ?>" 
               class="<?= ($uri->getSegment(1) == 'jadwal' ? 'active' : '') ?> hover:text-green-700 transition">Jadwal</a>
        </nav>

        <a href="<?= base_url('/login') ?>" 
           class="bg-green-700 text-white px-8 py-3 rounded-full font-semibold hover:bg-green-800 shadow-sm transition">
           Login
        </a>
    </header>

    <!-- Spacer supaya konten tidak ketutupan header -->
    <div class="h-28"></div>

    <!-- Konten Halaman -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-emerald-700 text-white mt-16">
        <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">
            
            <div>
                <h3 class="text-lg font-semibold mb-4 text-center md:text-left">Hubungi Kami:</h3>
                <ul class="space-y-4"> 
                    <li class="flex items-center space-x-4">
                        <img src="<?= base_url('assets/icons/email.png') ?>" alt="Email" class="w-6 h-6">
                        <span>fauzifernanda1407@gmail.com</span>
                    </li>
                    <li class="flex items-center space-x-4">
                        <img src="<?= base_url('assets/icons/phone.png') ?>" alt="Phone" class="w-6 h-6">
                        <span>085271232732</span>
                    </li>
                    <li class="flex items-center space-x-4">
                        <img src="<?= base_url('assets/icons/instagram.png') ?>" alt="Instagram" class="w-6 h-6">
                        <span>@Fz_Frnd14</span>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4 text-center md:text-left">Fitur</h3>
                <ul class="space-y-2">
                    <li>Search Buku</li>
                    <li>Peminjaman</li>
                    <li>Kalkulasi Denda</li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4 text-center md:text-left">Lokasi</h3>
                <p>SDN 11 Taratak,<br>Surian</p>
            </div>
        </div>

        <hr class="border-t border-white/30 mx-6">

        <div class="text-center text-xs py-4 text-white/80 tracking-wider">
            © <?= date('Y') ?> | SISTEM MANAJEMEN PERPUSTAKAAN
        </div>
    </footer>

</body>
</html>
