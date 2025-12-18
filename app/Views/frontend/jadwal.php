<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Jadwal
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="text-center my-6 px-6">
    <h1 class="text-4xl md:text-5xl font-bold text-green-700 mb-3">
        Jadwal Buka
    </h1>
    <p class="text-gray-500 max-w-2xl mx-auto">
        Siswa dapat melakukan peminjaman dan pengembalian buku sesuai dengan jadwal berikut
    </p>
</div>

<section class="bg-[#ECF9EF] py-16 px-8 md:px-20">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-10">

            <!-- Senin - Kamis -->
            <div class="schedule-card">
                <div class="schedule-header">Senin - Kamis</div>
                <div class="schedule-body">
                    <p class="text-lg font-semibold text-gray-700">Buka</p>
                    <p class="text-2xl font-bold text-[#25622D] mb-3">7.30 - 14.00 WIB</p>
                    <hr class="my-3 border-gray-200">
                    <p class="text-sm text-gray-600 text-left leading-tight">
                        <span class="font-semibold">Note:</span><br>
                        Jam 12:30–13.00 Istirahat
                    </p>
                </div>
            </div>

            <!-- Jumat -->
            <div class="schedule-card">
                <div class="schedule-header">Jumat</div>
                <div class="schedule-body">
                    <p class="text-lg font-semibold text-gray-700">Buka</p>
                    <p class="text-2xl font-bold text-[#25622D] mb-3">7.30 - 12.00 WIB</p>
                    <hr class="my-3 border-gray-200">
                    <p class="text-sm text-gray-600 text-left leading-tight">
                        <span class="font-semibold">Note:</span><br>
                        -
                    </p>
                </div>
            </div>

            <!-- Sabtu -->
            <div class="schedule-card">
                <div class="schedule-header">Sabtu</div>
                <div class="schedule-body">
                    <p class="text-lg font-semibold text-gray-700">Buka</p>
                    <p class="text-2xl font-bold text-[#25622D] mb-3">7.30 - 14.00 WIB</p>
                    <hr class="my-3 border-gray-200">
                    <p class="text-sm text-gray-600 text-left leading-tight">
                        <span class="font-semibold">Note:</span><br>
                        Jam 12:30–13.00 Istirahat
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>
