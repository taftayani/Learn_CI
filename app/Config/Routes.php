<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/auth/authenticate', 'Auth::authenticate');
$routes->get('/logout', 'Auth::logout');

$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
    
    // Users management (Admin/Super Admin only)
    $routes->group('users', ['filter' => 'auth:Super Admin,Admin'], function($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('create', 'Users::create');
        $routes->get('edit/(:num)', 'Users::edit/$1');
        $routes->post('edit/(:num)', 'Users::edit/$1');
        $routes->get('delete/(:num)', 'Users::delete/$1');
    });
    
    // Rooms management (Admin/Super Admin only)
    $routes->group('rooms', ['filter' => 'auth:Super Admin,Admin'], function($routes) {
        $routes->get('/', 'Rooms::index');
        $routes->get('create', 'Rooms::create');
        $routes->post('create', 'Rooms::create');
        $routes->get('edit/(:num)', 'Rooms::edit/$1');
        $routes->post('edit/(:num)', 'Rooms::edit/$1');
        $routes->get('delete/(:num)', 'Rooms::delete/$1');
    });
    
    // Assets management (Admin/Super Admin only)
    $routes->group('assets', ['filter' => 'auth:Super Admin,Admin'], function($routes) {
        $routes->get('/', 'Assets::index');
        $routes->get('create', 'Assets::create');
        $routes->post('create', 'Assets::create');
        $routes->get('edit/(:num)', 'Assets::edit/$1');
        $routes->post('edit/(:num)', 'Assets::edit/$1');
        $routes->get('delete/(:num)', 'Assets::delete/$1');
    });
    
    // Room-Asset relationships (Admin/Super Admin only)
    $routes->group('room-assets', ['filter' => 'auth:Super Admin,Admin'], function($routes) {
        $routes->get('/', 'RoomAssets::index');
        $routes->get('room/(:num)', 'RoomAssets::showRoom/$1');
        $routes->post('add', 'RoomAssets::add');
        $routes->get('remove/(:num)', 'RoomAssets::remove/$1');
    });
    
    // Assessments (GA Staff only)
    $routes->group('assessments', ['filter' => 'auth:GA Staff'], function($routes) {
        $routes->get('/', 'Assessments::index');
        $routes->get('rooms', 'Assessments::rooms');
        $routes->get('room/(:num)', 'Assessments::assessRoom/$1');
        $routes->post('save', 'Assessments::saveAssessment');
        $routes->get('history', 'Assessments::history');
        $routes->get('details/(:num)', 'Assessments::details/$1');
    });
    
    // Reports (Leaders only)
    $routes->group('reports', ['filter' => 'auth:Leader'], function($routes) {
        $routes->get('/', 'Reports::index');
        $routes->get('assets', 'Reports::assetReports');
        $routes->get('rooms', 'Reports::roomReports');
        $routes->get('export/pdf', 'Reports::exportPdf');
        $routes->get('export/excel', 'Reports::exportExcel');
    });
});
