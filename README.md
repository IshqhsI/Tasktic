# Tasktic

Platform pengumpulan tugas dan laporan akademik berbasis web dengan sistem anti-cheat bawaan.

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-4E56A6?style=flat&logo=livewire&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=flat&logo=postgresql&logoColor=white)

---

## Fitur Utama

### 👨‍💼 Admin
- Manajemen pengguna (admin, dosen, mahasiswa)
- Manajemen program studi, tahun ajaran, semester, kelas
- Aktivasi semester & tahun ajaran

### 👨‍🏫 Dosen
- Manajemen mata kuliah + assign kelas
- Buat & edit tugas dengan soal dinamis
- Penilaian per mahasiswa — full page, dua kolom (jawaban + form nilai)
- Navigasi prev/next antar mahasiswa saat menilai
- Lihat ringkasan anomali anti-cheat per mahasiswa
- Export rekap nilai ke **CSV/Excel** dan **PDF** (print browser)

### 🎓 Mahasiswa
- Dashboard tugas aktif + nilai terbaru
- Kerjakan tugas dengan auto-save draft per soal
- Lihat hasil nilai + komentar dosen

### 🛡️ Anti-Cheat (7 Layer)
| Layer | Mekanisme |
|---|---|
| L1 | Blokir `Ctrl+V` / paste |
| L2 | Blokir drag & drop teks |
| L3 | Blokir klik kanan |
| L4 | Deteksi pindah tab (`visibilitychange`) |
| L5 | Snapshot isi jawaban setiap 30 detik |
| L6 | Analisis rasio keystroke vs karakter baru |
| L7 | Log semua aktivitas mencurigakan ke database |

---

## Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 11 |
| Frontend | Livewire 3 + Alpine.js |
| Styling | Tailwind CSS (CDN) |
| Database | PostgreSQL |
| Auth | Laravel Breeze |
| Server lokal | Laragon (PHP 8.2) |

---

## Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AntiCheatController.php
│   │   └── ExportController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Responses/
│       └── LoginResponse.php
├── Livewire/
│   ├── Admin/
│   │   ├── Dashboard.php
│   │   ├── UserManager.php
│   │   ├── ProdiManager.php
│   │   ├── TahunAjaranManager.php
│   │   ├── SemesterManager.php
│   │   └── KelasManager.php
│   ├── Dosen/
│   │   ├── Dashboard.php
│   │   ├── MatkulManager.php
│   │   ├── TugasList.php
│   │   ├── TugasForm.php
│   │   ├── PenilaianView.php
│   │   └── PenilaianMahasiswa.php
│   ├── Mahasiswa/
│   │   ├── Dashboard.php
│   │   ├── TugasList.php
│   │   ├── PengerjaanForm.php
│   │   └── HasilNilai.php
│   └── Shared/
│       └── ProfilePage.php
├── Models/                    # 13 model
├── Policies/                  # TugasPolicy, JawabanPolicy, PenilaianPolicy
├── Providers/
│   ├── AppServiceProvider.php
│   └── ViewServiceProvider.php
└── Services/
    └── AntiCheatService.php

database/
└── seeders/                   # 7 seeder
```

---

## Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- PostgreSQL
- Node.js (untuk build assets jika diperlukan)
- Laragon / XAMPP / Valet

### Langkah Setup

**1. Clone repository**
```bash
git clone https://github.com/username/tasktic.git
cd tasktic
```

**2. Install dependency**
```bash
composer install
```

**3. Copy file environment**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Konfigurasi `.env`**
```env
APP_NAME=Tasktic
APP_URL=http://tasktic.test

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tasktic
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

**5. Konfigurasi Livewire**

Buka `config/livewire.php`, pastikan:
```php
'make_command' => [
    'type' => 'class',  // bukan 'sfc'
],
```

**6. Daftarkan ViewServiceProvider**

Di `bootstrap/app.php` atau `config/app.php`:
```php
App\Providers\ViewServiceProvider::class,
```

**7. Migrate & seed database**
```bash
php artisan migrate:fresh --seed
```

**8. Jalankan server**
```bash
php artisan serve
```
Atau akses langsung via `http://tasktic.test` jika menggunakan Laragon.

---

## Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@tasktic.id | password |
| Dosen | dosen@tasktic.id | password |
| Mahasiswa | mahasiswa@tasktic.id | password |

> ⚠️ Ganti semua password default sebelum deploy ke production.

---

## Alur Penggunaan

```
Admin
 └── Setup: Prodi → Tahun Ajaran → Semester (aktifkan) → Kelas → User

Dosen
 └── Mata Kuliah (assign kelas) → Buat Tugas → Nilai Mahasiswa → Export

Mahasiswa
 └── Dashboard → Kerjakan Tugas → Lihat Nilai
```

---

## Anti-Cheat — Cara Kerja

Saat mahasiswa mengerjakan tugas, sistem secara otomatis:

1. **Memblokir** paste, drag & drop, dan klik kanan pada area jawaban
2. **Mendeteksi** perpindahan tab browser
3. **Mengirim snapshot** isi jawaban ke server setiap 30 detik
4. **Menganalisis** delta karakter vs keystroke — delta besar tanpa ketikan → flagged
5. **Mencatat** semua aktivitas ke tabel `activity_log` dan `jawaban_snapshots`

Dosen dapat melihat ringkasan anomali di halaman penilaian — badge merah menandakan aktivitas mencurigakan tinggi (≥5 kejadian).

---

## Export

### Excel (CSV)
- Format UTF-8 dengan BOM agar Excel bisa membaca karakter Indonesia
- Kolom: No, Nama, NIM, Prodi, Kelas, Nilai, Kategori, Komentar, Waktu Dinilai, Anomali
- Unduh via tombol di halaman penilaian tugas

### PDF
- Render sebagai HTML print-friendly
- Buka di tab baru → `Ctrl+P` → **Save as PDF**
- Berisi statistik ringkasan + tabel nilai lengkap

---

## Deployment (Production)

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

Pastikan:
- `APP_ENV=production`
- `APP_DEBUG=false`
- Storage & bootstrap/cache writable

---

## Lisensi

MIT License — bebas digunakan dan dimodifikasi.
