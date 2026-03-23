# 🚀 Digitalance
Digitalance adalah platform marketplace berbasis web yang dirancang untuk membantu siswa SMK Telkom Sidoarjo dalam memonetisasi keahlian mereka melalui layanan digital.

Platform ini memungkinkan siswa untuk membuat profil profesional, menawarkan jasa, berinteraksi dengan klien, serta mengelola transaksi dan pesanan secara terstruktur.

## 💻 Latar Belakang Proyek
Proyek Uji Kenaikan Level adalah tugas akhir yang harus diselesaikan oleh siswa SMK Telkom Sidoarjo kelas XI jurusan SIJA untuk naik ke tingkat berikutnya. Digitalance dipilih sebagai proyek karena relevansinya dengan kebutuhan pasar digital saat ini dan memberikan pengalaman praktis dalam pengembangan aplikasi web.

## 🎯 Tujuan Proyek
Digitalance dibuat untuk:
- Memberikan wadah bagi siswa untuk menjual keahlian digital
- Melatih siswa memahami sistem marketplace berbasis web
- Memberikan pengalaman nyata dalam manajemen order & transaksi
- Mendorong kewirausahaan digital sejak bangku sekolah
- Menjadi simulasi sistem freelance marketplace skala kecil

## 🧩 Fitur Utama
- Autentikasi multi-role (Administrator, Client, Freelancer)
- Manajemen layanan (services)
- Sistem pemesanan (orders)
- Sistem penawaran (offers)
- Manajemen transaksi
- Upload hasil pekerjaan
- Sistem review (one-to-one per order)
- Kategori layanan

## 🏗️ Tech Stack
- Backend/API: Laravel
- Database: MySQL
- ORM: Eloquent
- Frontend: HTML / CSS / JavaScript (Vanilla)
- Authentication: Laravel Sanctum
- Payment Integration: *Coming Soon*

## 🔄 Core Flow Sistem
1. Freelancer membuat layanan
2. Client memesan layanan
3. Freelancer memproses pesanan
4. Freelancer mengunggah hasil pekerjaan
5. Client memberikan review

## ⚙️ Instalasi Lokal
### Requirements
- PHP >= 8.2
- Composer
- MySQL (Rekomendasi: XAMPP atau Laragon)
- Code Editor (Rekomendasi: Visual Studio Code)
- Ekstensi Live Server untuk frontend

### Langkah-langkah
1. Clone repository ini
```bash
https://github.com/Falrafa4/digitalance.git
```
2. Masuk ke direktori proyek
```bash
cd digitalance
```
3. Masuk ke direktori backend
```bash
cd backend
```
4. Instal dependencies dengan menjalankan:
```bash
composer install
```
5. Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database
6. Buat tabel database dengan menjalankan migrasi:
```bash
php artisan migrate --seed
```
7. Untuk menjalankan aplikasi, jalankan perintah berikut:
```bash
php artisan serve
```
8. Akses aplikasi pada URL `http://localhost:8000`.

## 👨‍💻 Contributors
1. Muhammad Naufal Rafa Al As'ad – Backend Development – [GitHub](https://github.com/falrafa4/)
2. Syarivatun Nisa'I Nur Aulia – Frontend Development – [GitHub](https://github.com/jeonwonwooo)

## 📃 License
This project is licensed under the MIT License - see the LICENSE file for details.