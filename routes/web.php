<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\User\Kas\BayarPinjamanController;
use App\Http\Controllers\User\Kas\PemasukanController;
use App\Http\Controllers\User\Kas\PinjamanController;
use App\Http\Controllers\User\KasController;
use App\Http\Middleware\CheckActiveStatusAdmin;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AllRouteUrlController;
use App\Http\Controllers\Admin\AnggaranController;
use App\Http\Controllers\Admin\CashExpenditureController;
use App\Http\Controllers\Admin\CompanyInformationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DataWargaController;
use App\Http\Controllers\Admin\KasPaymentController;
use App\Http\Controllers\Admin\LayoutsFormController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaldoController;
use App\Http\Controllers\Admin\SubMenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\notificationController;
use App\Http\Controllers\User\Kas\PengeluaranController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(
    function () {
        Route::middleware('admin:admin')->group(function () {
            Route::get('/login', [AdminController::class, 'formLogin']);
            Route::post('/login', [AdminController::class, 'store'])->name('admin.login');
        });
        Route::middleware([
            'auth:sanctum,admin',
            config('jetstream.auth_session'),
            'verified',
            CheckActiveStatusAdmin::class
        ])->group(function () {
            //Halaman untuk ngelola data admin
            Route::resource('/data-admin', AdminAuthController::class);
            Route::post('/toggle-status/{id}', [AdminAuthController::class, 'toggleStatus'])->name('admin.toggleStatus');
            Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
            //Halaman untuk pengelola dashboard
            Route::resource('/dashboard', DashboardController::class); //halaman dashboard pertama
            //Halaman Informasi Perusahaan
            Route::resource('/tentang', CompanyInformationController::class);
            //Halaman Untuk pengelola All Rout Url
            Route::resource('/all-route-url', AllRouteUrlController::class);
            Route::post('/check-route', [AllRouteUrlController::class, 'checkRoute'])->name('check.route');

            //Halaman Untuk ngelola Data Menu
            Route::resource('/menu', MenuController::class);
            Route::post('/menus/toggle-active/{id}', [MenuController::class, 'toggleActive'])->name('menu.toggleStatus');

            //Halaman Untuk ngelola Data Sub Menu
            Route::resource('/sub-menu', SubMenuController::class);
            Route::post('/sub-menus/toggle-active/{id}', [SubMenuController::class, 'toggleActive'])->name('sub-menu.toggleStatus');

            //Halaman Untuk ngelola Data Program
            Route::resource('/program', ProgramController::class);
            Route::post('/programs/toggle-active/{id}', [ProgramController::class, 'toggleActive'])->name('program.toggleStatus');
            Route::post('/programs-settings/store', [ProgramController::class, 'storeProgramSetting'])->name('program-setting_store'); //menambahkan data setting program
            Route::delete('/programs-setting/delete/{id}', [ProgramController::class, 'destroyProgramSetting'])->name('program_setting_destroy'); //untuk mengahpus data setting program
            Route::put('/programs-setting/update/{id}', [ProgramController::class, 'edit'])->name('program_setting_update'); //untukmengupdate data setting program


            //Halaman Untuk ngelola Data Anggaran
            Route::resource('/anggaran', AnggaranController::class);
            Route::post('/anggrans/toggle-active/{id}', [AnggaranController::class, 'toggleActive'])->name('anggaran.toggleStatus');
            Route::post('/anggarans-settings/store', [AnggaranController::class, 'storeAnggaranSetting'])->name('anggaran-setting_store'); //menambahkan data setting Anggaran
            Route::delete('/anggarans-setting/delete/{id}', [AnggaranController::class, 'destroyAnggaranSetting'])->name('anggaran_setting_destroy'); //untuk mengahpus data setting Anggaran
            Route::put('/anggarans-setting/update/{id}', [AnggaranController::class, 'edit'])->name('anggaran_setting_update'); //untukmengupdate data setting Anggaran

            //Halaman Untuk ngelola Data Role
            Route::resource('/role', RoleController::class);
            Route::post('/roles/toggle-active/{id}', [RoleController::class, 'toggleActive'])->name('role.toggleStatus');

            //Halaman Untuk ngelola Data User
            Route::resource('/user', UserController::class);
            Route::post('/users/toggle-active/{id}', [UserController::class, 'toggleActive'])->name('user.toggleStatus');

            //Halaman Untuk ngelola Data warga
            Route::resource('/warga', DataWargaController::class);
            Route::get('/get-pasangan', [DataWargaController::class, 'getPasangan'])->name('getPasangan');
            Route::post('/create/account', [DataWargaController::class, 'account_store'])->name('account.store');
            Route::post('/update/pernikahan', [DataWargaController::class, 'updatePernikahan'])->name('update.pernikahan');
            Route::post('/update/pekerjaan', [DataWargaController::class, 'updatePekerjaan'])->name('update.pekerjaan');
            Route::post('/programs/toggle', [DataWargaController::class, 'toggleAccess'])->name('programs.toggle');
            Route::post('/access-program/toggle-active/{id}', [DataWargaController::class, 'toggleActive'])->name('access_program.toggleStatus');

            // Halaman untuk Kas
            Route::resource('/kas-payment', KasPaymentController::class);

            // Untuk halaman cek saldo
            Route::resource('/saldo', SaldoController::class);

            //Untuk Mengelola data pengeluaran
            Route::resource('/expenditure', CashExpenditureController::class);

            Route::resource('/loan', LoanController::class);



            Route::resource('/layouts-form', LayoutsFormController::class);
        });
    }
);


