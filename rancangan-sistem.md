# 📚 Rancangan Sistem — Tasktic

> Platform Pengumpulan Tugas & Laporan | Laravel 11 + Livewire 3 + Blade + MySQL

---

## 1. Gambaran Umum

Tasktic adalah platform pengumpulan tugas dan laporan berbasis web yang memungkinkan:
- **Dosen** membuat dan mengelola tugas per mata kuliah, menilai jawaban mahasiswa, serta mengekspor rekap nilai.
- **Mahasiswa** mengerjakan dan mengumpulkan tugas secara manual (mengetik langsung, **tidak bisa paste dari luar**), serta melampirkan file pendukung.
- **Admin** mengelola seluruh data pengguna dan mata kuliah.

> **Filosofi Anti-Cheat:** Tasktic bukan lie detector — dia adalah speed bump. Tujuannya bukan membuat sistem yang tidak bisa ditembus, tapi membuat effort untuk curang lebih besar dari effort untuk jujur.

---

## 2. Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 11 |
| Frontend | Livewire 3 + Blade |
| Database | MySQL |
| Autentikasi | Laravel Breeze (multi-role) |
| File Storage | Laravel Local Storage / S3 |
| Export | Laravel Excel (Maatwebsite) + DomPDF |

---

## 3. Role & Hak Akses

### 3.1 Admin
- Mengelola data user (dosen & mahasiswa): tambah, edit, hapus, reset password
- Mengelola mata kuliah: tambah, edit, hapus
- Assign dosen ke mata kuliah
- Assign mahasiswa ke mata kuliah
- Melihat seluruh aktivitas sistem termasuk activity log anti-cheat

### 3.2 Dosen
- Melihat daftar mata kuliah yang diampu
- Membuat, mengedit, dan menghapus tugas per mata kuliah *(soft delete jika sudah ada jawaban)*
- Menambahkan soal (bisa multiple soal per tugas) beserta file/gambar lampiran
- Mengatur kebijakan revisi (boleh revisi / tidak boleh revisi)
- Mengatur deadline pengumpulan
- Melihat status pengumpulan seluruh mahasiswa (sudah/belum kumpul)
- Menilai jawaban mahasiswa (nilai angka 0–100 + komentar/feedback)
- Melihat flag kecurigaan pada jawaban mahasiswa (keystroke ratio tidak normal)
- Mengekspor rekap nilai ke Excel dan PDF

### 3.3 Mahasiswa
- Melihat daftar mata kuliah yang **diikuti saja**
- Melihat daftar tugas beserta status (belum dikerjakan / sudah dikumpul / sudah dinilai)
- Mengerjakan tugas: mengetik jawaban manual per soal (**anti-paste aktif berlapis**)
- Melampirkan file pendukung jawaban (tipe & ukuran dibatasi)
- Melihat nilai dan feedback dari dosen setelah dinilai

---

## 4. Struktur Database

### Tabel: `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | varchar | Nama lengkap |
| email | varchar unique | |
| password | varchar | |
| role | enum | `admin`, `dosen`, `mahasiswa` |
| nim_nidn | varchar nullable | NIM untuk mahasiswa, NIDN untuk dosen |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel: `mata_kuliah`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar | Nama mata kuliah |
| kode | varchar unique | Kode matkul |
| dosen_id | bigint FK → users | |
| semester | varchar | Contoh: Genap 2024/2025 |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel: `matkul_mahasiswa` *(pivot)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| matkul_id | bigint FK → mata_kuliah | |
| mahasiswa_id | bigint FK → users | |

### Tabel: `tugas`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| matkul_id | bigint FK → mata_kuliah | |
| judul | varchar | |
| deskripsi | text nullable | Petunjuk umum tugas |
| deadline | datetime | |
| allow_revision | boolean | `true` = mahasiswa boleh revisi sebelum deadline |
| deleted_at | timestamp nullable | Soft delete — data jawaban tetap aman |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel: `soal`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| tugas_id | bigint FK → tugas | |
| urutan | integer | Nomor urut soal |
| pertanyaan | text | Isi soal/pertanyaan |
| file_path | varchar nullable | Path file/gambar lampiran soal |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel: `jawaban`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| soal_id | bigint FK → soal | |
| mahasiswa_id | bigint FK → users | |
| isi_jawaban | longtext | Jawaban yang diketik manual |
| file_path | varchar nullable | Path file lampiran pendukung |
| submitted_at | timestamp nullable | Waktu submit final |
| is_final | boolean | `false` = draft, `true` = sudah dikumpul |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Constraint:** `UNIQUE(soal_id, mahasiswa_id)` — mencegah duplikasi jawaban akibat concurrent submission.

