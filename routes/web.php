<?php

use Illuminate\Support\Facades\Route;

// ── Livewire: Admin ───────────────────────────────────────────
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\UserManager;
use App\Livewire\Admin\ProdiManager;
use App\Livewire\Admin\TahunAjaranManager;
use App\Livewire\Admin\SemesterManager;
use App\Livewire\Admin\KelasManager;

// ── Livewire: Dosen ───────────────────────────────────────────
use App\Livewire\Dosen\Dashboard as DosenDashboard;
use App\Livewire\Dosen\MatkulManager;
use App\Livewire\Dosen\TugasList as DosenTugasList;
use App\Livewire\Dosen\TugasForm;
use App\Livewire\Dosen\PenilaianView;
use App\Livewire\Dosen\PenilaianMahasiswa;

// ── Livewire: Mahasiswa ───────────────────────────────────────
use App\Livewire\Mahasiswa\Dashboard as MahasiswaDashboard;
use App\Livewire\Mahasiswa\TugasList as MahasiswaTugasList;
use App\Livewire\Mahasiswa\PengerjaanForm;
use App\Livewire\Mahasiswa\HasilNilai;

// ── Livewire: Shared (semua role) ─────────────────────────────
use App\Livewire\Shared\ProfilePage;

// ── Controllers ──────────────────────────────────────────────
use App\Http\Controllers\AntiCheatController;
use App\Http\Controllers\ExportController;

// ─────────────────────────────────────────────────────────────
// ROOT
// ─────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ─────────────────────────────────────────────────────────────
// AUTH — bawaan Breeze
// ─────────────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─────────────────────────────────────────────────────────────
// REDIRECT /dashboard → sesuai role
// ─────────────────────────────────────────────────────────────
Route::middleware('auth')->get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'dosen' => redirect()->route('dosen.dashboard'),
        'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
        default => abort(403),
    };
})->name('dashboard');

// ─────────────────────────────────────────────────────────────
// ADMIN — /admin/*
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/users', UserManager::class)->name('users');
        Route::get('/prodi', ProdiManager::class)->name('prodi');
        Route::get('/tahun-ajaran', TahunAjaranManager::class)->name('tahun-ajaran');
        Route::get('/semester', SemesterManager::class)->name('semester');
        Route::get('/kelas', KelasManager::class)->name('kelas');
        Route::get('/profil', ProfilePage::class)->name('profil');
    });

// ─────────────────────────────────────────────────────────────
// DOSEN — /dosen/*
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:dosen'])
    ->prefix('dosen')
    ->name('dosen.')
    ->group(function () {

        Route::get('/dashboard', DosenDashboard::class)->name('dashboard');
        Route::get('/matkul', MatkulManager::class)->name('matkul');
        Route::get('/tugas', DosenTugasList::class)->name('tugas');
        Route::get('/tugas/buat', TugasForm::class)->name('tugas.buat');
        Route::get('/tugas/{tugas}/edit', TugasForm::class)->name('tugas.edit');
        Route::get('/tugas/{tugas}/nilai', PenilaianView::class)->name('tugas.nilai');
        Route::get('/tugas/{tugas}/nilai/{mahasiswa}', PenilaianMahasiswa::class)->name('tugas.nilai.mahasiswa');
        Route::get('/tugas/{tugas}/export-excel', [ExportController::class, 'excel'])->name('export.excel');
        Route::get('/tugas/{tugas}/export-pdf', [ExportController::class, 'pdf'])->name('export.pdf');
        Route::get('/profil', ProfilePage::class)->name('profil');
    });

// ─────────────────────────────────────────────────────────────
// MAHASISWA — /mahasiswa/*
// ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

        Route::get('/dashboard', MahasiswaDashboard::class)->name('dashboard');
        Route::get('/tugas', MahasiswaTugasList::class)->name('tugas');
        Route::get('/tugas/{tugas}/kerjakan', PengerjaanForm::class)->name('tugas.kerjakan');
        Route::get('/tugas/{tugas}/hasil', HasilNilai::class)->name('tugas.hasil');
        Route::get('/profil', ProfilePage::class)->name('profil');
    });

// ─────────────────────────────────────────────────────────────
// ANTI-CHEAT — POST endpoints, semua role login
// ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/log-activity', [AntiCheatController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('log.activity');

    Route::post('/snapshot', [AntiCheatController::class, 'snapshot'])
        ->middleware('throttle:10,1')
        ->name('snapshot');
});
