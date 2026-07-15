<div align="center">
  <br />
  <h2>🎯 Sistem Pendukung Keputusan (SPK) Pemilihan Lokasi Baru</h2>
  <p>Aplikasi Cerdas Berbasis Web Menggunakan Metode <strong>TOPSIS</strong> <i>(Technique for Order of Preference by Similarity to Ideal Solution)</i> Terintegrasi Data Geospasial & Statistik Jawa Barat.</p>
  
  <p>
    <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
    <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
    <img src="https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine JS" />
    <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  </p>
</div>

---

## 📖 Deskripsi Proyek
**SPK Pemilihan Lokasi Baru** adalah sebuah sistem yang dirancang secara khusus untuk membantu level manajemen (Manajer & Direktur) dalam mengevaluasi dan memilih lokasi operasional/cabang baru secara objektif.

Sistem ini mengimplementasikan algoritma **TOPSIS**, sebuah metode Multi-Criteria Decision Making (MCDM) yang sangat efektif dalam mengurutkan alternatif berdasarkan jarak terpendek dari Solusi Ideal Positif (SIP) dan jarak terjauh dari Solusi Ideal Negatif (SIN).

### ✨ Fitur Unggulan
- 🔐 **Role-Based Access Control (RBAC):** Pemisahan hak akses antara **Manajer** (Operasional & Evaluasi) dan **Direktur** (Eksekutif & Pengambil Keputusan).
- 📍 **Integrasi Peta Interaktif:** Terintegrasi langsung dengan **Leaflet.js** dan **OpenStreetMap Nominatim API** untuk *reverse-geocoding* dan penentuan titik koordinat secara langsung.
- 📡 **Automasi Data Eksternal:** Mengambil data statistik regional secara *real-time* via **Emsifa API** & **BPS Jabar API** (UMK, Indeks PDRB, dan Populasi Penduduk).
- 📊 **Perhitungan TOPSIS Otomatis:** Sistem mengeksekusi matriks keputusan, normalisasi, hingga nilai preferensi secara instan dan akurat.
- 📑 **Laporan Eksekutif:** Mendukung export dokumen rekomendasi ke dalam format **PDF** dan **Excel (Multi-Sheet)** untuk kebutuhan rapat Direksi.
- 🎨 **Modern & Premium UI/UX:** Mengusung desain *Glassmorphism*, transisi mulus, dan *responsive layout* menggunakan Alpine.js & TailwindCSS.

---

## 🛠️ Teknologi & *Stack*
- **Backend:** PHP 8.2+, Laravel 11.x
- **Frontend:** HTML5, Alpine.js, Tailwind CSS, Leaflet.js
- **Database:** MySQL / MariaDB
- **Libraries Tambahan:**
  - `spatie/laravel-permission` (Manajemen Hak Akses)
  - `maatwebsite/excel` (Export Data ke Excel)
  - `barryvdh/laravel-dompdf` (Cetak Laporan PDF)
  - `guzzlehttp/guzzle` (Client API Terintegrasi)

---

## 🚀 Cara Instalasi (Local Development)

Ikuti panduan berikut untuk menjalankan sistem ini di komputer Anda (Localhost).

### 1. Persyaratan Sistem
Pastikan Anda sudah menginstal alat-alat berikut:
- **PHP** (Minimal versi 8.2)
- **Composer** (Package Manager PHP)
- **Node.js & NPM** (Untuk kompilasi Frontend)
- **MySQL** (Atau web server lokal seperti XAMPP/Laragon)

### 2. Langkah Instalasi

**a. Clone Repository**
```bash
git clone https://github.com/USERNAME/spk-lokasi-topsis.git
cd spk-lokasi-topsis
```

**b. Instalasi Dependensi PHP (Composer)**
```bash
composer install
```

**c. Instalasi Dependensi Frontend (NPM)**
```bash
npm install
```

