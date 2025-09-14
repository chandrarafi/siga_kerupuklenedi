<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function () {
    return redirect()->to('/auth');
});
$routes->get('/login', 'Auth::login');
$routes->post('/auth/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');

// Auth Routes
$routes->get('auth', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

// Admin Routes
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Admin::index', ['filter' => 'role:admin,pimpinan']);

    // User Management (hanya admin)
    $routes->group('', ['filter' => 'role:admin,pimpinan'], function ($routes) {
        $routes->get('users', 'Admin::users');
        $routes->get('getUsers', 'Admin::getUsers');
        $routes->get('getUser/(:num)', 'Admin::getUser/$1');
        $routes->post('createUser', 'Admin::createUser');
        $routes->post('addUser', 'Admin::addUser');
        $routes->post('updateUser/(:num)', 'Admin::updateUser/$1');
        $routes->post('deleteUser/(:num)', 'Admin::deleteUser/$1');
        $routes->get('getRoles', 'Admin::getRoles');

        // Bagian Routes
        $routes->get('bagian', 'Bagian::index');
        $routes->post('bagian/store', 'Bagian::store');
        $routes->post('bagian/update/(:num)', 'Bagian::update/$1');
        $routes->get('bagian/delete/(:num)', 'Bagian::delete/$1');

        // Jabatan Routes
        $routes->get('jabatan', 'Jabatan::index');
        $routes->post('jabatan/store', 'Jabatan::store');
        $routes->post('jabatan/update/(:num)', 'Jabatan::update/$1');
        $routes->get('jabatan/delete/(:num)', 'Jabatan::delete/$1');
        $routes->get('jabatan/report', 'Jabatan::report');

        // Pegawai Routes
        $routes->get('pegawai', 'Pegawai::index');
        $routes->get('pegawai/create', 'Pegawai::create');
        $routes->post('pegawai/store', 'Pegawai::store');
        $routes->get('pegawai/edit/(:segment)', 'Pegawai::edit/$1');
        $routes->post('pegawai/update/(:segment)', 'Pegawai::update/$1');
        $routes->get('pegawai/delete/(:segment)', 'Pegawai::delete/$1');
        $routes->get('pegawai/report', 'Pegawai::report');
        $routes->get('pegawai/getJabatanByBagian', 'Pegawai::getJabatanByBagian');

        // Absensi
        $routes->get('absensi', 'Absensi::index');
        $routes->get('absensi/create', 'Absensi::create');
        $routes->post('absensi/store', 'Absensi::store');
        $routes->get('absensi/edit/(:num)', 'Absensi::edit/$1');
        $routes->post('absensi/update/(:num)', 'Absensi::update/$1');
        $routes->get('absensi/delete/(:num)', 'Absensi::delete/$1');
        $routes->get('absensi/report', 'Admin\Absensi::report');

        // Izin
        $routes->get('izin', 'Admin\Izin::index');
        $routes->get('izin/show/(:num)', 'Admin\Izin::show/$1');
        $routes->get('izin/show/(:segment)', 'Admin\Izin::show/$1');
        $routes->post('izin/approve/(:num)', 'Admin\Izin::approve/$1');
        $routes->post('izin/approve/(:segment)', 'Admin\Izin::approve/$1');
        $routes->post('izin/reject/(:num)', 'Admin\Izin::reject/$1');
        $routes->post('izin/reject/(:segment)', 'Admin\Izin::reject/$1');
        $routes->get('izin/report', 'Admin\Izin::report');
        $routes->get('izin/report_partial', 'Admin\Izin::report_partial');
        $routes->get('izin/generatePdf', 'Admin\Izin::generatePdf');

        // Lembur
        $routes->get('lembur', 'Admin\Lembur::index');
        $routes->get('lembur/create', 'Admin\Lembur::create');
        $routes->post('lembur/store', 'Admin\Lembur::store');
        $routes->get('lembur/edit/(:num)', 'Admin\Lembur::edit/$1');
        $routes->post('lembur/update/(:num)', 'Admin\Lembur::update/$1');
        $routes->get('lembur/delete/(:num)', 'Admin\Lembur::delete/$1');
        $routes->get('lembur/show/(:num)', 'Admin\Lembur::show/$1');
        $routes->get('lembur/report', 'Admin\Lembur::report');

        // Settings Routes
        $routes->get('settings/office-location', 'Admin\Settings::officeLocation');
        $routes->post('settings/update-office-location', 'Admin\Settings::updateOfficeLocation');
        $routes->post('settings/save-office-location', 'Admin\Settings::saveOfficeLocation');
        $routes->get('settings/absensi-settings', 'Admin\Settings::absensiSettings');
        $routes->post('settings/save-absensi-settings', 'Admin\Settings::saveAbsensiSettings');

        // Gaji
        $routes->get('gaji', 'Admin\Gaji::index');
        $routes->get('gaji/create', 'Admin\Gaji::create');
        $routes->post('gaji/store', 'Admin\Gaji::store');
        $routes->get('gaji/edit/(:num)', 'Admin\Gaji::edit/$1');
        $routes->get('gaji/edit/(:segment)', 'Admin\Gaji::edit/$1');
        $routes->post('gaji/update/(:num)', 'Admin\Gaji::update/$1');
        $routes->post('gaji/update/(:segment)', 'Admin\Gaji::update/$1');
        $routes->get('gaji/delete/(:num)', 'Admin\Gaji::delete/$1');
        $routes->get('gaji/delete/(:segment)', 'Admin\Gaji::delete/$1');
        $routes->post('gaji/delete/(:num)', 'Admin\Gaji::delete/$1');
        $routes->post('gaji/delete/(:segment)', 'Admin\Gaji::delete/$1');
        $routes->get('gaji/show/(:num)', 'Admin\Gaji::show/$1');
        $routes->get('gaji/show/(:segment)', 'Admin\Gaji::show/$1');
        $routes->get('gaji/slip/(:num)', 'Admin\Gaji::slip/$1');
        $routes->get('gaji/slip/(:segment)', 'Admin\Gaji::slip/$1');
        $routes->get('gaji/report', 'Admin\Gaji::report');
        $routes->get('gaji/report_partial', 'Admin\Gaji::report_partial');
        $routes->get('gaji/generatePdf', 'Admin\Gaji::generatePdf');
        $routes->post('gaji/hitung-gaji', 'Admin\Gaji::hitungGaji');
        $routes->post('gaji/hitung', 'Admin\Gaji::hitungGaji');
        $routes->get('gaji/get-pegawai', 'Admin\Gaji::getPegawai');
        $routes->post('gaji/process-payment/(:num)', 'Admin\Gaji::processPayment/$1');
        $routes->post('gaji/process-payment/(:segment)', 'Admin\Gaji::processPayment/$1');
        $routes->post('gaji/cancel-payment/(:num)', 'Admin\Gaji::cancelPayment/$1');
        $routes->post('gaji/cancel-payment/(:segment)', 'Admin\Gaji::cancelPayment/$1');
    });

    // Admin Pegawai Routes
    $routes->group('pegawai', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/', 'Pegawai::index');
        $routes->get('create', 'Pegawai::create');
        $routes->post('store', 'Pegawai::store');
        $routes->get('edit/(:num)', 'Pegawai::edit/$1');
        $routes->post('update/(:num)', 'Pegawai::update/$1');
        $routes->get('delete/(:num)', 'Pegawai::delete/$1');
        $routes->get('getJabatanByBagian', 'Pegawai::getJabatanByBagian');
        $routes->get('report', 'Pegawai::report');
    });

    // Admin - Izin
    $routes->get('admin/izin', 'Admin\Izin::index', ['filter' => 'role:admin']);
    $routes->get('admin/izin/show/(:num)', 'Admin\Izin::show/$1', ['filter' => 'role:admin']);
    $routes->get('admin/izin/show/(:any)', 'Admin\Izin::show/$1', ['filter' => 'role:admin']);
    $routes->get('admin/izin/report', 'Admin\Izin::report', ['filter' => 'role:admin']);
    $routes->get('admin/izin/report_partial', 'Admin\Izin::report_partial', ['filter' => 'role:admin']);
    $routes->get('admin/izin/generatePdf', 'Admin\Izin::generatePdf', ['filter' => 'role:admin']);
    $routes->get('admin/izin/debug_all_data', 'Admin\Izin::debug_all_data'); // Route debugging tanpa filter
    $routes->get('admin/izin/add_sample_data', 'Admin\Izin::add_sample_data'); // Route untuk menambah data contoh tanpa filter
    $routes->get('admin/izin/debug_table', 'Admin\Izin::debug_table'); // Route untuk memeriksa struktur tabel
    $routes->post('admin/izin/approve/(:num)', 'Admin\Izin::approve/$1', ['filter' => 'role:admin']);
    $routes->post('admin/izin/approve/(:any)', 'Admin\Izin::approve/$1', ['filter' => 'role:admin']);
    $routes->post('admin/izin/reject/(:num)', 'Admin\Izin::reject/$1', ['filter' => 'role:admin']);
    $routes->post('admin/izin/reject/(:any)', 'Admin\Izin::reject/$1', ['filter' => 'role:admin']);
});