### Tabel: `penilaian`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| tugas_id | bigint FK → tugas | |
| mahasiswa_id | bigint FK → users | |
| nilai | decimal(5,2) nullable | Nilai angka, wajib antara 0–100 |
| komentar | text nullable | Feedback dari dosen |
| dinilai_at | timestamp nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel: `activity_log` *(anti-cheat log)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| mahasiswa_id | bigint FK → users | |
| tugas_id | bigint FK → tugas | |
| aksi | varchar | `paste_attempt`, `drop_attempt`, `right_click_attempt`, `tab_switch`, `auto_cleared` |
| created_at | timestamp | Waktu kejadian |

### Tabel: `typing_metrics` *(anti-cheat backend)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| jawaban_id | bigint FK → jawaban | |
| soal_id | bigint FK → soal | |
| mahasiswa_id | bigint FK → users | |
| tugas_id | bigint FK → tugas | |
| keystroke_count | integer | Total keystroke tercatat selama pengerjaan |
| char_count_final | integer | Total karakter jawaban saat submit |
| keystroke_ratio | decimal(5,3) | keystroke_count / char_count_final (normal ≈ 1.0) |
| typing_started_at | timestamp | Saat mahasiswa mulai mengetik |
| char_count_per_minute | integer | Rata-rata karakter per menit |
| auto_clear_count | integer | Berapa kali form di-auto-clear |
| is_suspicious | boolean | `true` jika ratio atau CPM tidak wajar |
| created_at | timestamp | |

### Tabel: `jawaban_snapshots` *(incremental snapshot)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| soal_id | bigint FK → soal | |
| mahasiswa_id | bigint FK → users | |
| char_count | integer | Jumlah karakter saat snapshot |
| delta | integer | Selisih dari snapshot sebelumnya |
| is_suspicious | boolean | `true` jika delta > threshold |
| snapshot_at | timestamp | |

---

## 5. Mekanisme Anti-Cheat (Lengkap)

### Layer Overview

```
Layer 1–4 : Frontend (deterrent, bisa dibypass DevTools)
Layer 5–7 : Backend (enforcement sesungguhnya, tidak bisa dibypass)
```

### Layer 1 — Blokir Paste Event (Frontend)
Event `paste` di-`preventDefault()` di semua textarea jawaban.
Mahasiswa yang tidak tahu DevTools langsung terhenti di sini.

### Layer 2 — Blokir Keyboard Shortcut (Frontend)
Event `keydown` intercept `Ctrl+V` / `Cmd+V` sebelum diproses browser.
Paste via keyboard tidak berfungsi sama sekali.

### Layer 3 — Blokir Drag & Drop (Frontend)
Event `drop` di-`preventDefault()`.
Drag teks dari aplikasi lain ke textarea diblokir.

### Layer 4 — Atribut HTML (Frontend)
`autocomplete="off"`, `autocorrect="off"`, `autocapitalize="off"` di semua textarea.
Mencegah browser autofill inject teks.

### Layer 5 — Incremental Snapshot (Backend) ⭐
Setiap 30 detik, karakter count dikirim ke backend.
Backend bandingkan dengan snapshot sebelumnya:

| Delta Karakter | Status | Aksi |
|---|---|---|
| < 200 | ✅ Normal | Lanjut |
| 200–400 | ⚠️ Warning | Catat di log, tidak clear |
| > 400 | 🚨 Mencurigakan | **Auto-clear form dari server** |
| > 500 | 🚨 Sangat mencurigakan | Auto-clear + **lock form 60 detik** |

> **Mengapa auto-clear dari server lebih kuat?** Karena mahasiswa tidak bisa mencegah response dari server, berbeda dengan event listener frontend yang bisa dihapus via DevTools.

Setiap kejadian auto-clear, lock duration **berlipat ganda:**
- Auto-clear ke-1 → lock 60 detik
- Auto-clear ke-2 → lock 120 detik
- Auto-clear ke-3 → lock 240 detik

### Layer 6 — Keystroke Counter (Backend) ⭐
Setiap `keydown` yang terjadi di textarea dicatat dan dikirim secara berkala.
Saat submit, backend hitung:

```
keystroke_ratio = keystroke_count / char_count_final

Normal      : ratio ≈ 0.8 – 1.5 (ada backspace, typo, dll)
Mencurigakan: ratio < 0.1 (sangat sedikit ketukan untuk karakter sebanyak itu)
```

