<div align="center">
  <br>
  <h1>Sistem Pendukung Keputusan Penentuan Lokasi Cabang Baru Saung Aqiqah Menggunakan Metode TOPSIS</h1>
  <p>
    <b>A Modern, Semi-Dynamic Decision Support System built with Laravel 13 & Tailwind CSS</b>
  </p>
  
  [![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
  [![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
  [![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
  [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)
  [![Status](https://img.shields.io/badge/Status-Completed-success?style=for-the-badge)](#)
</div>

---

## 🎨 Project Preview

<div align="center">
  <img src="https://via.placeholder.com/800x400/1a202c/ffffff?text=Login+Page+Screenshot" alt="Login Page">
  <p><em>Autentikasi Aman & Bersih</em></p>

  <img src="https://via.placeholder.com/800x400/1a202c/ffffff?text=Dashboard+Screenshot" alt="Dashboard">
  <p><em>Dashboard Analitik Interaktif</em></p>

  <img src="https://via.placeholder.com/800x400/1a202c/ffffff?text=TOPSIS+Calculation+Screenshot" alt="TOPSIS Calculation">
  <p><em>Transparansi Perhitungan Matematis TOPSIS</em></p>

  <img src="https://via.placeholder.com/800x400/1a202c/ffffff?text=Recommendation+Result+Screenshot" alt="Recommendation Result">
  <p><em>Hasil Perankingan Rekomendasi Lokasi</em></p>
</div>

---

## ✨ Features

- 🔐 **Authentication & Role Management**: Sistem akses aman berbasis role untuk keamanan data.
- 👥 **Manajemen User**: Pengelolaan akun Manajer dan Direktur dengan hak akses yang berbeda.
- ⚙️ **Manajemen Kriteria (Semi-Dynamic)**: Kustomisasi bobot dan urutan kriteria tanpa merusak arsitektur perhitungan dasar.
- 📍 **Manajemen Lokasi Alternatif**: Pendataan kandidat lokasi cabang baru yang terstruktur.
- 📝 **Observasi Lokasi**: Formulir digital terintegrasi untuk mencatat hasil tinjauan lapangan.
- 📊 **Penilaian Matriks Keputusan**: Pembentukan matriks awal TOPSIS secara otomatis dari hasil observasi.
- 🧮 **Perhitungan TOPSIS Transparan**: Menampilkan langkah demi langkah matematis (Normalisasi hingga Nilai Preferensi).
- 🏆 **Hasil Rekomendasi Lokasi**: Peringkat lokasi terbaik yang siap dipresentasikan.
- 🗺️ **Dataset Kepadatan Penduduk**: Integrasi data kepadatan penduduk level wilayah (Provinsi, Kabupaten, Kecamatan) berbasis database lokal.

---

## 📐 TOPSIS Methodology

Metode *Technique for Order of Preference by Similarity to Ideal Solution* (TOPSIS) mengedepankan logika matematis untuk pengambilan keputusan multi-kriteria:

1. **Matriks Keputusan ($X_{ij}$)**: Matriks awal berdasarkan data observasi.
2. **Normalisasi ($R_{ij}$)**: Menyamakan skala semua kriteria.
3. **Normalisasi Terbobot ($Y_{ij}$)**: Mengalikan matriks normalisasi dengan bobot masing-masing kriteria.
4. **Solusi Ideal ($A^+ / A^-$)**: Menentukan nilai terbaik (Positif) dan terburuk (Negatif) untuk setiap kriteria berdasarkan jenisnya (*Benefit/Cost*).
5. **Jarak Solusi ($D^+ / D^-$)**: Menghitung jarak geometris setiap alternatif terhadap Solusi Ideal Positif dan Negatif.
6. **Nilai Preferensi ($V_i$)**: Kalkulasi nilai akhir. Nilai terbesar adalah lokasi yang paling direkomendasikan.

---

## 🏗 System Architecture

Sistem ini dibangun dengan **Monolithic Architecture** yang modern. Antarmuka sisi klien didorong oleh Blade Template, Alpine.js, dan Tailwind CSS, sedangkan logika komputasi ditangani seutuhnya oleh PHP & Laravel.

### Arsitektur Kriteria Semi-Dinamis
Konsep Semi-Dinamis dirancang secara spesifik untuk kasus tesis ini:
- **Konfigurasi Kriteria Bersifat Dinamis**: Administrator dapat merubah nama tampilan, atribut (*Benefit/Cost*), bobot kriteria, dan urutan (*display order*).
- **Entitas Observasi Bersifat Tetap (Fixed)**: Field operasional observasi tidak berubah secara serampangan untuk menjamin validitas metodologi pengukuran matematis.

### Hubungan Entitas Core
`Lokasi` ➔ `Observasi` ➔ `Penilaian` ➔ `Detail Penilaian` ➔ `Hasil Perhitungan`

---

## 🗄 Database Design

Desain skema basis data inti (RDBMS):

| Tabel | Deskripsi |
| --- | --- |
| `users` | Autentikasi dan identitas pengguna aplikasi. |
| `kriteria` | Parameter pendukung keputusan (bobot, atribut, kunci_observasi, urutan). |
| `lokasi` | Entitas alternatif wilayah atau alamat kandidat cabang. |
| `observasi_lokasi` | Pencatatan data mentah survei fisik ke sebuah `lokasi`. |
| `penilaian` | Tabel induk untuk menginisiasi proses penilaian. |
| `detail_penilaian` | Tabel matriks (pivot) yang menyimpan skor untuk tiap relasi kriteria dan penilaian. |
| `hasil_perhitungan` | Menyimpan log nilai preferensi akhir dan peringkat (*ranking*). |

---

## 🚀 Installation Guide

Ikuti panduan berikut untuk menjalankan sistem secara lokal:

### Requirements
- **PHP** `^8.3`
- **Composer**
- **Node.js** & **NPM**
- **MySQL**

### Step-by-step
```bash
# 1. Clone repository
git clone https://github.com/yourusername/saung-aqiqah-topsis.git
cd saung-aqiqah-topsis

# 2. Install PHP Dependencies
composer install

# 3. Install Node.js Dependencies
npm install

# 4. Environment Setup
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi Database
# Buka file .env dan sesuaikan kredensial MySQL Anda:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=nama_database_anda
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan Migrasi & Seeder Utama (termasuk Role & Kriteria)
php artisan migrate:fresh --seed

# 7. Import Dataset Wilayah & Kepadatan Penduduk BPS
php artisan import:wilayah

# 8. Import Dataset Spatial (Kompetitor & RPH)
php artisan import:dataset

# 9. Build Frontend Assets
npm run build

# 10. Jalankan Development Server
php artisan serve
```

### Konfigurasi Khusus (Opsional)
Untuk pengaturan kalkulasi spasial (seperti radius pencarian kompetitor), Anda bisa menyesuaikan variabel berikut di dalam `.env`:
```env
SPATIAL_COMPETITOR_RADIUS=5
```
Data CSV hanya diimpor sekali melalui `php artisan import:dataset`. Seluruh proses kalkulasi jarak (Haversine) akan langsung membaca basis data untuk optimalisasi performa.

---

## 🧪 Seeder & Dummy Data

Untuk memvalidasi dan membandingkan hasil perhitungan web dengan dokumen Excel tesis, sistem menyediakan Seeder yang dirancang secara presisi menggunakan *dataset* nyata pengujian.

Dataset *Dummy* ini berisi konfigurasi matriks A1 hingga A7:
- `Kp Sarampad`
- `Jl. Perumahan Puncak Manis`
- `Jl Sabandar`
- `Jl. Griya Maleber Indah`
- `Jl. Bumi Emas`
- `Jl Raya Cibeber, Kp Cilaku Kaum`
- `Ciputri`

**Cara Menjalankan Seeder TOPSIS:**
```bash
php artisan import:dummy
```
Perintah ini mengeksekusi `DummyTopsisSeeder` yang secara otomatis membuatkan entitas Lokasi, Observasi, Matriks Keputusan, serta Detail Penilaian secara terstruktur tanpa merusak data inti.

---

## 🔑 Login Credentials

Setelah menjalankan *seeding* bawaan, Anda dapat login dengan akun berikut:

### Manajer (Administrator Operasional)
- **Email:** `manajer@example.com`
- **Password:** `password`

### Direktur (Pimpinan Pengambil Keputusan)
- **Email:** `direktur@example.com`
- **Password:** `password`

---

## 📁 Project Structure

Direktori penting di dalam aplikasi ini:

<details>
<summary><b>Klik untuk melihat struktur</b></summary>

- `app/` : Menyimpan logika inti (*Controllers, Models, Services, Requests*). Di sini tempat *Engine* `TopsisService.php` bersemayam.
- `resources/views/` : Kumpulan antarmuka UI dengan arsitektur Blade & Tailwind CSS.
- `database/` : File migrasi struktur tabel, seeder *dummy*, dan *factories*.
- `routes/` : Deklarasi rute `web.php` untuk autentikasi, akses manajer, dan direktur.
- `public/` : Penyimpanan berkas *assets* yang di-_build_ serta aset publik lainnya.
</details>

---

## 🔄 TOPSIS Flow Explanation

Alur kerja aplikasi dirancang sangat intuitif:

1. **Input Lokasi:** Manajer mendata alternatif lokasi wilayah cabang baru.
2. **Observasi:** Manajer turun ke lapangan dan mengisi form kelayakan, kepadatan penduduk, serta jarak RPH ke dalam sistem.
3. **Generate Matriks:** Sistem otomatis menerjemahkan data mentah observasi menjadi nilai angka matriks X.
4. **Hitung TOPSIS:** Dengan satu klik, layanan `TopsisService` mengolah matriks secara matematis secara *real-time*.
5. **Ranking Hasil:** Direktur dan Manajer melihat peringkat V (Nilai Preferensi) tertinggi untuk membuat keputusan akhir.

---

## 🖼️ Screenshots

<div align="center">
  <table width="100%">
    <tr>
      <td width="50%" align="center">
        <img src="https://via.placeholder.com/600x350/1a202c/ffffff?text=Penilaian+Matrix" alt="Matrix Preview">
        <br><i>Tabel Penilaian Awal</i>
      </td>
      <td width="50%" align="center">
        <img src="https://via.placeholder.com/600x350/1a202c/ffffff?text=Kriteria+Management" alt="Kriteria Preview">
        <br><i>Manajemen Kriteria (Semi-Dinamis)</i>
      </td>
    </tr>
    <tr>
      <td width="50%" align="center">
        <img src="https://via.placeholder.com/600x350/1a202c/ffffff?text=Observasi+Form" alt="Observasi Form">
        <br><i>Formulir Observasi Interaktif</i>
      </td>
      <td width="50%" align="center">
        <img src="https://via.placeholder.com/600x350/1a202c/ffffff?text=Laporan+Cetak" alt="Laporan Preview">
        <br><i>Tampilan Laporan & Ranking</i>
      </td>
    </tr>
  </table>
</div>

---

## 🚀 Future Development

Beberapa ide pengembangan di masa depan untuk meningkatkan skalabilitas dan fungsionalitas sistem:
- 🗺️ **Google Maps Integration**: Memasukkan _coordinate selector_ terpadu untuk pencatatan titik lokasi.
- 🌍 **GIS Support**: Visualisasi peta *Heatmap* untuk distribusi kepadatan penduduk dan persebaran kompetitor.
- 📈 **Multi-Branch Analytics**: Dasbor analitik tingkat lanjut untuk perbandingan pendapatan antar cabang.
- 📑 **Export PDF / Excel**: Modul pengunduhan hasil perankingan SPK sebagai dokumen laporan cetak resmi.
- 📊 **Data Visualization**: Grafik interaktif (misal: *Radar Chart*) untuk perbandingan komparatif lokasi A vs B.

---