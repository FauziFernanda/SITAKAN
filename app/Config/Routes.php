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
