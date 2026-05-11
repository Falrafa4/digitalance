# 🚀 Digitalance

Digitalance adalah platform marketplace jasa berbasis web yang dirancang untuk membantu siswa SMK Telkom Sidoarjo dalam memonetisasi keahlian digital mereka melalui layanan profesional secara terstruktur.

Platform ini memungkinkan pengguna untuk membuat profil profesional, menawarkan jasa, berinteraksi dengan klien, mengelola transaksi, hingga menyelesaikan pesanan dalam satu sistem terintegrasi.

---

## 💻 Latar Belakang Proyek

Proyek Uji Kenaikan Level adalah tugas akhir yang harus diselesaikan oleh siswa SMK Telkom Sidoarjo kelas XI jurusan SIJA untuk naik ke tingkat berikutnya.

Digitalance dipilih sebagai proyek karena relevansinya dengan kebutuhan industri digital saat ini serta memberikan pengalaman nyata dalam membangun aplikasi marketplace modern menggunakan Laravel.

Selain sebagai media pembelajaran teknis, proyek ini juga menjadi simulasi ekosistem freelance marketplace skala kecil yang berfokus pada:
- manajemen layanan,
- transaksi digital,
- komunikasi client–freelancer,
- dan workflow bisnis berbasis web.

---

## 🎯 Tujuan Proyek

Digitalance dibuat untuk:

- Memberikan wadah bagi siswa untuk menjual keahlian digital
- Melatih siswa memahami sistem marketplace berbasis web
- Memberikan pengalaman nyata dalam manajemen order & transaksi
- Mendorong kewirausahaan digital sejak bangku sekolah
- Menjadi simulasi sistem freelance marketplace skala kecil
- Melatih implementasi multi-role authentication dan workflow bisnis

---

## 🧩 Fitur Utama

- Autentikasi multi-role (`Administrator`, `Client`, `Freelancer`)
- Manajemen layanan (`services`)
- Sistem pemesanan (`orders`)
- Sistem penawaran & negosiasi (`offers`)
- Manajemen transaksi
- Upload hasil pekerjaan
- Sistem review (one-to-one per order)
- Kategori layanan
- Dashboard berbeda untuk setiap role
- Attachment upload pada order
- Dokumentasi API sederhana
- Realtime infrastructure (persiapan negotiation/chat)

---

## 🔄 Core Flow Sistem

1. Freelancer membuat layanan (`Service`)
2. Client membuat pesanan (`Order`)
3. Client dan Freelancer melakukan negosiasi (`Offer`)
4. Freelancer memproses pekerjaan
5. Freelancer mengunggah hasil pekerjaan (`Result`)
6. Transaksi diselesaikan
7. Client memberikan review

---

# 👥 Role dan Hak Akses

## Administrator
- Verifikasi dan moderasi akun Freelancer
- Manajemen data master:
  - users
  - categories
  - services
  - orders
  - offers
  - transactions
  - reviews

## Client
- Menjelajah layanan dan talent
- Membuat order
- Mengirim attachment
- Negosiasi harga & revisi
- Menerima/menolak offer
- Melihat histori transaksi
- Memberikan review setelah order selesai

## Freelancer
- Mengelola profil dan portofolio
- Membuat dan mengelola service
- Memproses order
- Mengatur status dan harga pekerjaan
- Mengirim hasil pekerjaan
- Mengelola negotiation inbox

---

# 🏗️ Tech Stack

## Backend
- PHP `^8.2`
- Laravel Framework `^12`
- Eloquent ORM
- MySQL
- Multi-guard session authentication:
  - `administrator`
  - `client`
  - `freelancer`

## Frontend
- Blade Templates
- Tailwind CSS
- Vanilla JavaScript
- Vite (`vite`, `laravel-vite-plugin`)

## Supporting Packages
- `laravel/sanctum`
- `laravel/reverb`
- `dedoc/scramble`

---

# 🧠 Gaya Arsitektur

Digitalance menggunakan pendekatan:

