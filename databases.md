# 🗄️ Rancangan Database Tasktic (Revisi)

> Hierarki lengkap: Tahun Ajaran → Semester → Prodi → Kelas → Mahasiswa

---

## Hierarki Sistem

```
Tahun Ajaran (2024/2025)
└── Semester (Ganjil / Genap)
    └── Prodi (Teknik Informatika)
        ├── Kelas (TI-A, TI-B)
        │   └── Mahasiswa (users dengan role mahasiswa)
        └── Mata Kuliah (Pemrograman Web)
            ├── Kelas yang mengikuti (via pivot kelas_matkul)
            └── Tugas
                └── Soal
                    └── Jawaban (per mahasiswa)
```

---

## Tabel-Tabel

---

### `tahun_ajaran`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar | Contoh: `2024/2025` |
| is_active | boolean | Hanya 1 yang aktif dalam satu waktu |
| created_at | timestamp | |
| updated_at | timestamp | |

```php
// Contoh data
['nama' => '2024/2025', 'is_active' => true]
['nama' => '2023/2024', 'is_active' => false]
```

---

### `semester`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| tahun_ajaran_id | bigint FK → tahun_ajaran | |
| tipe | enum | `Ganjil`, `Genap` |
| is_active | boolean | Semester yang sedang berjalan |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Constraint:** `UNIQUE(tahun_ajaran_id, tipe)` — tidak bisa ada 2 semester Ganjil di tahun ajaran yang sama.

```php
// Contoh data
['tahun_ajaran_id' => 1, 'tipe' => 'Ganjil', 'is_active' => true]
['tahun_ajaran_id' => 1, 'tipe' => 'Genap',  'is_active' => false]
```

---

### `prodi`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar | Contoh: `Teknik Informatika` |
| kode | varchar unique | Contoh: `TI` |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `kelas`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| prodi_id | bigint FK → prodi | |
| semester_id | bigint FK → semester | |
| nama | varchar | Contoh: `TI-A`, `TI-B` |
| angkatan | year | Contoh: `2023` |
| created_at | timestamp | |
| updated_at | timestamp | |

```php
// Contoh data
['prodi_id' => 1, 'semester_id' => 1, 'nama' => 'TI-A', 'angkatan' => 2023]
['prodi_id' => 1, 'semester_id' => 1, 'nama' => 'TI-B', 'angkatan' => 2023]
```

---

### `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | varchar | |
| email | varchar unique | |
| password | varchar | |
| role | enum | `admin`, `dosen`, `mahasiswa` |
| nim_nidn | varchar nullable | NIM untuk mahasiswa, NIDN untuk dosen |
| prodi_id | bigint FK nullable → prodi | Untuk dosen & mahasiswa |
| kelas_id | bigint FK nullable → kelas | **Hanya untuk mahasiswa** |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Catatan:** `kelas_id` hanya diisi untuk role `mahasiswa`. Dosen hanya punya `prodi_id`.
> Satu mahasiswa = satu kelas = satu prodi (tidak perlu tabel pivot).

---

### `mata_kuliah`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| prodi_id | bigint FK → prodi | |
| semester_id | bigint FK → semester | |
| dosen_id | bigint FK → users | |
| nama | varchar | |
| kode | varchar | |
| sks | tinyint | Jumlah SKS |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Constraint:** `UNIQUE(kode, semester_id)` — kode matkul unik per semester.

---

### `kelas_matkul` *(pivot — pengganti matkul_mahasiswa)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| kelas_id | bigint FK → kelas | |
| matkul_id | bigint FK → mata_kuliah | |

> **Constraint:** `UNIQUE(kelas_id, matkul_id)`
>
> Ini yang **menggantikan** tabel `matkul_mahasiswa` lama.
> Assign 1 kelas ke matkul = semua mahasiswa di kelas itu otomatis ikut matkul tersebut.

---

### `tugas`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| matkul_id | bigint FK → mata_kuliah | |
| judul | varchar | |
| deskripsi | text nullable | |
| deadline | datetime | |
| allow_revision | boolean | |
| deleted_at | timestamp nullable | Soft delete |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `soal`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| tugas_id | bigint FK → tugas | |
| urutan | integer | |
| pertanyaan | text | |
| file_path | varchar nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `jawaban`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| soal_id | bigint FK → soal | |
| mahasiswa_id | bigint FK → users | |
| isi_jawaban | longtext nullable | |
| file_path | varchar nullable | |
| submitted_at | timestamp nullable | |
| is_final | boolean | |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Constraint:** `UNIQUE(soal_id, mahasiswa_id)`

