# 🚀 Setup Tasktic — Windows + Laragon + PHP 8.2

---

## Persiapan Awal

Pastikan Laragon sudah running. Klik kanan icon tray → **Terminal**.

```bash
php -v        # PHP 8.2.x
composer -v   # Composer 2.x
node -v       # Node 18+
npm -v
```

---

## STEP 1 — Buat Project

```bash
cd C:/laragon/www
composer create-project laravel/laravel tasktic
cd tasktic
```

---

## STEP 2 — Buat Database

Buka `http://localhost/phpmyadmin`:
- Klik **New**
- Nama: `tasktic`
- Collation: `utf8mb4_unicode_ci`
- Klik **Create**

---

## STEP 3 — Konfigurasi .env

```env
APP_NAME=Tasktic
APP_URL=http://tasktic.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tasktic
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
```

> **Tip Laragon:** Virtual host `tasktic.test` otomatis dibuat kalau nama foldernya `tasktic`.

---

## STEP 4 — Install Package

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade

composer require livewire/livewire
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf

npm install && npm run build
```

---

## STEP 5 — Publish Config

```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

---

## STEP 6 — Migration

Buat semua file migration (urutan penting karena foreign key!):

```bash
php artisan make:migration add_columns_to_users_table --table=users
php artisan make:migration create_prodi_table
php artisan make:migration create_tahun_ajaran_table
php artisan make:migration create_semester_table
php artisan make:migration create_kelas_table
php artisan make:migration create_mata_kuliah_table
php artisan make:migration create_kelas_matkul_table
php artisan make:migration create_tugas_table
php artisan make:migration create_soal_table
php artisan make:migration create_jawaban_table
php artisan make:migration create_penilaian_table
php artisan make:migration create_activity_log_table
php artisan make:migration create_typing_metrics_table
php artisan make:migration create_jawaban_snapshots_table
```

Isi masing-masing file di `database/migrations/`:

### `add_columns_to_users_table`
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'dosen', 'mahasiswa'])->default('mahasiswa')->after('email');
        $table->string('nim_nidn')->nullable()->after('role');
        $table->foreignId('prodi_id')->nullable()->constrained('prodi')->nullOnDelete()->after('nim_nidn');
        $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete()->after('prodi_id');
    });
}
public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['prodi_id']);
        $table->dropForeign(['kelas_id']);
        $table->dropColumn(['role', 'nim_nidn', 'prodi_id', 'kelas_id']);
    });
}
```

### `create_prodi_table`
```php
public function up(): void
{
    Schema::create('prodi', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('kode')->unique();
        $table->timestamps();
    });
}
```

### `create_tahun_ajaran_table`
```php
public function up(): void
{
    Schema::create('tahun_ajaran', function (Blueprint $table) {
        $table->id();
        $table->string('nama'); // contoh: 2024/2025
        $table->boolean('is_active')->default(false);
        $table->timestamps();
    });
}
```

### `create_semester_table`
```php
public function up(): void
{
    Schema::create('semester', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
        $table->enum('tipe', ['Ganjil', 'Genap']);
        $table->boolean('is_active')->default(false);
        $table->timestamps();
        $table->unique(['tahun_ajaran_id', 'tipe']);
    });
}
```

### `create_kelas_table`
```php
public function up(): void
{
    Schema::create('kelas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
        $table->foreignId('semester_id')->constrained('semester')->cascadeOnDelete();
        $table->string('nama'); // TI-A, TI-B
        $table->year('angkatan');
        $table->timestamps();
    });
}
```

### `create_mata_kuliah_table`
```php
public function up(): void
{
    Schema::create('mata_kuliah', function (Blueprint $table) {
        $table->id();
        $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
        $table->foreignId('semester_id')->constrained('semester')->cascadeOnDelete();
        $table->foreignId('dosen_id')->constrained('users')->cascadeOnDelete();
        $table->string('nama');
        $table->string('kode');
        $table->tinyInteger('sks')->default(3);
        $table->timestamps();
        $table->unique(['kode', 'semester_id']);
    });
}
```

### `create_kelas_matkul_table`
```php
public function up(): void
{
    Schema::create('kelas_matkul', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
        $table->foreignId('matkul_id')->constrained('mata_kuliah')->cascadeOnDelete();
        $table->unique(['kelas_id', 'matkul_id']);
    });
}
```

### `create_tugas_table`
```php
public function up(): void
{
    Schema::create('tugas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('matkul_id')->constrained('mata_kuliah')->cascadeOnDelete();
        $table->string('judul');
        $table->text('deskripsi')->nullable();
        $table->dateTime('deadline');
        $table->boolean('allow_revision')->default(false);
        $table->softDeletes();
        $table->timestamps();
    });
}
```

### `create_soal_table`
```php
public function up(): void
{
    Schema::create('soal', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
        $table->integer('urutan');
        $table->text('pertanyaan');
        $table->string('file_path')->nullable();
        $table->timestamps();
    });
}
```

### `create_jawaban_table`
```php
public function up(): void
{
    Schema::create('jawaban', function (Blueprint $table) {
        $table->id();
        $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
        $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
        $table->longText('isi_jawaban')->nullable();
        $table->string('file_path')->nullable();
        $table->timestamp('submitted_at')->nullable();
        $table->boolean('is_final')->default(false);
        $table->unique(['soal_id', 'mahasiswa_id']);
        $table->timestamps();
    });
}
```

### `create_penilaian_table`
```php
public function up(): void
{
    Schema::create('penilaian', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
        $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
        $table->decimal('nilai', 5, 2)->nullable();
        $table->text('komentar')->nullable();
        $table->timestamp('dinilai_at')->nullable();
        $table->unique(['tugas_id', 'mahasiswa_id']);
        $table->timestamps();
    });
}
```

### `create_activity_log_table`
```php
public function up(): void
{
    Schema::create('activity_log', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
        $table->string('aksi');
        $table->timestamp('created_at')->useCurrent();
    });
}
```

### `create_typing_metrics_table`
```php
public function up(): void
{
    Schema::create('typing_metrics', function (Blueprint $table) {
        $table->id();
        $table->foreignId('jawaban_id')->constrained('jawaban')->cascadeOnDelete();
        $table->integer('keystroke_count')->default(0);
        $table->integer('char_count_final')->default(0);
        $table->decimal('keystroke_ratio', 5, 3)->default(0);
        $table->timestamp('typing_started_at')->nullable();
        $table->integer('char_count_per_minute')->default(0);
        $table->integer('auto_clear_count')->default(0);
        $table->boolean('is_suspicious')->default(false);
        $table->timestamps();
    });
}
```

### `create_jawaban_snapshots_table`
```php
public function up(): void
{
    Schema::create('jawaban_snapshots', function (Blueprint $table) {
        $table->id();
        $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
        $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
        $table->integer('char_count')->default(0);
        $table->integer('delta')->default(0);
        $table->boolean('is_suspicious')->default(false);
        $table->timestamp('snapshot_at')->useCurrent();
    });
}
```

Setelah semua diisi:
```bash
php artisan migrate
```

---

## STEP 7 — Buat Model

```bash
php artisan make:model Prodi
php artisan make:model TahunAjaran
php artisan make:model Semester
php artisan make:model Kelas
php artisan make:model MataKuliah
php artisan make:model Tugas
php artisan make:model Soal
php artisan make:model Jawaban
php artisan make:model Penilaian
php artisan make:model ActivityLog
php artisan make:model TypingMetric
php artisan make:model JawabanSnapshot
```

*(Isi relasi sesuai dokumen rancangan database v2)*

---

## STEP 8 — Middleware, Policy & Service

```bash
php artisan make:middleware RoleMiddleware
php artisan make:policy TugasPolicy --model=Tugas
php artisan make:policy JawabanPolicy --model=Jawaban
php artisan make:policy PenilaianPolicy --model=Penilaian
mkdir app/Services
# Buat app/Services/AntiCheatService.php manual
```

Daftar middleware di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

---

## STEP 9 — Livewire Components

```bash
# Admin
php artisan make:livewire Admin/UserManager
php artisan make:livewire Admin/ProdiManager
php artisan make:livewire Admin/TahunAjaranManager
php artisan make:livewire Admin/SemesterManager
php artisan make:livewire Admin/KelasManager
php artisan make:livewire Admin/MatkulManager

