# 👥 Peran & Cara Kerja Tasktic

---

## Hierarki Pengguna

```
Admin
├── Dosen
│   └── Mata Kuliah → Tugas → Soal
└── Mahasiswa
    └── Kelas → Matkul yang diikuti → Jawaban
```

---

## 🔴 Admin — Setup Sistem

Admin hanya bekerja di **awal semester**. Setelah data terisi, Admin hampir tidak perlu login lagi.

### Yang Admin Lakukan:

**1. Isi Data Master (sekali saja)**
- Tambah Prodi (Teknik Informatika, Sistem Informasi, dll)
- Tidak perlu diulang kecuali ada prodi baru

**2. Tiap Tahun Ajaran Baru**
- Buat Tahun Ajaran (contoh: `2025/2026`)
- Aktifkan Tahun Ajaran yang berjalan
- Buat Semester Ganjil / Genap di bawah tahun ajaran tersebut
- Aktifkan semester yang sedang berjalan

**3. Tiap Awal Semester**
- Buat Kelas baru (contoh: `TI-A 2023`, `TI-B 2023`)
- Tambah akun mahasiswa baru (bisa bulk/import)
- Assign mahasiswa ke kelas masing-masing
- Tambah akun dosen jika ada dosen baru

**4. Setelah Itu Admin Selesai.**
Dosen yang handle sisanya.

### Alur Kerja Admin:
```
Login → Dashboard Admin
     ├── Prodi       → Tambah/edit prodi
     ├── Tahun Ajaran→ Buat & aktifkan tahun ajaran
     ├── Semester    → Buat & aktifkan semester
     ├── Kelas       → Buat kelas, assign mahasiswa
     └── User        → Tambah dosen & mahasiswa, reset password
```

### Yang TIDAK dilakukan Admin:
- ❌ Tidak membuat Mata Kuliah
- ❌ Tidak membuat Tugas
- ❌ Tidak menilai jawaban
- ❌ Tidak assign kelas ke matkul (itu tugas Dosen)

---

## 🔵 Dosen — Operasional Harian

Dosen adalah pengguna paling aktif di Tasktic. Semua yang berhubungan dengan akademik ada di tangan dosen.

### Yang Dosen Lakukan:

**1. Setup Mata Kuliah (awal semester)**
- Buat Mata Kuliah (nama, kode, SKS)
- Mata kuliah otomatis terhubung ke semester yang aktif
- Assign kelas yang mengikuti matkul tersebut
  - Contoh: Pemrograman Web → assign ke TI-A dan TI-B
  - Setelah assign, semua mahasiswa di TI-A dan TI-B otomatis terdaftar

**2. Kelola Tugas (sepanjang semester)**
- Buat Tugas baru di dalam matkul
- Isi judul, deskripsi/petunjuk pengerjaan
- Set deadline
- Set kebijakan revisi (mahasiswa boleh edit jawaban sebelum deadline atau tidak)
- Tambah Soal (bisa multiple soal per tugas)
- Upload file/gambar lampiran per soal jika perlu

**3. Pantau Pengumpulan**
- Lihat status per mahasiswa: belum kumpul / sudah kumpul / sudah dinilai
- Lihat activity log: berapa kali mahasiswa mencoba paste, pindah tab, dll
- Lihat flag kecurigaan: jawaban yang keystroke ratio-nya tidak normal

**4. Penilaian**
- Buka jawaban tiap mahasiswa
- Beri nilai angka (0–100)
- Beri komentar/feedback
- Mahasiswa otomatis bisa lihat nilai & feedback setelah dinilai

**5. Export**
- Export rekap nilai seluruh mahasiswa ke Excel atau PDF
- Bisa dilakukan kapan saja, tidak harus tunggu semua dinilai

### Alur Kerja Dosen:
```
Login → Dashboard Dosen (daftar matkul yang diampu)
     │
     ├── [Awal Semester]
     │   └── Buat Matkul → Assign Kelas → Selesai setup
     │
     ├── [Sepanjang Semester]
     │   └── Pilih Matkul → Buat Tugas
     │                    → Tambah Soal (+ lampiran opsional)
     │                    → Set Deadline & Kebijakan Revisi
     │                    → Publish
     │
     ├── [Setelah Deadline]
     │   └── Pilih Tugas → Lihat daftar mahasiswa
     │                   → Klik nama mahasiswa → Lihat jawaban
     │                   → Cek activity log & flag kecurigaan
     │                   → Input nilai + komentar → Simpan
     │
     └── [Kapan Saja]
         └── Export nilai → Excel / PDF
```

### Yang TIDAK dilakukan Dosen:
- ❌ Tidak mengelola user
- ❌ Tidak membuat prodi / kelas / semester
- ❌ Tidak bisa lihat matkul dosen lain