// Bagian API Routes
$routes->group('bagian', ['filter' => 'auth'], function ($routes) {
    $routes->post('getAll', 'Bagian::getAll');
    $routes->post('store', 'Bagian::store');
    $routes->get('edit/(:num)', 'Bagian::edit/$1');
    $routes->post('update/(:num)', 'Bagian::update/$1');
    $routes->delete('delete/(:num)', 'Bagian::delete/$1');
});

// Jabatan API Routes
$routes->group('jabatan', ['filter' => 'auth'], function ($routes) {
    $routes->post('getAll', 'Jabatan::getAll');
    $routes->get('getBagian', 'Jabatan::getBagian');
    $routes->post('store', 'Jabatan::store');
    $routes->get('edit/(:num)', 'Jabatan::edit/$1');
    $routes->post('update/(:num)', 'Jabatan::update/$1');
    $routes->delete('delete/(:num)', 'Jabatan::delete/$1');
});

// Pegawai API Routes
$routes->group('pegawai', ['filter' => 'auth'], function ($routes) {
    $routes->post('getAll', 'Pegawai::getAll');
    $routes->get('getJabatan', 'Pegawai::getJabatan');
    $routes->get('create', 'Pegawai::create');
    $routes->post('store', 'Pegawai::store');
    $routes->get('edit/(:any)', 'Pegawai::edit/$1');
    $routes->post('update/(:any)', 'Pegawai::update/$1');
    $routes->delete('delete/(:any)', 'Pegawai::delete/$1');
});

