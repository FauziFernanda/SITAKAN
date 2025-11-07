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
                                // prefer the normalized absolute URL if controller provided it
                                if (!empty($b['cover_url_full'])) {
                                    $coverSrc = $b['cover_url_full'];
                                } elseif (!empty($b['cover_url'])) {
                                    // build with uploads/ prefix to match where files are saved
                                    $coverSrc = rtrim(base_url(), '/') . '/uploads/' . ltrim($b['cover_url'], '/');
                                } else {
                                    $coverSrc = base_url('assets/img/buku.png');
                                }
                                // safe base64-encoded JSON payload to avoid quoting/escaping issues in attributes
                                $bookJson = json_encode($b, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                                $bookJsonB64 = base64_encode($bookJson);
                            ?>
                            <div class="cover-box">
                                <img src="<?= esc($coverSrc) ?>" alt="Cover" data-book="<?= esc($bookJsonB64) ?>" class="cover-clickable">
                            </div>
                            <div class="book-title"><?= esc($b['judul']) ?></div>
                            <div class="book-meta">
                                <div class="book-stock">Stok : <?= esc($b['stok'] ?? '0') ?></div>
                                <button type="button" data-book="<?= esc($bookJsonB64) ?>" class="btn-details">Details</button>
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

<!-- Book Detail Modal -->
<div id="bookDetailModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto">
    <div class="modal-content bg-white w-11/12 md:w-3/4 lg:w-2/3 mx-auto my-8 rounded-xl shadow-xl p-6 relative">
        <button onclick="closeBookDetail()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <div class="flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-1/2">
                <!-- Main image display -->
                <div class="main-image-container mb-4 rounded-lg overflow-hidden shadow-lg">
                    <img id="mainBookImage" src="" alt="Book Cover" class="w-full h-auto">
                </div>
                <!-- Thumbnail images -->
                <div id="bookThumbnails" class="grid grid-cols-1 gap-2">
                    <!-- Thumbnails will be inserted here by JavaScript -->
                </div>
            </div>
            
            <div class="w-full md:w-1/2 mt-7">
                <h2 id="bookTitle" class="text-2xl font-bold text-gray-800 mb-4"></h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="font-semibold text-gray-600">Kategori</div>
                        <div id="bookCategory" class="col-span-2"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="font-semibold text-gray-600">Penulis</div>
                        <div id="bookAuthor" class="col-span-2"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="font-semibold text-gray-600">Penerbit</div>
                        <div id="bookPublisher" class="col-span-2"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="font-semibold text-gray-600">Tahun Terbit</div>
                        <div id="bookYear" class="col-span-2"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="font-semibold text-gray-600">Stok</div>
                        <div id="bookStock" class="col-span-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modal {
    z-index: 1000;
}
.modal-content {
    max-height: 90vh;
    overflow-y: auto;
}
.main-image-container {
    aspect-ratio: 3/4;
    overflow: hidden;
    max-width: 420px; /* limit main image width to reduce size */
    margin: 0 auto;
}
.main-image-container img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* show whole cover and avoid cropping */
    max-height: 360px; /* cap height */
}
.main-image-container {
    display:flex;
    align-items:center;
    justify-content:center;
    aspect-ratio: 3/4;
    overflow: hidden;
    max-width: 300px; /* smaller width so image card is compact */
    margin: 0 auto;
    padding: 6px; /* small padding to show stroke */
    border-radius: 12px;
    background: transparent; /* no large white area */
    border: 1px solid rgba(0,0,0,0.08); /* thin stroke */
    box-shadow: none; /* remove heavy shadow */
}
.main-image-container img.main-image {
    width: 100%;
    height: auto;
    object-fit: contain;
    display:block;
}
.thumb-row {
    display:flex;
    gap:12px;
    justify-content:center;
    margin-top:12px;
}
.thumb {
    width:72px;
    height:72px;
    aspect-ratio:1/1;
    border-radius:8px;
    overflow:hidden;
    cursor:pointer;
    transition:transform .18s ease, box-shadow .18s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.thumb.active {
    transform: translateY(-8px) scale(1.08);
    box-shadow: 0 14px 30px rgba(0,0,0,0.18);
}
.thumb img { width:100%; height:100%; object-fit:cover }
.thumbnail {
    aspect-ratio: 1;
    cursor: pointer;
    transition: opacity 0.2s;
}
.thumbnail:hover {
    opacity: 0.8;
}
.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>

<script>
function showBookDetail(book) {
    const modal = document.getElementById('bookDetailModal');
    const thumbnails = document.getElementById('bookThumbnails');
    
    // Set book details
    document.getElementById('bookTitle').textContent = book.judul;
    // book may have kategori (from controller) or kategori_nama (older key); support both
    document.getElementById('bookCategory').textContent = book.kategori || book.kategori_nama || '-';
    document.getElementById('bookAuthor').textContent = book.penulis || '-';
    document.getElementById('bookPublisher').textContent = book.penerbit || '-';
    document.getElementById('bookYear').textContent = book.tahun_terbit || '-';
    document.getElementById('bookStock').textContent = book.stok || '0';
    
    // Handle images: create main image element and a centered thumbnail row
    thumbnails.innerHTML = '';
    const thumbsWrap = document.createElement('div');
    thumbsWrap.className = 'thumb-row';

    // determine first image src
    let firstSrc = '<?= base_url('assets/img/buku.png') ?>';
    if (book.gambar && book.gambar.length > 0) {
        const first = book.gambar[0];
        firstSrc = first.full_url || ('<?= rtrim(base_url(), '/') ?>' + '/uploads/' + (first.url || ''));
    }

    // create main image element (no extra white card wrapper)
    const mainContainer = document.querySelector('.main-image-container');
    mainContainer.innerHTML = '';
    const imgEl = document.createElement('img');
    imgEl.id = 'mainBookImage';
    imgEl.className = 'main-image';
    imgEl.src = firstSrc;
    imgEl.alt = 'Book Cover';
    mainContainer.appendChild(imgEl);

    if (book.gambar && book.gambar.length > 0) {
        book.gambar.forEach((img, index) => {
            const src = img.full_url || ('<?= rtrim(base_url(), '/') ?>' + '/uploads/' + (img.url || ''));
            const t = document.createElement('div');
            t.className = 'thumb';
            t.innerHTML = `<img src="${src}" alt="Book image ${index + 1}">`;
            t.addEventListener('click', () => {
                // set main image
                const mainImageEl = document.getElementById('mainBookImage');
                if (mainImageEl) mainImageEl.src = src;
                // set active state
                thumbsWrap.querySelectorAll('.thumb').forEach(el => el.classList.remove('active'));
                t.classList.add('active');
            });
            if (index === 0) t.classList.add('active');
            thumbsWrap.appendChild(t);
        });
    } else {
        const t = document.createElement('div');
        t.className = 'thumb active';
        t.innerHTML = `<img src="<?= base_url('assets/img/buku.png') ?>" alt="no image">`;
        thumbsWrap.appendChild(t);
    }

    thumbnails.appendChild(thumbsWrap);
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBookDetail() {
    const modal = document.getElementById('bookDetailModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal when clicking outside content
document.getElementById('bookDetailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBookDetail();
    }
});

// Handle escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBookDetail();
    }
});
</script>

<script>
// Bind clicks on data-book attributes to open modal (robust, avoids inline JSON escaping issues)
document.addEventListener('DOMContentLoaded', function() {
    function bindDataBook() {
        document.querySelectorAll('[data-book]').forEach(function(el) {
            if (el.dataset._bound) return;
            el.addEventListener('click', function(e) {
                e.preventDefault();
                const b64 = this.dataset.book;
                try {
                    const json = atob(b64);
                    const book = JSON.parse(json);
                    showBookDetail(book);
                } catch (err) {
                    console.error('Failed to parse book data', err, b64);
                }
            });
            el.dataset._bound = '1';
        });
    }

    bindDataBook();
});
</script>


<?= $this->endSection() ?>