# Dosen
php artisan make:livewire Dosen/TugasList
php artisan make:livewire Dosen/TugasForm
php artisan make:livewire Dosen/PenilaianView

# Mahasiswa
php artisan make:livewire Mahasiswa/TugasList
php artisan make:livewire Mahasiswa/PengerjaanForm
php artisan make:livewire Mahasiswa/HasilNilai
```

---

## STEP 10 — Controller & Export

```bash
php artisan make:controller AntiCheatController
php artisan make:controller ExportController
php artisan make:export NilaiExport
```

---

## STEP 11 — Seeder

```bash
php artisan make:seeder DatabaseSeeder
```

```php
public function run(): void
{
    // Admin
    \App\Models\User::create([
        'name'     => 'Administrator',
        'email'    => 'admin@tasktic.id',
        'password' => bcrypt('password'),
        'role'     => 'admin',
    ]);

    // Data awal
    $prodi    = \App\Models\Prodi::create(['nama' => 'Teknik Informatika', 'kode' => 'TI']);
    $ta       = \App\Models\TahunAjaran::create(['nama' => '2024/2025', 'is_active' => true]);
    $semester = \App\Models\Semester::create(['tahun_ajaran_id' => $ta->id, 'tipe' => 'Ganjil', 'is_active' => true]);
    \App\Models\Kelas::create(['prodi_id' => $prodi->id, 'semester_id' => $semester->id, 'nama' => 'TI-A', 'angkatan' => 2023]);
}
```

```bash
php artisan db:seed
```

---

## STEP 12 — Storage Link & Verifikasi

```bash
php artisan storage:link
php artisan route:list
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

Buka `http://tasktic.test` → halaman login muncul ✅

Login pertama:
- **Email:** `admin@tasktic.id`
- **Password:** `password`

---

## Semua Command Sekaligus

```bash
cd C:/laragon/www
composer create-project laravel/laravel tasktic && cd tasktic
composer require laravel/breeze --dev && php artisan breeze:install blade
composer require livewire/livewire maatwebsite/excel barryvdh/laravel-dompdf
npm install && npm run build
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
# (isi semua migration dulu)
php artisan migrate
php artisan db:seed
php artisan storage:link
```

---

## Troubleshooting

| Error | Solusi |
|---|---|
| `SQLSTATE: Access denied` | Cek DB_USERNAME & DB_PASSWORD di .env |
| `Class not found` | `composer dump-autoload` |
| `Key too long` | Tambah `Schema::defaultStringLength(191)` di AppServiceProvider |
| `Foreign key constraint fails` | Pastikan urutan migrasi sudah benar |
| `npm not found` | Pakai terminal bawaan Laragon |
| Halaman 404 | Pastikan nama folder `tasktic`, restart Laragon |
| `Target class not found` | `php artisan config:clear` lalu cek namespace Livewire |

---

*Setup Guide Tasktic v2 — hierarki akademik lengkap.*
