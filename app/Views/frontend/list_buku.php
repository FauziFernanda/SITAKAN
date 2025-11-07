<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Daftar Buku
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="px-0 py-8">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-6">
            <h1 class="text-4xl md:text-5xl font-bold text-green-700">Daftar Buku</h1>
            <p class="text-gray-600 mt-2">Ayo!!! cari buku yang kamu butuhkan</p>
        </div>

        <div class="mb-8">
            <form action="<?= base_url('/list-buku') ?>" method="get">
                <div class="relative w-full">
                    <img src="<?= base_url('assets/icons/search.png') ?>" alt="Search" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 opacity-70">
                    <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search...." 
                           class="w-full pl-14 pr-6 py-4 rounded-xl border border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-200 text-lg">
                </div>
            </form>
        </div>
    </div>

    <!-- Full-width green background -->
    <div class="full-bleed-green py-8">
        <div class="max-w-6xl mx-auto px-6">
            <?php if (!empty($bukus) && is_array($bukus)): ?>
                <div class="books-grid">
                    <?php foreach ($bukus as $b): ?>
                        <div class="book-card">
                            <?php
                                $cover = $b['cover'] ?? '';
                                if (empty($cover)) {
                                    $cover = base_url('assets/img/buku.png');
                                }
                            ?>
                            <div class="cover-box">
                                <img src="<?= esc($cover) ?>" alt="Cover">
                            </div>
                            <div class="book-title"><?= esc($b['judul']) ?></div>
                            <div class="book-meta">
                                <div class="book-stock">Stok : <?= esc($b['stok'] ?? '0') ?></div>
                                <a href="<?= base_url('buku/' . ($b['id_buku'] ?? '')) ?>" class="btn-details">Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-gray-600">Tidak ada buku ditemukan.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
