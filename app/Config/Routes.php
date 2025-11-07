<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

$routes->get('/', 'Front\Home::index');
$routes->get('/peraturan', 'Front\Home::peraturan');
$routes->get('/jadwal', 'Front\Home::jadwal');
$routes->get('/login', 'Front\Home::login');
// Frontend list buku
$routes->get('/list-buku', 'Front\ViewBukuController::index');


//backend
$routes->group('backend', function($routes) {
    $routes->get('home', 'Backend\Home::index');
});
$routes->get('/admin/buku', 'Backend\BukuController::index');
// ROUTE BACKEND
$routes->group('backend', function($routes) {
    $routes->get('home', 'Backend\DashboardController::index');
    $routes->get('buku_list', 'Backend\BukuController::index');
    $routes->get('peminjaman', 'Backend\PinjamanController::index');
    $routes->get('denda', 'Backend\DendaController::index');
    $routes->get('riwayat', 'Backend\RiwayatController::index');
    $routes->post('riwayat/delete/(:num)', 'Backend\RiwayatController::delete/$1');
    // Pinjaman create
    $routes->post('peminjaman/create', 'Backend\PinjamanController::create');
    // Pinjaman return (mark finished and restore stock)
    $routes->post('peminjaman/return/(:num)', 'Backend\PinjamanController::return/$1');
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