// Pegawai Routes
$routes->group('pegawai', ['filter' => 'role:pegawai'], function ($routes) {
    $routes->get('dashboard', 'Pegawai\Dashboard::index');
    $routes->get('dashboard/riwayat', 'Pegawai\Dashboard::riwayat');
    $routes->get('dashboard/riwayat/(:num)/(:num)', 'Pegawai\Dashboard::riwayat/$1/$2');
    $routes->post('dashboard/absen-masuk', 'Pegawai\Dashboard::absenMasuk');
    $routes->post('dashboard/absen-pulang', 'Pegawai\Dashboard::absenPulang');

    // Rute untuk lembur dan gaji
    $routes->get('dashboard/lembur', 'Pegawai\Dashboard::lembur');
    $routes->get('dashboard/lembur/(:num)/(:num)', 'Pegawai\Dashboard::lembur/$1/$2');
    $routes->get('dashboard/gaji', 'Pegawai\Dashboard::gaji');
    $routes->get('dashboard/gaji/(:num)/(:num)', 'Pegawai\Dashboard::gaji/$1/$2');
    $routes->get('dashboard/slip-gaji/(:alphanum)', 'Pegawai\Dashboard::slipGaji/$1');

    // Izin Routes
    $routes->get('izin', 'Izin::index', ['filter' => 'role:pegawai']);
    $routes->get('izin/create', 'Izin::create', ['filter' => 'role:pegawai']);
    $routes->post('izin/store', 'Izin::store', ['filter' => 'role:pegawai']);
    $routes->get('izin/edit/(:alphanum)', 'Izin::edit/$1', ['filter' => 'role:pegawai']);
    $routes->post('izin/update/(:alphanum)', 'Izin::update/$1', ['filter' => 'role:pegawai']);
    $routes->get('izin/show/(:alphanum)', 'Izin::show/$1', ['filter' => 'role:pegawai']);
    $routes->get('izin/delete/(:alphanum)', 'Izin::delete/$1', ['filter' => 'role:pegawai']);
});

// API Absensi routes
$routes->group('api/absensi', function ($routes) {
    $routes->post('masuk', 'Absensi::apiAbsen');
    $routes->post('pulang', 'Absensi::apiPulang');
});
