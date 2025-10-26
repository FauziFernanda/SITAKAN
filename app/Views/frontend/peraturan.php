<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Peraturan
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="container mx-auto px-8 md:px-20 py-5">

    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-green-700 mb-3">
            Peraturan
        </h1>
        <p class="text-gray-600">
            Aturan dan tata tertib dalam lingkup perpustakaan
        </p>
    </div>

    <div class="flex flex-col-reverse md:flex-row justify-between items-end md:gap-12 mb-16">

        <div class="w-full md:w-3/5 space-y-4 mt-8 md:mt-0">

            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-white border border-gray-300 rounded-md font-bold text-gray-700 text-lg">
                    1.
                </div>
                <div class="flex-1 bg-green-50 border border-green-200 rounded-md p-4 text-gray-800">
                    Jaga ketenangan ketika diperpustakaan
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-white border border-gray-300 rounded-md font-bold text-gray-700 text-lg">
                    2.
                </div>
                <div class="flex-1 bg-green-50 border border-green-200 rounded-md p-4 text-gray-800">
                    Dilarang membawa makanan ke perpustakaan
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-white border border-gray-300 rounded-md font-bold text-gray-700 text-lg">
                    3.
                </div>
                <div class="flex-1 bg-green-50 border border-green-200 rounded-md p-4 text-gray-800">
                    Buku hanya boleh dipinjam 1 persiswa
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-white border border-gray-300 rounded-md font-bold text-gray-700 text-lg">
                    4.
                </div>
                <div class="flex-1 bg-green-50 border border-green-200 rounded-md p-4 text-gray-800">
                    Buku wajib dikembalikan tepat waktu
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-white border border-gray-300 rounded-md font-bold text-gray-700 text-lg">
                    5.
                </div>
                <div class="flex-1 bg-green-50 border border-green-200 rounded-md p-4 text-gray-800">
                    Jika melanggar, maka akan dikenakan saksi/denda
                </div>
            </div>

        </div>

        <div class="w-3/5 md:w-1/2">
            <img src="<?= base_url('assets/img/peraturan.png') ?>" 
                 alt="Siswa belajar di perpustakaan" 
                 class="rounded-lg shadow-lg w-full object-cover">
        </div>

    </div>

</section>

<?= $this->endSection() ?>