Route::get('/', function () {
    return view('welcome');
});
Route::post('/send-payment-notification', [notificationController::class, 'sendPaymentSuccessNotification'])->name('notif');
Route::get('/send-notification', [notificationController::class, 'index']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Untuk pembayaran kas user
    Route::resource('/kas', PemasukanController::class);
    Route::get('/pengajuan/kas', [PemasukanController::class, 'pengajuan'])->name('kas.pengajuan');
    Route::get('/confirm/kas/{id}', [PemasukanController::class, 'show_confirm'])->name('kas.show.confirm');
    Route::patch('/confirm/kas/{id}', [PemasukanController::class, 'confirm'])->name('kas.confirm');
    Route::get('/edit/kas/{id}', [PemasukanController::class, 'editPengurus'])->name('kas.editPengurus');
    Route::patch('/edit/kas/{id}', [PemasukanController::class, 'updatePengurus'])->name('kas.updatePengurus');
    Route::delete('/hapus/kas/{id}', [PemasukanController::class, 'destroyPengurus'])->name('kas.destroyPengurus');

    // Untuk Pengeluaran kas user
    Route::resource('/pengeluaran', PengeluaranController::class);
    Route::get('/pengeluarans/pengajuan', [PengeluaranController::class, 'pengajuan'])->name('pengeluaran.pengajuan');
    Route::get('/confirm/pengeluaran/{id}', [PengeluaranController::class, 'show_confirm'])->name('pengeluaran.show.confirm');
    Route::patch('/approve/pengeluaran/{id}', [PengeluaranController::class, 'approved'])->name('pengeluaran.approved');
    Route::patch('/disburse/pengeluaran/{id}', [PengeluaranController::class, 'disbursed'])->name('pengeluaran.disbursed');

    Route::resource('/pinjaman', PinjamanController::class);
    Route::get('/pinjamans/pengajuan', [PinjamanController::class, 'pengajuan'])->name('pinjaman.pengajuan');
    Route::get('/confirm/pinjaman/{id}', [PinjamanController::class, 'show_confirm'])->name('pinjaman.show.confirm');
    Route::patch('/approve/pinjaman/{id}', [PinjamanController::class, 'approved'])->name('pinjaman.approved');
    Route::patch('/disburse/pinjaman/{id}', [PinjamanController::class, 'disbursed'])->name('pinjaman.disbursed');
    Route::patch('/acknowledge/pinjaman/{id}', [PinjamanController::class, 'Acknowledged'])->name('pinjaman.acknowledged');
    Route::get('/edit/pinjaman/{id}', [PinjamanController::class, 'editPengurus'])->name('pinjaman.editPengurus');
    Route::patch('/edit/pinjaman/{id}', [PinjamanController::class, 'updatePengurus'])->name('pinjaman.updatePengurus');
    Route::delete('/hapus/pinjaman/{id}', [PinjamanController::class, 'destroyPengurus'])->name('pinjaman.destroyPengurus');

    // Untuk pembayaran pinjaman
    Route::resource('/bayar-pinjaman', BayarPinjamanController::class);
    Route::get('/pembayaran/bayar-pinjaman/{id}', [BayarPinjamanController::class, 'pembayaran'])->name('bayar-pinjaman.pembayaran');
    Route::get('/pengajuan/bayar-pinjaman', [BayarPinjamanController::class, 'pengajuan'])->name('bayar-pinjaman.pengajuan');
    Route::get('/confirm/bayar-pinjaman/{id}', [BayarPinjamanController::class, 'show_confirm'])->name('bayar-pinjaman.show.confirm');
    Route::patch('/confirm/bayar-pinjaman/{id}', [BayarPinjamanController::class, 'confirm'])->name('bayar-pinjaman.confirm');
    Route::get('/edit/bayar-pinjaman/{id}', [BayarPinjamanController::class, 'editPengurus'])->name('bayar-pinjaman.editPengurus');
    Route::patch('/edit/bayar-pinjaman/{id}', [BayarPinjamanController::class, 'updatePengurus'])->name('bayar-pinjaman.updatePengurus');
    Route::delete('/hapus/bayar-pinjaman/{id}', [BayarPinjamanController::class, 'destroyPengurus'])->name('bayar-pinjaman.destroyPengurus');
});