**d. Konfigurasi Environment**
Salin file `.env.example` menjadi `.env`.
```bash
cp .env.example .env
```
Buka file `.env` dan atur konfigurasi *database* Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spk_lokasi
DB_USERNAME=root
DB_PASSWORD=
```

**e. Generate Application Key**
```bash
php artisan key:generate
```

**f. Migrasi Database & Seeding (Penting!)**
Jalankan perintah ini untuk membangun tabel dan mengisi *dummy data* awal beserta *Role Permissions*:
```bash
php artisan migrate:fresh --seed
```

**g. Kompilasi Aset Frontend**
```bash
npm run dev
```

**h. Jalankan Server Laravel**
Buka terminal baru, dan jalankan server:
```bash
php artisan serve
```

Aplikasi sekarang dapat diakses melalui browser pada `http://127.0.0.1:8000`.

---

## 🔐 Akun Default (Seeder)
Gunakan kredensial berikut untuk melakukan *login* awal ke dalam sistem:

| Peran (Role) | Email | Password |
|--------------|-------|----------|
| **Manajer** | `manajer@example.com` | `password` |
| **Direktur** | `direktur@example.com` | `password` |

*(Catatan: Anda dapat menambah atau mengubah *user* melalui halaman Manajemen User di *dashboard* Manajer).*

---

## 📂 Struktur Modul Sistem

1. **Modul Manajer (Administrator Operasional)**
   - **Kelola Kriteria & Bobot:** Mengatur kriteria penilaian (Benefit/Cost) serta bobotnya secara dinamis.
   - **Manajemen Observasi Lokasi:** Menambah data lokasi survei baru dengan input koordinat *real-time*.
   - **Evaluasi Penilaian:** Memasukkan nilai observasi lapangan ke dalam matriks awal.
   - **Eksekusi TOPSIS:** Memicu *engine* untuk menghitung nilai preferensi secara matematis.
   - **Riwayat Perhitungan:** Mengakses kembali kalkulasi dan pemeringkatan di *batch* atau periode sebelumnya.

2. **Modul Direktur (Pengambil Keputusan)**
   - **Dashboard Monitoring:** Ringkasan statistik performa seluruh cabang.
   - **Review Observasi:** Meninjau kelayakan data survei mentah dari lapangan.
   - **Hasil Keputusan (Rekomendasi):** Melihat peringkat akhir (*Final Ranking*) dari hasil TOPSIS.
   - **Export Laporan:** Mengunduh hasil kalkulasi komprehensif ke format PDF atau Excel Multi-Sheet.

---

## 💡 Metode Perhitungan TOPSIS
Aplikasi ini secara otomatis melewati fase matematis TOPSIS sebagai berikut:
1. Pembangunan **Matriks Keputusan (X)** dari data hasil penilaian lapangan.
2. Pembangunan **Matriks Keputusan Ternormalisasi (R)**.
3. Pembangunan **Matriks Keputusan Ternormalisasi Berbobot (Y)** berdasarkan *weight* tiap kriteria.
4. Menentukan nilai **Solusi Ideal Positif (A+)** dan **Solusi Ideal Negatif (A-)**.
5. Menghitung jarak alternatif dari A+ (D+) dan jarak dari A- (D-).
6. Mendapatkan **Nilai Preferensi (V)** tertinggi sebagai rekomendasi terbaik.

---

## 🛡️ Keamanan (Security)
Sistem ini menggunakan perlindungan standar *enterprise* Laravel:
- Middleware `Spatie\Permission` memblokir akses ke rute ilegal.
- Proteksi CSRF (`@csrf`) pada seluruh formulir manipulasi data.
- Enkripsi password menggunakan algoritma *Bcrypt*.
- Validasi Input Ketat (*Form Request Validation*) untuk menghindari *Mass Assignment* & XSS.

---

<div align="center">
  <p>Dibuat dengan ❤️ untuk keperluan Skripsi / Tugas Akhir.</p>
</div>