Jawaban tetap tersimpan, tapi dosen mendapat **flag merah** di halaman penilaian:
> ⚠️ *"Perlu diperiksa: hanya 12 keystroke untuk 900 karakter (ratio: 0.013)"*

### Layer 7 — Threshold Dinamis (Backend)
Threshold tidak flat — disesuaikan dengan waktu pengerjaan:

| Waktu | Max Delta per 30 detik |
|---|---|
| 0–60 detik pertama | 300 karakter (longgar, baru mulai) |
| Setelah menit ke-1 | 200 karakter (normal) |
| Kapanpun | > 400 → auto-clear tanpa pengecualian |

---

## 6. Studi Kasus Alur Anti-Cheat

### 👤 Mahasiswa Jujur (Budi)
```
00:00 → Buka halaman, mulai ngetik
00:30 → Snapshot 1: 87 karakter, delta 87   → ✅ AMAN
01:00 → Snapshot 2: 183 karakter, delta 96   → ✅ AMAN
01:30 → Snapshot 3: 271 karakter, delta 88   → ✅ AMAN
...
06:00 → Snapshot 12: 1.050 karakter, delta 95 → ✅ AMAN
07:30 → Submit
        keystroke_count : 1.120
        char_count_final: 1.050
        ratio           : 1.07 → ✅ NORMAL
        Jawaban tersimpan ✅
```

### 👤 Mahasiswa Curang Level 1 — Paste Langsung (Candra)
```
00:03 → Ctrl+V
        Layer 2 aktif → e.preventDefault()
        Paste dibatalkan sebelum terjadi ✅
        Toast: "Paste tidak diizinkan"
        Log: paste_attempt dicatat
```

### 👤 Mahasiswa Curang Level 2 — Bypass DevTools (Candra)
```
00:00 → Hapus event listener via DevTools Console
00:03 → Ctrl+V berhasil → 1.200 karakter masuk sekaligus
00:30 → Snapshot 1: delta 1.200
        🚨 AUTO-CLEAR dari server
        Form dikosongkan paksa
        Lock 60 detik
        Log: auto_cleared dicatat
01:31 → Coba paste lagi
02:00 → Snapshot 2: delta 1.200 lagi
        🚨 AUTO-CLEAR ke-2
        Lock 120 detik
        (lock berlipat ganda setiap kejadian)
```

### 👤 Mahasiswa Curang Level 3 — Paste Sedikit-sedikit (Candra)
```
00:30 → Paste 100 karakter   → delta 100 → ✅ lolos snapshot
01:00 → Paste 100 karakter   → delta 100 → ✅ lolos snapshot
01:30 → Paste 100 karakter   → delta 100 → ✅ lolos snapshot
...
09:00 → Submit 900 karakter
        keystroke_count : 12   ← sangat sedikit!
        char_count_final: 900
        ratio           : 0.013 → 🚨 FLAGGED

        Jawaban tersimpan, tapi dosen dapat notifikasi:
        ⚠️ "Jawaban perlu diperiksa.
            Hanya 12 keystroke untuk 900 karakter (ratio: 0.013)"
```

### Ringkasan Pertahanan per Level

| Kemampuan Mahasiswa | Skenario | Hasil |
|---|---|---|
| Tidak tahu DevTools | Paste Ctrl+V | 🚫 Diblokir frontend |
| Tahu DevTools | Paste sekaligus | 🚨 Auto-clear backend |
| Cukup teknis | Paste sedikit-sedikit | ⚠️ Flagged ke dosen |
| Ngetik cepat (jujur) | Ngetik normal | ✅ Submit lancar |

---

## 7. Struktur Folder Laravel

```
app/
├── Models/
│   ├── User.php
│   ├── MataKuliah.php
│   ├── Tugas.php
│   ├── Soal.php
│   ├── Jawaban.php
│   ├── Penilaian.php
│   ├── ActivityLog.php
│   ├── TypingMetric.php
│   └── JawabanSnapshot.php
│
├── Livewire/
│   ├── Admin/
│   │   ├── UserManager.php
│   │   └── MatkulManager.php
│   ├── Dosen/
│   │   ├── TugasList.php
│   │   ├── TugasForm.php
│   │   ├── PenilaianView.php
│   │   └── ExportNilai.php
│   └── Mahasiswa/
│       ├── TugasList.php
│       ├── PengerjaanForm.php
│       └── HasilNilai.php
│
├── Http/
│   ├── Controllers/
│   │   ├── AntiCheatController.php   ← snapshot + keystroke + log
│   │   └── ExportController.php
│   └── Middleware/
│       └── RoleMiddleware.php
│
├── Policies/
│   ├── TugasPolicy.php
│   ├── JawabanPolicy.php
│   └── PenilaianPolicy.php
│
└── Services/
    └── AntiCheatService.php          ← logic deteksi terpusat
```

