<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CekTagihanController;
use App\Http\Controllers\CekKasController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('login');
// });



Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/export-low-progress', [App\Http\Controllers\HomeController::class, 'exportLowProgress'])->name('home.export-low-progress')->middleware('auth');

Auth::routes();

Route::get('generator_builder', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@builder')->name('io_generator_builder');

Route::get('field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@fieldTemplate')->name('io_field_template');

Route::get('relation_field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@relationFieldTemplate')->name('io_relation_field_template');

Route::post('generator_builder/generate', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generate')->name('io_generator_builder_generate');

Route::post('generator_builder/rollback', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@rollback')->name('io_generator_builder_rollback');

Route::post(
    'generator_builder/generate-from-file',
    '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generateFromFile'
)->name('io_generator_builder_generate_from_file');


Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    Route::resource('kas-sekolahs', App\Http\Controllers\KasSekolahController::class);
    Route::resource('siswas', App\Http\Controllers\SiswaController::class);
    Route::resource('kelas', App\Http\Controllers\KelasController::class);
    Route::get('/get-siswa-by-kelas/{kelas}', [App\Http\Controllers\SiswaController::class, 'getSiswaByKelas']);
    Route::get('/get-tagihan-by-siswa/{siswa_id}', [App\Http\Controllers\SiswaController::class, 'getTagihanBySiswa']);
    Route::get('/get-sisa-tagihan/{siswa_id}/{tagihan_id}', [App\Http\Controllers\SiswaController::class, 'getSisaTagihan']);
    Route::get('/get-jurusan/{kelas}', [App\Http\Controllers\KelasController::class, 'getJurusan']);
    Route::resource('tagihans', App\Http\Controllers\TagihanController::class);

    Route::get('cek-tagihan', [CekTagihanController::class, 'index'])->name('cek-tagihan');
    Route::get('cek-tagihan/print/{id}', [CekTagihanController::class, 'print'])->name('tagihan.print');
    Route::get('cek-tagihan/export/{id}', [CekTagihanController::class, 'export'])->name('tagihan.export');
    Route::get('/cek-kas', [CekKasController::class, 'index'])->name('cek-kas');
    Route::get('/cek-kas/export', [CekKasController::class, 'export'])->name('kas.export');

    // admin-only area
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index')->middleware('role:admin');
    Route::post('/admin/kenaikan-kelulusan', [AdminController::class, 'promoteAndGraduate'])->name('admin.promote')->middleware('role:admin');

    // super-admin only area
    Route::resource('users', App\Http\Controllers\UserController::class)->middleware('role:super-admin');
    Route::resource('activity-logs', App\Http\Controllers\ActivityLogController::class)->only(['index', 'show'])->middleware('role:super-admin');
});

