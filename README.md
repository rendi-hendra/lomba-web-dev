# 🏘️ SuaraDesa — Platform Pengaduan Warga Desa

> Sistem pengaduan warga desa berbasis web dengan **penilaian prioritas otomatis (AI)**, **countdown respon real-time**, dan **dashboard analitik** untuk aparat desa.

---

## 📑 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Prasyarat](#-prasyarat)
- [Instalasi & Menjalankan Project](#-instalasi--menjalankan-project)
  - [Cara Cepat (1 Perintah)](#cara-cepat-1-perintah)
  - [Cara Manual (Step-by-Step)](#cara-manual-step-by-step)
- [Akun Default & Login](#-akun-default--login)
- [Panduan Penggunaan](#-panduan-penggunaan)
- [Struktur Database (ERD)](#-struktur-database-erd)
- [API Specification (Internal Livewire)](#-api-specification-internal-livewire)
- [Struktur Folder Project](#-struktur-folder-project)
- [Testing](#-testing)
- [Troubleshooting](#-troubleshooting)
- [Lisensi](#-lisensi)

---

## 📖 Tentang Proyek

**SuaraDesa** adalah platform digital yang memungkinkan warga desa untuk melaporkan keluhan terkait fasilitas umum, infrastruktur, kebersihan, kesehatan, keamanan, dan layanan administrasi desa. Sistem ini secara otomatis menilai tingkat prioritas setiap aduan menggunakan algoritma berbasis keyword (AI), menentukan batas waktu respon, dan menyediakan transparansi melalui fitur pelacakan tiket real-time.

### Alur Kerja Sistem

```
Warga Mengisi Form → Sistem Membuat Kode Tiket
    → AI Menilai Urgensi, Dampak, Jumlah Warga Terdampak
    → Menghitung Skor Prioritas (Weighted Score)
    → Menentukan Tingkat Prioritas (Rendah/Sedang/Tinggi/Darurat)
    → Menentukan Batas Waktu Respon
    → Warga Menerima Tiket & Bisa Melacak Status
    → Admin/Petugas Menindaklanjuti dari Dashboard
```

---

## ✨ Fitur Utama

| # | Fitur | Deskripsi |
|---|-------|-----------|
| 1 | **Form Pengaduan** | Warga bisa mengisi judul, deskripsi, lokasi, dan kontak. Tersedia opsi kirim anonim. |
| 2 | **Kode Tiket Otomatis** | Setiap pengaduan mendapat kode tiket unik (format: `SD-2026-XXXXXX`). |
| 3 | **AI Penilaian Prioritas** | Sistem secara otomatis menilai urgensi, dampak, dan jumlah warga terdampak berdasarkan analisis keyword pada deskripsi. |
| 4 | **Skor Prioritas Berbobot** | Skor dihitung: `(Urgensi × 0.40) + (Dampak × 0.35) + (Jumlah Warga × 0.25)`. |
| 5 | **Tingkat Prioritas** | Otomatis ditentukan: **Darurat** (≥8.0), **Tinggi** (≥5.5), **Sedang** (≥3.0), **Rendah** (<3.0). |
| 6 | **Batas Waktu Respon** | Darurat: 6 jam, Tinggi: 24 jam, Sedang: 3 hari, Rendah: 7 hari. |
| 7 | **Lacak Pengaduan** | Warga dapat melacak status aduan menggunakan kode tiket dengan countdown real-time. |
| 8 | **Dashboard Admin** | Statistik total aduan, grafik bulanan, breakdown kategori, dan daftar aduan prioritas. |
| 9 | **Tindak Lanjut Aduan** | Admin/Petugas bisa mengubah status, memberikan tanggapan, dan mengeskalasi ke tingkat atas. |
| 10 | **Role-Based Access** | 3 role: Admin, Petugas, Warga. Dashboard dilindungi autentikasi. |

---

## 🛠️ Tech Stack

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| **Framework** | Laravel | 12.x |
| **PHP** | PHP | ≥ 8.2 |
| **Frontend Reactive** | Livewire (Volt) | 4.x |
| **CSS Framework** | Tailwind CSS | 4.x |
| **Build Tool** | Vite | 7.x |
| **Admin Panel** | Filament | 5.0 |
| **Autentikasi** | Laravel Fortify | 1.x |
| **Database** | MySQL | 5.7+ / 8.x |
| **Monitoring** | Laravel Telescope | 5.x |

---

## 🏗️ Arsitektur Sistem

```
┌───────────────────────────────────────────────────────────┐
│                       BROWSER (Client)                    │
│  Livewire Components + Tailwind CSS + Vite HMR            │
└────────────────────────┬──────────────────────────────────┘
                         │ HTTP / WebSocket
┌────────────────────────▼──────────────────────────────────┐
│                   LARAVEL 12 APPLICATION                  │
│                                                           │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────────┐ │
│  │   Routes     │  │  Livewire    │  │  Fortify Auth    │ │
│  │  (web.php)   │  │  Components  │  │  (username+pwd)  │ │
│  └──────┬──────┘  └──────┬───────┘  └────────┬─────────┘ │
│         │                │                    │           │
│  ┌──────▼────────────────▼────────────────────▼─────────┐ │
│  │              Services & Models                       │ │
│  │  ┌─────────────────────┐  ┌────────────────────────┐ │ │
│  │  │  PrioritasService   │  │  Eloquent Models       │ │ │
│  │  │  (AI Keyword Engine) │  │  Aduan, Laporan, etc.  │ │ │
│  │  └─────────────────────┘  └────────────────────────┘ │ │
│  └──────────────────────────────────────────────────────┘ │
│                         │                                 │
│  ┌──────────────────────▼──────────────────────────────┐  │
│  │                    MySQL Database                   │  │
│  │  roles · users · aduan · penilaian_prioritas        │  │
│  │  target_respon · laporan · v_countdown_respon (VIEW) │  │
│  └─────────────────────────────────────────────────────┘  │
└───────────────────────────────────────────────────────────┘
```

---

## 📋 Prasyarat

Sebelum memulai, pastikan sistem Anda telah terinstal:

| Software | Versi Minimum | Cek Versi |
|----------|--------------|-----------|
| **PHP** | 8.2 | `php -v` |
| **Composer** | 2.x | `composer -V` |
| **Node.js** | 18.x | `node -v` |
| **npm** | 9.x | `npm -v` |
| **MySQL** | 5.7 / 8.x | `mysql --version` |

> **💡 Tip:** Jika menggunakan [Laravel Herd](https://herd.laravel.com/) (Windows/Mac), PHP, Composer, dan Node.js sudah otomatis tersedia.

### Ekstensi PHP yang Diperlukan

```
php-mbstring, php-xml, php-curl, php-mysql, php-zip, php-bcmath, php-intl
```

---

## 🚀 Instalasi & Menjalankan Project

### Cara Cepat (1 Perintah)

Jika sudah memiliki database MySQL bernama `suaradesa`, cukup jalankan:

```bash
composer setup
```

Perintah ini akan otomatis:
1. ✅ Install dependensi PHP (`composer install`)
2. ✅ Copy `.env.example` → `.env` (jika belum ada)
3. ✅ Generate APP_KEY
4. ✅ Menjalankan migrasi database
5. ✅ Install dependensi Node.js (`npm install`)
6. ✅ Build aset frontend (`npm run build`)

Kemudian jalankan server:

```bash
composer dev
```

> Perintah `composer dev` akan menjalankan **4 proses sekaligus**: Laravel Server, Queue Worker, Log Viewer (Pail), dan Vite dev server.

---

### Cara Manual (Step-by-Step)

Ikuti langkah-langkah ini **secara berurutan**:

#### 1️⃣ Clone / Extract Repository

```bash
# Jika menggunakan Git:
git clone <repository-url> suaradesa
cd suaradesa

# Jika dari file ZIP:
# Extract ZIP → Masuk ke folder project
cd suaradesa
```

#### 2️⃣ Install Dependensi PHP

```bash
composer install
```

#### 3️⃣ Konfigurasi Environment

```bash
# Copy file environment
copy .env.example .env        # Windows
# cp .env.example .env        # Linux/Mac

# Generate application key
php artisan key:generate
```

#### 4️⃣ Buat Database MySQL

Buka MySQL client (phpMyAdmin, HeidiSQL, MySQL Workbench, atau CLI), lalu buat database:

```sql
CREATE DATABASE suaradesa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 5️⃣ Konfigurasi Database di `.env`

Buka file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=suaradesa
DB_USERNAME=root
DB_PASSWORD=
```

> **⚠️ Penting:** Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi MySQL lokal Anda. Untuk Laravel Herd / XAMPP default, biasanya username `root` tanpa password.

#### 6️⃣ Jalankan Migrasi & Seeder Database

```bash
# Migrasi tabel (membuat semua tabel + view)
php artisan migrate

# Isi data awal (role + akun admin default)
php artisan db:seed
```

> **📌 Catatan:** Migrasi akan membuat tabel `roles`, `users`, `sessions`, `aduan`, `penilaian_prioritas`, `target_respon`, `laporan`, dan database view `v_countdown_respon`.

#### 7️⃣ Install Dependensi Frontend

```bash
npm install
```

#### 8️⃣ Build Aset Frontend

```bash
# Untuk development (dengan hot-reload):
npm run dev

# Untuk production:
npm run build
```

#### 9️⃣ Jalankan Server Laravel

Buka terminal **baru** (terpisah dari Vite):

```bash
php artisan serve
```

#### 🔟 Akses Aplikasi

Buka browser dan akses:

| Halaman | URL |
|---------|-----|
| **Beranda** | `http://localhost:8000` |
| **Form Pengaduan** | `http://localhost:8000/aduan` |
| **Lacak Pengaduan** | `http://localhost:8000/aduan/lacak` |
| **Login Admin** | `http://localhost:8000/login` |
| **Dashboard Admin** | `http://localhost:8000/dashboard` |

> **💡 Jika menggunakan Laravel Herd**, URL bisa berupa `http://lomba.test` (sesuai nama folder project).

---

### 🚀 Shortcut: Menjalankan Semua Sekaligus

Gunakan perintah `composer dev` yang menjalankan **4 service** secara paralel:

```bash
composer dev
```

| Service | Warna | Keterangan |
|---------|-------|------------|
| `server` | 🔵 Biru | `php artisan serve` — Web server |
| `queue` | 🟣 Ungu | `php artisan queue:listen` — Queue worker |
| `logs` | 🔴 Merah | `php artisan pail` — Real-time log viewer |
| `vite` | 🟠 Oranye | `npm run dev` — Vite dev server + HMR |

Tekan `Ctrl+C` untuk menghentikan semua service.

---

## 🔐 Akun Default & Login

Setelah menjalankan `php artisan db:seed`, akun berikut tersedia:

| Field | Nilai |
|-------|-------|
| **Username** | `admin` |
| **Password** | `admin` |
| **Role** | Administrator |

**Cara Login:**
1. Buka `http://localhost:8000/login`
2. Masukkan **Username**: `admin`
3. Masukkan **Password**: `admin`
4. Klik tombol **Login**
5. Otomatis redirect ke `/dashboard`

> ⚠️ Sistem menggunakan **username** untuk login (bukan email).

---

## 📱 Panduan Penggunaan

### Untuk Warga (Tanpa Login)

#### A. Membuat Pengaduan Baru

1. Buka halaman utama → Klik **"Buat Pengaduan Sekarang"**
2. Isi form:
   - **Judul Pengaduan** — Ringkasan masalah (min. 5 karakter)
   - **Deskripsi Kejadian** — Detail lengkap masalah (min. 10 karakter)
   - **Alamat / Patokan Lokasi** — Lokasi kejadian
   - **Kontak** — Nomor HP (opsional, bisa kirim anonim)
3. Centang persetujuan kebijakan privasi
4. Klik **"Kirim Pengaduan"**
5. Sistem akan menampilkan **Kode Tiket** → **Simpan/catat kode ini!**

#### B. Melacak Status Pengaduan

1. Buka halaman utama → Klik **"Lacak Pengaduan"**
2. Masukkan **Kode Tiket** (contoh: `SD-2026-AB1C2D`)
3. Klik **"Lacak"**
4. Sistem menampilkan:
   - Status aduan (Diajukan / Diproses / Selesai / Diteruskan / Ditolak)
   - Tingkat prioritas & skor
   - **Countdown** batas waktu respon
   - Tanggapan dari petugas (jika sudah direspon)

### Untuk Admin/Petugas (Perlu Login)

#### A. Dashboard

- Melihat statistik: total aduan, menunggu, diproses, selesai, eskalasi
- Grafik tren aduan bulanan (6 bulan terakhir)
- Breakdown kategori (Infrastruktur, Kebersihan, Kesehatan, Keamanan, Administrasi)
- Daftar aduan terbaru dengan prioritas

#### B. Daftar Aduan

- Melihat semua aduan dengan pagination
- Filter berdasarkan status
- Pencarian berdasarkan kode tiket, deskripsi, atau alamat

#### C. Tindak Lanjut Aduan

1. Klik aduan dari daftar
2. Lihat detail aduan, hasil penilaian AI, dan countdown
3. Update status aduan (Diproses / Selesai / Ditolak)
4. Isi tanggapan resmi
5. Bisa eskalasi ke tingkat Kecamatan (status = Diteruskan)
6. Klik **"Simpan Tindak Lanjut"**

---

## 🗄️ Struktur Database (ERD)

```mermaid
erDiagram
    ROLES {
        bigint id_role PK
        varchar(50) name UK
        timestamp created_at
        timestamp updated_at
    }

    USERS {
        bigint id_user PK
        bigint id_role FK
        varchar name
        varchar(100) username UK
        varchar password
        timestamp created_at
        timestamp updated_at
    }

    ADUAN {
        bigint id_aduan PK
        varchar(40) kode_tiket UK
        text judul_aduan
        text deskripsi_aduan
        decimal(10_7) latitude
        decimal(10_7) longitude
        text alamat
        varchar(20) nohp
        enum status_aduan "diajukan|diproses|diteruskan|selesai|ditolak"
        text tanggapan
        varchar(100) petugas_penanggung_jawab
        datetime waktu_pengaduan
        timestamp created_at
        timestamp updated_at
    }

    PENILAIAN_PRIORITAS {
        bigint id_penilaian PK
        bigint id_aduan FK_UK
        decimal(5_2) nilai_urgensi
        decimal(5_2) nilai_dampak
        decimal(5_2) nilai_jumlah_warga
        decimal(5_4) bobot_urgensi "default 0.4000"
        decimal(5_4) bobot_dampak "default 0.3500"
        decimal(5_4) bobot_jumlah_warga "default 0.2500"
        decimal(6_3) skor_prioritas
        enum tingkat_prioritas "rendah|sedang|tinggi|darurat"
        datetime waktu_penilaian
        timestamp created_at
        timestamp updated_at
    }

    TARGET_RESPON {
        bigint id_target PK
        bigint id_penilaian FK_UK
        int batas_respon_menit
        datetime tenggat_respon
        datetime waktu_respon
        enum status_respon "menunggu|direspons|terlambat"
        timestamp created_at
        timestamp updated_at
    }

    LAPORAN {
        bigint id_laporan PK
        bigint id_aduan FK_UK
        bigint id_penilaian FK_UK
        bigint id_target FK_UK
        timestamp created_at
        timestamp updated_at
    }

    ROLES ||--o{ USERS : "has many"
    ADUAN ||--o| PENILAIAN_PRIORITAS : "has one"
    PENILAIAN_PRIORITAS ||--o| TARGET_RESPON : "has one"
    ADUAN ||--o| LAPORAN : "has one"
    PENILAIAN_PRIORITAS ||--o| LAPORAN : "belongs to"
    TARGET_RESPON ||--o| LAPORAN : "belongs to"
```

### Database View: `v_countdown_respon`

View ini menggabungkan tabel `aduan`, `penilaian_prioritas`, dan `target_respon` untuk menampilkan countdown real-time:

```sql
SELECT
    a.id_aduan, a.kode_tiket, a.deskripsi_aduan, a.status_aduan,
    pp.id_penilaian, pp.skor_prioritas, pp.tingkat_prioritas,
    tr.id_target, tr.batas_respon_menit, tr.tenggat_respon, tr.waktu_respon,
    -- Status countdown otomatis:
    CASE
        WHEN tr.waktu_respon IS NOT NULL THEN 'direspons'
        WHEN CURRENT_TIMESTAMP >= tr.tenggat_respon THEN 'terlambat'
        ELSE 'menunggu'
    END AS status_countdown,
    -- Sisa detik countdown:
    CASE
        WHEN tr.waktu_respon IS NOT NULL THEN 0
        ELSE GREATEST(TIMESTAMPDIFF(SECOND, CURRENT_TIMESTAMP, tr.tenggat_respon), 0)
    END AS sisa_detik
FROM aduan a
LEFT JOIN penilaian_prioritas pp ON pp.id_aduan = a.id_aduan
LEFT JOIN target_respon tr ON tr.id_penilaian = pp.id_penilaian;
```

---

## 📡 API Specification (Internal Livewire)

> **Catatan:** Aplikasi ini **tidak memiliki REST API publik**. Semua interaksi menggunakan **Livewire components** (reactive full-stack) yang berkomunikasi via HTTP POST internal ke endpoint `/livewire/update`. Berikut adalah spesifikasi fungsional setiap endpoint halaman dan action yang tersedia.

---

### Halaman Publik (Tanpa Autentikasi)

#### `GET /` — Beranda

| Property | Detail |
|----------|--------|
| **Component** | `beranda` |
| **File** | `resources/views/components/beranda/⚡index.blade.php` |
| **Deskripsi** | Halaman landing page dengan statistik umum |

**Data yang disajikan:**

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `totalLaporan` | `int` | Jumlah total semua aduan |
| `selesaiCount` | `int` | Jumlah aduan yang berstatus selesai |
| `selesaiPct` | `float` | Persentase aduan selesai |

---

#### `GET /aduan` — Form Pengaduan Baru

| Property | Detail |
|----------|--------|
| **Component** | `aduan.create` |
| **File** | `resources/views/components/aduan/⚡create.blade.php` |
| **Deskripsi** | Form pembuatan aduan baru |

**Input Properties (Bindable):**

| Property | Tipe | Validasi | Deskripsi |
|----------|------|----------|-----------|
| `judul_aduan` | `string` | `required\|min:5\|max:100` | Judul pengaduan |
| `deskripsi_aduan` | `string` | `required\|min:10` | Detail deskripsi kejadian |
| `lokasi` | `string` | `required` | Alamat / patokan lokasi |
| `kontak` | `string` | opsional | Nomor HP pelapor |
| `is_anonim` | `bool` | — | Jika `true`, kontak tidak disimpan |
| `setuju` | `bool` | `accepted` | Persetujuan kebijakan privasi |

**Action:**

| Method | Deskripsi |
|--------|-----------|
| `save()` | Validasi input → Buat kode tiket → Simpan aduan → Jalankan `PrioritasService::proses()` → Redirect ke halaman tiket |

**Response Flow:**

```
save() berhasil → Redirect ke /aduan/tiket/{kode_tiket}
save() gagal validasi → Tampilkan error message di form
save() error sistem → Tampilkan flash error message
```

**Kode Tiket yang Dihasilkan:**

```
Format: SD-{TAHUN}-{6_KARAKTER_RANDOM}
Contoh: SD-2026-A3B7XZ
```

---

#### `GET /aduan/lacak` — Lacak Pengaduan

| Property | Detail |
|----------|--------|
| **Component** | `aduan.lacak` |
| **File** | `resources/views/components/aduan/⚡lacak.blade.php` |
| **Deskripsi** | Warga memasukkan kode tiket untuk melacak status pengaduan |

**Input Properties:**

| Property | Tipe | Validasi | Deskripsi |
|----------|------|----------|-----------|
| `kode_tiket` | `string` | `required\|min:3` | Kode tiket yang ingin dilacak |

**Action:**

| Method | Deskripsi |
|--------|-----------|
| `lacak()` | Cari aduan berdasarkan kode tiket → Query view `v_countdown_respon` → Tampilkan hasil |

**Response (jika ditemukan) — `result` array:**

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `id_aduan` | `int` | ID aduan |
| `kode_tiket` | `string` | Kode tiket |
| `deskripsi` | `string` | Deskripsi aduan |
| `judul` | `string` | Judul aduan |
| `status_aduan` | `enum` | `diajukan\|diproses\|diteruskan\|selesai\|ditolak` |
| `tanggapan` | `string\|null` | Tanggapan resmi dari petugas |
| `petugas_penanggung_jawab` | `string\|null` | Nama petugas |
| `tingkat_prioritas` | `enum` | `rendah\|sedang\|tinggi\|darurat` |
| `skor_prioritas` | `float` | Skor prioritas (0-10) |
| `batas_respon_menit` | `int` | Batas respon dalam menit |
| `tenggat_respon` | `datetime\|null` | Waktu tenggat respon |
| `waktu_respon` | `datetime\|null` | Waktu respon aktual |
| `status_countdown` | `enum` | `menunggu\|direspons\|terlambat` |
| `sisa_detik` | `int` | Sisa detik countdown |

**Response (jika tidak ditemukan):**

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `errorMessage` | `string` | `"Kode tiket tidak ditemukan..."` |

---

#### `GET /aduan/tiket/{kode_tiket?}` — Bukti Tiket

| Property | Detail |
|----------|--------|
| **Component** | `aduan.tiket` |
| **File** | `resources/views/components/aduan/⚡tiket.blade.php` |
| **Deskripsi** | Menampilkan bukti tiket setelah pengaduan berhasil dikirim |

**Parameter URL:**

| Param | Tipe | Deskripsi |
|-------|------|-----------|
| `kode_tiket` | `string` (opsional) | Kode tiket yang ditampilkan |

**Data yang Disajikan — `aduanData` array:**

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `kode_tiket` | `string` | Kode tiket |
| `judul_aduan` | `string` | Judul pengaduan |
| `deskripsi_aduan` | `string` | Deskripsi pengaduan |
| `alamat` | `string` | Lokasi kejadian |
| `waktu` | `string` | Waktu pengaduan (formatted) |
| `status` | `string` | Status aduan |
| `prioritas` | `string` | Tingkat prioritas |
| `skor` | `float` | Skor prioritas |
| `tenggat` | `string` | Tenggat respon (formatted) |
| `batas_jam` | `int` | Batas respon dalam jam |

---

#### `GET /aduan/detail/{id_aduan?}` — Detail Pengaduan

| Property | Detail |
|----------|--------|
| **Component** | `aduan.detail` |
| **File** | `resources/views/components/aduan/⚡detail.blade.php` |
| **Deskripsi** | Halaman detail lengkap sebuah pengaduan |

**Parameter URL:**

| Param | Tipe | Deskripsi |
|-------|------|-----------|
| `id_aduan` | `int` (opsional) | ID aduan. Jika kosong, redirect ke `/aduan/lacak` |

---

### Halaman Admin (Memerlukan Autentikasi)

> Semua endpoint di bawah ini memerlukan login terlebih dahulu. Akses tanpa login akan di-redirect ke `/login`.

---

#### `POST /login` — Login (Fortify)

| Property | Detail |
|----------|--------|
| **Handler** | Laravel Fortify |
| **File** | `resources/views/auth/login.blade.php` |
| **Guard** | `web` |

**Request Body:**

| Field | Tipe | Validasi | Deskripsi |
|-------|------|----------|-----------|
| `username` | `string` | required | Username pengguna |
| `password` | `string` | required | Password pengguna |

**Response:**

| Kondisi | Aksi |
|---------|------|
| Login berhasil | Redirect ke `/dashboard` |
| Login gagal | Kembali ke form login dengan pesan error |
| Rate limit | Max 5 percobaan/menit per IP+username |

**Logout:**

| Method | URL | Response |
|--------|-----|----------|
| `POST` | `/logout` | Redirect ke `/` |

---

#### `GET /dashboard` — Dashboard Utama

| Property | Detail |
|----------|--------|
| **Component** | `dashboard` |
| **File** | `resources/views/components/dashboard/⚡index.blade.php` |
| **Auth** | ✅ Required |
| **Deskripsi** | Dashboard analitik untuk admin/petugas |

**Data yang Disajikan:**

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `totalCount` | `int` | Total semua aduan |
| `menungguCount` | `int` | Aduan berstatus `diajukan` |
| `diprosesCount` | `int` | Aduan berstatus `diproses` |
| `selesaiCount` | `int` | Aduan berstatus `selesai` |
| `eskalasiCount` | `int` | Aduan `diteruskan` + `ditolak` |
| `recentAduans` | `Collection` | 5 aduan terbaru + relasi prioritas & target |
| `topPriority` | `Aduan\|null` | Aduan aktif dengan skor prioritas tertinggi |
| `kategoriStats` | `array` | Breakdown per kategori: Infrastruktur, Kebersihan, Kesehatan, Keamanan, Administrasi, Lainnya |
| `months` | `array` | Label 6 bulan terakhir |
| `monthCounts` | `array` | Jumlah aduan per bulan (6 bulan terakhir) |

---

#### `GET /dashboard/aduan` — Daftar Semua Aduan

| Property | Detail |
|----------|--------|
| **Component** | `aduan.list` |
| **File** | `resources/views/components/aduan/⚡list.blade.php` |
| **Auth** | ✅ Required |
| **Deskripsi** | Daftar aduan dengan filter, pencarian, dan pagination |

**Input Properties (Bindable):**

| Property | Tipe | Default | Deskripsi |
|----------|------|---------|-----------|
| `search` | `string` | `''` | Pencarian (kode tiket, deskripsi, alamat) |
| `status` | `string` | `'semua'` | Filter status: `semua\|diajukan\|diproses\|selesai\|diteruskan\|ditolak` |

**Data yang Disajikan:**

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| `aduans` | `Paginator` | Daftar aduan (10 per halaman) |
| `totalCount` | `int` | Total semua aduan |
| `diajukanCount` | `int` | Jumlah aduan diajukan |
| `prosesCount` | `int` | Jumlah aduan diproses |
| `selesaiCount` | `int` | Jumlah aduan selesai |

---

#### `GET /dashboard/aduan/{id_aduan?}` — Tindak Lanjut Aduan

| Property | Detail |
|----------|--------|
| **Component** | `aduan.tindakan` |
| **File** | `resources/views/components/aduan/⚡tindakan.blade.php` |
| **Auth** | ✅ Required |
| **Deskripsi** | Halaman tindak lanjut & update status aduan |

**Parameter URL:**

| Param | Tipe | Deskripsi |
|-------|------|-----------|
| `id_aduan` | `int` (opsional) | ID aduan. Jika kosong, redirect ke aduan terbaru |

**Input Properties (Bindable):**

| Property | Tipe | Validasi | Deskripsi |
|----------|------|----------|-----------|
| `status_aduan` | `string` | `required\|in:diajukan,diproses,diteruskan,selesai,ditolak` | Status baru |
| `tanggapan` | `string` | `nullable\|max:1000` | Tanggapan resmi petugas |
| `petugas_penanggung_jawab` | `string` | `nullable\|max:100` | Nama petugas PJ |

**Actions:**

| Method | Deskripsi |
|--------|-----------|
| `simpanTindakLanjut()` | Update status, tanggapan, dan petugas PJ. Jika status menjadi `diproses`/`selesai`, otomatis update `waktu_respon` dan `status_respon` |
| `eskalasiKeTingkatAtas()` | Set status = `diteruskan` dan isi tanggapan eskalasi otomatis, lalu simpan |

**Logika Khusus saat Update:**

```
Jika status = diproses atau selesai:
  → Set waktu_respon = now()
  → Jika waktu_respon ≤ tenggat_respon → status_respon = 'direspons'
  → Jika waktu_respon > tenggat_respon → status_respon = 'terlambat'
```

---

### Service: PrioritasService (AI Engine)

| Property | Detail |
|----------|--------|
| **File** | `app/Services/PrioritasService.php` |
| **Trigger** | Otomatis dipanggil saat aduan baru dibuat |

**Method Utama:**

```php
public function proses(Aduan $aduan): Laporan
```

**Alur Proses:**

```
1. Analisis teks deskripsi + alamat menggunakan keyword matching
2. Hitung nilai Urgensi (1-10) dari keyword matching
3. Hitung nilai Dampak (1-10) dari keyword matching
4. Hitung nilai Jumlah Warga Terdampak (1-10) dari keyword matching
5. Hitung Skor Prioritas = (Urgensi × 0.40) + (Dampak × 0.35) + (Jumlah Warga × 0.25)
6. Tentukan Tingkat Prioritas:
   - ≥ 8.0 → Darurat
   - ≥ 5.5 → Tinggi
   - ≥ 3.0 → Sedang
   - < 3.0 → Rendah
7. Tentukan Batas Respon:
   - Darurat → 6 jam
   - Tinggi  → 24 jam
   - Sedang  → 3 hari
   - Rendah  → 7 hari
8. Simpan ke tabel: penilaian_prioritas, target_respon, laporan
```

**Contoh Keyword yang Dikenali:**

| Kategori | Contoh Keyword | Nilai |
|----------|---------------|-------|
| **Urgensi** | kebakaran (10), darurat (10), longsor (9), ambruk (9), banjir (8), bocor (6), rusak (5), kotor (3) |
| **Dampak** | meninggal (10), korban (9), kecelakaan (9), cedera (8), terisolasi (7), macet (5), gelap (4) |
| **Jumlah Warga** | ribuan (10), seluruh warga (9), ratusan (8), seluruh RT (8), masyarakat (6), desa (6) |

---

## 📁 Struktur Folder Project

```
suaradesa/
├── app/
│   ├── Actions/Fortify/          # Aksi autentikasi (create user, reset password)
│   ├── Http/Controllers/         # Controller (base)
│   ├── Models/                   # Eloquent Models
│   │   ├── Aduan.php             # Model pengaduan warga
│   │   ├── Laporan.php           # Model laporan (relasi aduan+penilaian+target)
│   │   ├── PenilaianPrioritas.php # Model penilaian AI
│   │   ├── Role.php              # Model role (admin, petugas, warga)
│   │   ├── TargetRespon.php      # Model target & batas waktu respon
│   │   └── User.php              # Model user
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── FortifyServiceProvider.php  # Konfigurasi login (username-based)
│   └── Services/
│       └── PrioritasService.php  # ⭐ AI Engine — penilaian prioritas otomatis
│
├── database/
│   ├── factories/UserFactory.php
│   ├── migrations/
│   │   ├── 0001_..._create_users_table.php    # Tabel roles, users, sessions
│   │   ├── 2026_..._create_suaradesa_tables.php  # Tabel aduan, penilaian, target, laporan + VIEW
│   │   └── 2026_..._add_tanggapan_to_aduan.php   # Kolom tanggapan & petugas PJ
│   └── seeders/
│       └── DatabaseSeeder.php    # Seed role (admin/petugas/warga) + akun admin default
│
├── resources/views/
│   ├── auth/login.blade.php      # Halaman login
│   ├── components/
│   │   ├── beranda/⚡index.blade.php     # Landing page
│   │   ├── aduan/
│   │   │   ├── ⚡create.blade.php        # Form buat pengaduan
│   │   │   ├── ⚡lacak.blade.php         # Lacak status pengaduan
│   │   │   ├── ⚡tiket.blade.php         # Bukti tiket
│   │   │   ├── ⚡detail.blade.php        # Detail pengaduan
│   │   │   ├── ⚡list.blade.php          # Daftar aduan (admin)
│   │   │   └── ⚡tindakan.blade.php      # Tindak lanjut aduan (admin)
│   │   ├── dashboard/⚡index.blade.php   # Dashboard analitik (admin)
│   │   ├── ⚡navbar.blade.php            # Komponen navbar
│   │   ├── sidebar.blade.php             # Komponen sidebar dashboard
│   │   └── dashboard-header.blade.php    # Header dashboard
│   └── layouts/app.blade.php            # Layout utama
│
├── routes/web.php                # Definisi semua route
├── config/fortify.php            # Konfigurasi Fortify (username-based auth)
├── composer.json                 # Dependensi PHP + script setup/dev
├── package.json                  # Dependensi Node.js
├── vite.config.js                # Konfigurasi Vite + Tailwind
└── .env.example                  # Template konfigurasi environment
```

---

## 🧪 Testing

```bash
# Jalankan semua test
composer test

# Atau manual:
php artisan config:clear
php artisan test
```

---

## ❓ Troubleshooting

### ❌ Error: "SQLSTATE[42S02]: Base table or view not found"

**Solusi:** Jalankan migrasi database:

```bash
php artisan migrate
```

### ❌ Error: "Class 'App\Models\Aduan' not found"

**Solusi:** Regenerate autoload:

```bash
composer dump-autoload
```

### ❌ Error: Vite manifest not found

**Solusi:** Build aset frontend:

```bash
npm run build
# atau gunakan dev server:
npm run dev
```

### ❌ Error: "Could not find driver" (MySQL)

**Solusi:** Pastikan ekstensi `pdo_mysql` aktif di `php.ini`:

```ini
extension=pdo_mysql
```

### ❌ Halaman login tidak muncul / 404

**Solusi:** Pastikan Fortify terinstal dan service provider terdaftar:

```bash
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
```

### ❌ Error saat `composer setup` / `npm install`

**Solusi:** Hapus cache dan coba ulang:

```bash
# PHP
composer clear-cache
composer install

# Node.js
rm -rf node_modules package-lock.json
npm install
```

### ❌ Database view `v_countdown_respon` error

**Solusi:** View ini menggunakan fungsi `TIMESTAMPDIFF` dan `GREATEST` yang membutuhkan **MySQL**. Pastikan `DB_CONNECTION=mysql` di file `.env`, bukan `sqlite`.

---

## 📄 Lisensi

Project ini dikembangkan untuk keperluan lomba/kompetisi Web Development.

Framework Laravel berlisensi [MIT License](https://opensource.org/licenses/MIT).