- Laravel monolith architecture
- Controller-first pattern
- Server-rendered web application sebagai surface utama
- API tersedia dalam scope terbatas

---

# ⚙️ Instalasi Lokal

## Requirements

- PHP >= 8.2
- Composer
- Node.js + npm
- MySQL
- Git
- Code Editor (Rekomendasi: Visual Studio Code)

---

## 📦 Langkah Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/Falrafa4/digitalance.git
```

### 2. Masuk ke Direktori Project

```bash
cd digitalance
```

### 3. Install Dependency PHP

```bash
composer install
```

### 4. Buat File Environment

Linux/macOS:

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Konfigurasi Database

Atur konfigurasi database pada file `.env`

```env
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

### 7. Jalankan Migrasi dan Seeder

```bash
php artisan migrate --seed
```

### 8. Install Dependency Frontend

```bash
npm install
```

### 9. Jalankan Aplikasi

Rekomendasi:

```bash
composer dev
```

Alternatif manual:

```bash
php artisan serve
npm run dev
```

### 10. Akses Aplikasi

```text
http://localhost:8000
```

---

# 🔐 Sistem Authentication

> Catatan Penting

- Authentication utama menggunakan session multi-guard:
  - `administrator`
  - `client`
  - `freelancer`
- `Laravel Sanctum` tersedia sebagai dependency namun belum menjadi authentication utama untuk web app.
- API project masih bersifat terbatas dan web-first.

---

# 🧪 Data Demo dan Login

Setelah menjalankan:

```bash
php artisan migrate --seed
```

Akun berikut dapat digunakan:

| Role | Email | Password |
| --- | --- | --- |
| Administrator | `admin1@email.com` | `admin123` |
| Administrator | `admin2@email.com` | `admin123` |

### Akun Tambahan Seeder
- Client dibuat menggunakan email acak dengan password default:
  ```text
  password
  ```

- Freelancer menggunakan:
  ```text
  skomda_student.email
  ```

  dengan password default:
  ```text
  password
  ```

Jika tidak mengetahui email seeded account, pengguna dapat melakukan registrasi manual melalui halaman publik.

---

# 🌐 Endpoint Utama

## Web Routes
- `/`
- `/services`
- `/login`
- `/admin`
- `/client`
- `/freelancer`

## API & Dokumentasi
- `/docs/api`
- `/docs/api.json`
- `/api/test`

---

# 📂 Struktur Proyek

```text
app/                # Controllers, Models, Form Requests
routes/             # web.php dan api.php
resources/views/    # Blade templates
public/js/          # Vanilla JavaScript
public/css/         # Styling dashboard
database/           # Migrations, factories, seeders
tests/              # PHPUnit tests
docs/               # Dokumentasi internal
```

---

# ⚠️ Keterbatasan Saat Ini

- Surface API masih terbatas
- Realtime negotiation/chat belum sepenuhnya selesai end-to-end
- Payment gateway belum diimplementasikan
- Test coverage masih minimal dan membutuhkan perluasan feature testing

---

# 🤝 Kontribusi

Kontribusi sangat terbuka untuk pengembangan project ini.

## Alur Kontribusi

1. Buat branch baru

```bash
feature/<nama-singkat>
```

atau

```bash
fix/<nama-singkat>
```

2. Pastikan perubahan tetap terstruktur dan konsisten dengan arsitektur project

3. Jalankan testing sebelum membuka Pull Request

```bash
composer test
```

4. Buat Pull Request dengan:
- Ringkasan perubahan
- Screenshot (jika ada perubahan UI)
- Catatan pengujian

---

# 👨‍💻 Contributors

1. Muhammad Naufal Rafa Al As'ad  
   Backend Development  
   GitHub: https://github.com/falrafa4/

2. Syarivatun Nisa'I Nur Aulia  
   Frontend Development  
   GitHub: https://github.com/jeonwonwooo

---

# 📸 Visual Preview

*Coming Soon*

---

# 📃 License

Project ini menggunakan lisensi MIT.

Lihat file [LICENSE](LICENSE) untuk detail lebih lanjut.