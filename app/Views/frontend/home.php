<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Home
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="flex flex-col-reverse md:flex-row justify-between items-center px-8 md:px-20 py-6 bg-green-50 home-hero">
    <div class="max-w-xl text-center md:text-left mt-8 md:mt-0">
        <h1 class="text-4xl md:text-5xl font-bold text-green-700 leading-snug mb-4">
            Sistem Informasi <br> Perpustakaan
        </h1>
        <p class="text-gray-600 mb-8">
            “Cari, pinjam, dan kelola buku dengan mudah, cepat, cerdas, gratis!”
        </p>
        <a href="<?= base_url('/list-buku') ?>" 
           class="inline-block border-2 border-green-700 text-green-700 px-8 py-2 rounded-full font-semibold hover:bg-green-700 hover:text-white transition">
           MULAI →
        </a>
    </div>

    <div class="max-w-md">
        <img src="<?= base_url('assets/img/library_illustration.png') ?>" 
             alt="Ilustrasi Perpustakaan" 
             class="rounded-full shadow-md">
    </div>
</section>
<?= $this->endSection() ?>
