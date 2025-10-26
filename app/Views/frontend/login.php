<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SITAKAN</title>
    <link rel="icon" href="<?= base_url('assets/img/logo.png') ?>">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body class="bg-white font-poppins flex flex-col items-center justify-center min-h-screen">

    <!-- Logo & Judul -->
    <div class="text-center mb-6">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo SITAKAN" class="w-24 mx-auto mb-4">
        <h1 class="text-2xl md:text-3xl font-bold text-[#25622D]">LOGIN TO SITAKAN</h1>
        <p class="text-gray-500 mt-1 text-sm">“Silahkan masukkan username dan password anda”</p>
    </div>

    <!-- Card Login -->
    <div class="bg-[#ECF9EF] border border-[#2E8B57] rounded-sm shadow-sm w-11/12 md:w-[420px] p-8">
        <form action="<?= base_url('/login/process') ?>" method="post" class="space-y-5">

            <div>
                <label for="username" class="block text-sm font-medium text-gray-600 mb-1">Username</label>
                <input type="text" id="username" name="username"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#25622D] bg-gray-100">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600 mb-1">Password</label>
                <input type="password" id="password" name="password"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#25622D] bg-gray-100">
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" id="remember" name="remember" class="accent-[#25622D]">
                <label for="remember" class="text-gray-600 text-sm">Remember me</label>
            </div>

            <button type="submit"
                    class="w-full bg-[#25622D] text-white font-semibold py-2 rounded-md hover:bg-[#1E4E22] transition shadow-md">
                LOGIN
            </button>
        </form>
    </div>

</body>
</html>