---

### `penilaian`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| tugas_id | bigint FK → tugas | |
| mahasiswa_id | bigint FK → users | |
| nilai | decimal(5,2) nullable | Range 0–100 |
| komentar | text nullable | |
| dinilai_at | timestamp nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

> **Constraint:** `UNIQUE(tugas_id, mahasiswa_id)`

---

### `activity_log`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| mahasiswa_id | bigint FK → users | |
| tugas_id | bigint FK → tugas | |
| aksi | varchar | `paste_attempt`, `drop_attempt`, `right_click_attempt`, `tab_switch`, `auto_cleared` |
| created_at | timestamp | |

---

### `typing_metrics`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| jawaban_id | bigint FK → jawaban | |
| keystroke_count | integer | Total keystroke tercatat |
| char_count_final | integer | Total karakter saat submit |
| keystroke_ratio | decimal(5,3) | keystroke / char (normal ≈ 1.0) |
| typing_started_at | timestamp nullable | |
| char_count_per_minute | integer | |
| auto_clear_count | integer | Berapa kali di-auto-clear |
| is_suspicious | boolean | |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `jawaban_snapshots`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| soal_id | bigint FK → soal | |
| mahasiswa_id | bigint FK → users | |
| char_count | integer | |
| delta | integer | Selisih dari snapshot sebelumnya |
| is_suspicious | boolean | |
| snapshot_at | timestamp | |

---

## Ringkasan Perubahan dari Versi Sebelumnya

| | Versi Lama | Versi Baru |
|---|---|---|
| Assign mahasiswa ke matkul | Manual satu-satu via `matkul_mahasiswa` | Otomatis via kelas → `kelas_matkul` |
| Entitas semester | String di kolom matkul | Tabel tersendiri, bisa diaktifkan/nonaktifkan |
| Prodi | Tidak ada | Tabel tersendiri, terhubung ke user, kelas & matkul |
| Kelas | Tidak ada | Tabel tersendiri, container mahasiswa per semester |
| Tahun Ajaran | Tidak ada | Tabel tersendiri, parent dari semester |

---

## Relasi Penting

### Cara mendapat daftar mahasiswa di suatu matkul:
```php
// Lewat kelas yang di-assign ke matkul
$mahasiswas = $matkul->kelas()              // pivot kelas_matkul
    ->with('mahasiswas')                    // mahasiswa di kelas
    ->get()
    ->pluck('mahasiswas')
    ->flatten()
    ->unique('id');
```

### Cara mahasiswa lihat matkul yang diikuti:
```php
// Mahasiswa → kelas → kelas_matkul → matkul
$matkulList = auth()->user()->kelas->matkulList;
```

### Cara cek apakah mahasiswa boleh akses tugas:
```php
// TugasPolicy: mahasiswa hanya bisa akses tugas
// dari matkul yang kelasnya di-assign ke matkul tersebut
$boleh = $tugas->mataKuliah
    ->kelas()
    ->where('kelas.id', auth()->user()->kelas_id)
    ->exists();
```

---

## Diagram Relasi (Teks)

```
tahun_ajaran ──< semester ──< kelas ──< users (mahasiswa)
                          └──< mata_kuliah
                                └── kelas_matkul (pivot)
                                    ├── kelas_id
                                    └── matkul_id

prodi ──< kelas
      └──< mata_kuliah
      └──< users (dosen & mahasiswa)

mata_kuliah ──< tugas ──< soal ──< jawaban ──< typing_metrics
                               └──< jawaban_snapshots
           └──< penilaian

users (dosen) ──< mata_kuliah
users (mahasiswa) ──< jawaban
                  └──< penilaian
                  └──< activity_log
```

---

## Urutan Migrasi (Wajib Berurutan karena FK)

```
1. prodi
2. tahun_ajaran
3. semester
4. kelas
5. users (edit: tambah prodi_id, kelas_id)
6. mata_kuliah
7. kelas_matkul
8. tugas
9. soal
10. jawaban
11. penilaian
12. activity_log
13. typing_metrics
14. jawaban_snapshots
```

---

*Database Tasktic v2 — hierarki akademik lengkap.*