---

## 8. AntiCheatService

```php
// app/Services/AntiCheatService.php

class AntiCheatService
{
    // Threshold delta karakter per 30 detik
    const THRESHOLD_WARNING    = 200;
    const THRESHOLD_CLEAR      = 400;
    const THRESHOLD_CLEAR_LOCK = 500;

    // Threshold waktu awal (lebih longgar)
    const THRESHOLD_EARLY      = 300; // 0–60 detik pertama

    // Keystroke ratio minimum yang dianggap normal
    const MIN_KEYSTROKE_RATIO  = 0.1;

    public function checkSnapshot(int $soalId, int $mahasiswaId, int $charCount): array
    {
        $last = JawabanSnapshot::where('soal_id', $soalId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->latest('snapshot_at')->first();

        $delta = $last ? ($charCount - $last->char_count) : $charCount;

        // Tentukan threshold berdasarkan waktu
        $isEarly   = !$last || $last->snapshot_at->diffInSeconds(now()) < 60;
        $threshold = $isEarly ? self::THRESHOLD_EARLY : self::THRESHOLD_CLEAR;

        $isSuspicious = $delta > $threshold;

        // Simpan snapshot
        JawabanSnapshot::create([
            'soal_id'      => $soalId,
            'mahasiswa_id' => $mahasiswaId,
            'char_count'   => $charCount,
            'delta'        => $delta,
            'is_suspicious'=> $isSuspicious,
            'snapshot_at'  => now(),
        ]);

        if ($delta > self::THRESHOLD_CLEAR_LOCK) {
            return ['action' => 'clear_and_lock', 'lock_seconds' => $this->getLockDuration($mahasiswaId)];
        }
        if ($delta > self::THRESHOLD_CLEAR) {
            return ['action' => 'clear'];
        }
        if ($delta > self::THRESHOLD_WARNING) {
            return ['action' => 'warn'];
        }

        return ['action' => 'ok'];
    }

    public function checkKeystrokeRatio(Jawaban $jawaban, int $keystrokeCount): bool
    {
        $charCount = mb_strlen($jawaban->isi_jawaban);
        $ratio = $charCount > 0 ? $keystrokeCount / $charCount : 0;
        return $ratio < self::MIN_KEYSTROKE_RATIO;
    }

    private function getLockDuration(int $mahasiswaId): int
    {
        // Berlipat ganda setiap auto-clear
        $count = ActivityLog::where('mahasiswa_id', $mahasiswaId)
            ->where('aksi', 'auto_cleared')
            ->count();
        return 60 * pow(2, $count); // 60, 120, 240, 480, ...
    }
}
```

---

## 9. Ringkasan Celah & Solusi

| # | Celah | Solusi | Status |
|---|---|---|---|
| 1 | Paste via Ctrl+V | Layer 2: keydown preventDefault | ✅ |
| 2 | Paste via klik kanan | Layer 1: paste event preventDefault | ✅ |
| 3 | Drag & drop teks | Layer 3: drop event preventDefault | ✅ |
| 4 | Browser autofill | Layer 4: autocomplete="off" | ✅ |
| 5 | Hapus event listener (DevTools) | Layer 5: incremental snapshot + auto-clear server | ✅ |
| 6 | Paste sedikit-sedikit | Layer 6: keystroke ratio check | ✅ |
| 7 | Tab switching | visibilitychange event + log | ✅ |
| 8 | Submit setelah deadline | Validasi now() > deadline di backend | ✅ |
| 9 | Akses tugas orang lain | Laravel Policy per resource | ✅ |
| 10 | Activity log di-spam | throttle:30,1 + CSRF | ✅ |
| 11 | File upload berbahaya | Validasi mimes + simpan nama acak | ✅ |
| 12 | Nilai di luar 0–100 | Validasi min:0\|max:100 backend | ✅ |
| 13 | Concurrent submission | UNIQUE constraint + updateOrCreate | ✅ |
| 14 | Dosen hapus tugas berisi jawaban | Soft delete | ✅ |

---

## 10. Dependency / Package

```bash
composer require laravel/breeze --dev
composer require livewire/livewire
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

---

*Rancangan Tasktic v3 — sistem anti-cheat berlapis 7 layer frontend + backend.*
