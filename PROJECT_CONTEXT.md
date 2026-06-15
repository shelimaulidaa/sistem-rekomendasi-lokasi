PROJECT CONTEXT

Saya sedang membangun sistem pendukung keputusan (SPK) penentuan lokasi cabang baru Saung Aqiqah menggunakan metode TOPSIS.

Stack:
- Laravel 13
- PHP 8.4
- MySQL
- Tailwind CSS
- Alpine.js
- Monolithic Architecture
- Laravel Breeze Authentication

Role:
1. Manajer
2. Direktur

Hak Akses Manajer:
- Dashboard
- Kelola User
- Kelola Kriteria
- Kelola Lokasi
- Observasi Lokasi
- Penilaian
- Perhitungan TOPSIS
- Hasil Keputusan

Hak Akses Direktur:
- Dashboard
- Hasil Observasi
- Hasil Rekomendasi

TOPSIS Criteria (Fixed 6 Criteria):
1. Kepadatan Penduduk (Benefit)
2. Biaya Sewa (Cost)
3. Jumlah Kompetitor (Cost)
4. Jarak dengan RPH (Cost)
5. Aksesibilitas (Benefit)
6. Kelayakan Bangunan (Benefit)

Kriteria bersifat semi-dynamic:
- Tidak boleh tambah kriteria baru
- Tidak boleh hapus kriteria
- User hanya boleh mengubah:
  - nama kriteria
  - bobot
  - atribut benefit/cost
  - urutan

Kriteria memiliki field:
- kode_kriteria
- nama_kriteria
- bobot
- atribut
- urutan
- mapping_key

TOPSIS menggunakan:
- Bobot dinormalisasi otomatis
- Matriks keputusan
- Matriks normalisasi
- Matriks terbobot
- Solusi ideal positif
- Solusi ideal negatif
- D+
- D-
- Nilai preferensi
- Ranking

Lokasi:
- nama lokasi
- alamat lengkap
- kecamatan
- kabupaten/kota
- provinsi

Data wilayah:
- seluruhnya menggunakan database
- tidak menggunakan EMSIFA API
- tidak menggunakan API wilayah

Data kepadatan penduduk:
- menggunakan dataset CSV lokal
- file: kepadatan_penduduk.csv
- tidak menggunakan API BPS

Observasi Lokasi:
- dilakukan oleh Manajer
- satu lokasi memiliki satu observasi aktif
- observasi menyimpan seluruh data lapangan

Field observasi:
- kepadatan penduduk
- biaya sewa
- jumlah kompetitor
- jarak dengan RPH
- aksesibilitas
- kelayakan bangunan
- tipe bangunan
- luas tanah
- luas bangunan
- jumlah ruangan
- jumlah WC
- listrik
- sumber air
- area parkir
- catatan observasi
- dokumentasi foto

Dokumentasi:
- multiple upload
- dikompres otomatis

Perubahan terbaru:
- koordinat lokasi dipindahkan dari tabel Lokasi ke Observasi Lokasi
- koordinat diambil saat observasi lapangan
- menggunakan browser Geolocation API
- manager dapat menekan tombol:
  "Ambil Lokasi Saat Ini"
- latitude dan longitude otomatis terisi dari GPS perangkat
- koordinat merepresentasikan lokasi survei aktual

Direktur:
- hanya dapat melihat hasil observasi
- tidak dapat CRUD observasi
- dapat melihat detail observasi termasuk dokumentasi dan lokasi observasi

UI:
- Sidebar Layout
- Responsive
- Mobile First
- Tablet Friendly
- Desktop Friendly
- Font Arial
- Warna:
  - Hijau
  - Putih
  - Abu-abu
- Modern Minimalist

Saat memberikan implementation plan:
- jangan mengubah arsitektur yang sudah ada
- gunakan Laravel best practices
- gunakan service layer jika diperlukan
- gunakan eager loading
- gunakan responsive design
- gunakan clean code
- gunakan Tailwind CSS

Tolong gunakan context ini untuk seluruh percakapan berikutnya.