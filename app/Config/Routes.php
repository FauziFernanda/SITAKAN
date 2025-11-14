<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

$routes->get('/', 'Front\Home::index');
$routes->get('/peraturan', 'Front\Home::peraturan');
$routes->get('/jadwal', 'Front\Home::jadwal');

// Frontend Auth
$routes->get('/login', 'Front\Home::login');
$routes->post('/auth/login', 'AuthController::login');
$routes->get('/auth/logout', 'AuthController::logout');

// Frontend list buku
$routes->get('/list-buku', 'Frontend\ViewBukuController::index');
$routes->get('/list-buku/detail/(:num)', 'Frontend\ViewBukuController::detail/$1');

// ROUTE BACKEND
$routes->group('backend', ['filter' => 'auth'], function($routes) {
    $routes->get('home', 'Backend\Home::index');
    $routes->get('buku_list', 'Backend\BukuController::index');
    $routes->get('peminjaman', 'Backend\PinjamanController::index');
    $routes->get('denda', 'Backend\DendaController::index');
    $routes->get('denda/pdf', 'Backend\DendaController::pdf');
    $routes->get('riwayat', 'Backend\RiwayatController::index');
    $routes->get('register', 'Backend\UserController::index');
    $routes->post('register/create', 'Backend\UserController::create');
    $routes->get('register/edit/(:num)', 'Backend\UserController::edit/$1');
    $routes->post('register/update/(:num)', 'Backend\UserController::update/$1');
    $routes->post('register/delete/(:num)', 'Backend\UserController::delete/$1');
    $routes->get('riwayat/pdf', 'Backend\RiwayatController::pdf');
    $routes->get('riwayat/delete/(:num)', 'Backend\RiwayatController::delete/$1');
    // Pinjaman create
    $routes->post('peminjaman/create', 'Backend\PinjamanController::create');
    // Pinjaman return (mark finished and restore stock)
    $routes->post('peminjaman/return/(:num)', 'Backend\PinjamanController::return/$1');
    // Pinjaman complete (move to riwayat table and delete from pinjams)
    $routes->post('peminjaman/complete/(:num)', 'Backend\PinjamanController::complete/$1');
    // Pinjaman delete (remove peminjaman and restore stock)
    $routes->post('peminjaman/delete/(:num)', 'Backend\PinjamanController::delete/$1');
    // Kategori routes
    $routes->post('kategori/create', 'Backend\KategoriController::create');
    // Buku create (handle new book + 3 images)
    $routes->post('buku/create', 'Backend\BukuController::create');
    // Buku update (edit existing book)
    $routes->post('buku/update/(:num)', 'Backend\BukuController::update/$1');
    // Buku delete
    $routes->post('buku/delete/(:num)', 'Backend\BukuController::delete/$1');
});