---

## 🟢 Mahasiswa — Mengerjakan Tugas

Mahasiswa adalah pengguna dengan alur paling sederhana — fokus pada pengerjaan tugas.

### Yang Mahasiswa Lakukan:

**1. Lihat Daftar Tugas**
- Setelah login, mahasiswa langsung lihat semua matkul yang diikuti (sesuai kelasnya)
- Di tiap matkul, terlihat daftar tugas beserta status:
  - 🔴 Belum dikerjakan
  - 🟡 Draft (sudah mulai tapi belum dikumpul)
  - 🟢 Sudah dikumpul
  - ⭐ Sudah dinilai

**2. Mengerjakan Tugas**
- Klik tugas → baca soal
- Ketik jawaban manual di textarea (tidak bisa paste dari luar)
- Bisa upload file pendukung per soal (maks 10MB, format: pdf/jpg/png/docx)
- Bisa simpan sebagai Draft dulu, lanjut nanti sebelum deadline
- Klik **Kumpulkan** untuk submit final

**3. Lihat Nilai & Feedback**
- Setelah dosen menilai, mahasiswa bisa lihat:
  - Nilai angka
  - Komentar/feedback dari dosen
  - Per soal atau keseluruhan tugas

### Alur Kerja Mahasiswa:
```
Login → Dashboard (daftar matkul)
     │
     └── Pilih Matkul → Lihat daftar tugas
                      │
                      ├── [Tugas belum dikerjakan]
                      │   └── Klik "Kerjakan"
                      │       → Baca soal & lampiran dosen
                      │       → Ketik jawaban (anti-paste aktif)
                      │       → Upload file pendukung (opsional)
                      │       → Simpan Draft / Kumpulkan
                      │
                      └── [Tugas sudah dinilai]
                          └── Klik "Lihat Hasil"
                              → Nilai + feedback dosen
```

### Batasan Mahasiswa:
- ❌ Tidak bisa paste teks dari luar (diblokir berlapis)
- ❌ Tidak bisa submit setelah deadline
- ❌ Tidak bisa edit jawaban jika `allow_revision = false`
- ❌ Tidak bisa lihat jawaban mahasiswa lain
- ❌ Tidak bisa lihat nilai sebelum dosen menilai

---

## 🔄 Alur Lengkap Satu Siklus Semester

```
[ADMIN — Awal Semester]
1. Buat Tahun Ajaran & Semester → aktifkan
2. Buat Kelas → assign mahasiswa
3. Tambah akun dosen & mahasiswa baru
   ↓

[DOSEN — Awal Semester]
4. Buat Mata Kuliah
5. Assign kelas ke matkul
   ↓

[DOSEN — Sepanjang Semester]
6. Buat Tugas + Soal + set Deadline
   ↓

[MAHASISWA — Selama Deadline Buka]
7. Lihat tugas baru
8. Kerjakan → ketik jawaban manual
9. Kumpulkan sebelum deadline
   ↓

[DOSEN — Setelah Deadline]
10. Buka hasil pengumpulan
11. Cek activity log & flag kecurigaan
12. Nilai tiap mahasiswa + beri feedback
13. Export rekap nilai
    ↓

[MAHASISWA — Setelah Dinilai]
14. Lihat nilai & feedback
    ↓

[Ulangi dari langkah 6 untuk tugas berikutnya]
```

---

## 📊 Ringkasan Peran

| Fitur | Admin | Dosen | Mahasiswa |
|---|:---:|:---:|:---:|
| Kelola Prodi | ✅ | ❌ | ❌ |
| Kelola Tahun Ajaran & Semester | ✅ | ❌ | ❌ |
| Kelola Kelas | ✅ | ❌ | ❌ |
| Kelola User (dosen & mahasiswa) | ✅ | ❌ | ❌ |
| Buat Mata Kuliah | ❌ | ✅ | ❌ |
| Assign Kelas ke Matkul | ❌ | ✅ | ❌ |
| Buat Tugas & Soal | ❌ | ✅ | ❌ |
| Lihat Status Pengumpulan | ❌ | ✅ | ❌ |
| Pantau Activity Log & Flag | ❌ | ✅ | ❌ |
| Nilai & Beri Feedback | ❌ | ✅ | ❌ |
| Export Nilai | ❌ | ✅ | ❌ |
| Kerjakan Tugas | ❌ | ❌ | ✅ |
| Upload File Pendukung | ❌ | ❌ | ✅ |
| Lihat Nilai & Feedback | ❌ | ❌ | ✅ |

---

*Dokumen Peran & Cara Kerja Tasktic v1*
