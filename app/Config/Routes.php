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
    
    // Users management (Admin/Super Admin only for modification, Leader can view)
    $routes->group('users', ['filter' => 'auth:Super Admin,Admin,Leader'], function($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');  // Protected in controller
        $routes->post('create', 'Users::create');  // Protected in controller
        $routes->get('edit/(:num)', 'Users::edit/$1');  // Protected in controller
        $routes->put('edit/(:num)', 'Users::edit/$1');  // Protected in controller
        $routes->delete('delete/(:num)', 'Users::delete/$1');  // Protected in controller
    });
    
    // Rooms management (Admin/Super Admin only)
    $routes->group('rooms', ['filter' => 'auth:Super Admin,Admin'], function($routes) {
        $routes->get('/', 'Rooms::index');
        $routes->get('create', 'Rooms::create');
        $routes->post('create', 'Rooms::create');
        $routes->get('edit/(:num)', 'Rooms::edit/$1');
        $routes->put('edit/(:num)', 'Rooms::edit/$1');
        $routes->delete('delete/(:num)', 'Rooms::delete/$1');
    });
    
    // Assets management (Admin/Super Admin only)
    $routes->group('assets', ['filter' => 'auth:Super Admin,Admin'], function($routes) {
        $routes->get('/', 'Assets::index');
        $routes->get('create', 'Assets::create');
        $routes->post('create', 'Assets::create');
        $routes->get('edit/(:num)', 'Assets::edit/$1');
        $routes->put('edit/(:num)', 'Assets::edit/$1');
        $routes->delete('delete/(:num)', 'Assets::delete/$1');
    });
    
    // Room-Asset relationships (Admin/Super Admin only)
    $routes->group('room-assets', ['filter' => 'auth:Super Admin,Admin'], function($routes) {
        $routes->get('/', 'RoomAssets::index');
        $routes->get('room/(:num)', 'RoomAssets::showRoom/$1');
        $routes->get('show/(:num)', 'RoomAssets::showRoom/$1');
        $routes->get('create', 'RoomAssets::create');
        $routes->post('create', 'RoomAssets::create');
        $routes->get('add-asset/(:num)', 'RoomAssets::addAsset/$1');
        $routes->post('add-asset/(:num)', 'RoomAssets::addAsset/$1');
        $routes->post('add-assets/(:num)', 'RoomAssets::addAssets/$1');
        $routes->get('edit/(:num)', 'RoomAssets::edit/$1');
        $routes->put('edit/(:num)', 'RoomAssets::edit/$1');
        $routes->delete('delete/(:num)', 'RoomAssets::delete/$1');
        $routes->post('remove-asset/(:num)/(:num)', 'RoomAssets::removeAsset/$1/$2');
        $routes->delete('remove-asset/(:num)/(:num)', 'RoomAssets::removeAsset/$1/$2');
    });
    
    // Assessments (GA Staff, Admin, Super Admin, Leader can view)
    $routes->group('assessments', ['filter' => 'auth:GA Staff,Admin,Super Admin,Leader'], function($routes) {
        $routes->get('/', 'Assessments::index');
        $routes->get('admin', 'Assessments::adminIndex');  // Admin/Leader view
        $routes->get('rooms', 'Assessments::rooms');
        $routes->get('room/(:num)', 'Assessments::assessRoom/$1');
        $routes->get('assess/(:num)', 'Assessments::assessRoom/$1');
        $routes->post('save', 'Assessments::saveAssessment');  // Only GA Staff
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
        $routes->get('assets/export/pdf', 'Reports::exportAssetsPdf');
        $routes->get('assets/export/excel', 'Reports::exportAssetsExcel');
        $routes->get('rooms/export/pdf', 'Reports::exportRoomsPdf');
        $routes->get('rooms/export/excel', 'Reports::exportRoomsExcel');
    });
});
