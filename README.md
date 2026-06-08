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
- Inbox chat negosiasi realtime antara `Client` dan `Freelancer`
- Manajemen transaksi
- Upload hasil pekerjaan
- Sistem review (one-to-one per order)
- Kategori layanan
- Fitur lowongan kerja (`loker`) dan lamaran freelancer
- AI recommendation untuk membantu client menemukan freelancer yang relevan
- Career mapping freelancer dengan analisis spesialisasi dan pengajuan verifikasi jalur karir
- Dashboard berbeda untuk setiap role
- Attachment upload pada order
- Upload gambar profil dan portofolio dengan konversi WebP
- Avatar user di dashboard dan halaman publik
- Navbar publik dengan efek floating saat scroll
- REST API `/api/v1` dengan dokumentasi OpenAPI dari Scramble
- Status visual pesan pada inbox chat (`Perlu Respons` / `Sudah Dibaca`)

---

## 🔄 Core Flow Sistem

1. Freelancer membuat layanan (`Service`)
2. Client membuat pesanan (`Order`)
3. Client dan Freelancer melakukan negosiasi (`Offer`)
4. Freelancer memproses pekerjaan
5. Freelancer mengunggah hasil pekerjaan (`Result`)
6. Transaksi diselesaikan
7. Client memberikan review

Flow lain yang juga aktif:
- `Loker -> Application -> Approval -> Order`
- `Freelancer onboarding -> Career Mapping -> Verification`

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
- Tailwind CSS via CDN pada layout saat ini
- CSS statis di `public/css`
- Vanilla JavaScript
- Vite scaffold (`vite`, `laravel-vite-plugin`) untuk bundle `resources/css/app.css` dan `resources/js/app.js`

## Supporting Packages
- `laravel/sanctum`
- `laravel/reverb`
- `dedoc/scramble`
- `phpoffice/phpspreadsheet`

---

# 🧠 Gaya Arsitektur

Digitalance menggunakan pendekatan:

- Laravel monolith architecture
- Controller-first pattern
- Server-rendered web application sebagai surface utama
- Web-first application dengan REST API `/api/v1` sebagai surface pendamping

---

# ⚙️ Instalasi Lokal

## Requirements

- PHP >= 8.2
- PHP GD extension
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

Seeder membaca data siswa dari file:

```text
database/seeders/data/siswa.xlsx
```

File tersebut saat ini tersedia di repository. Pada environment non-production, `DatabaseSeeder` akan menjalankan `DevelopmentSeeder`; pada environment production akan menjalankan `ProductionSeeder`.

```bash
php artisan migrate --seed
```

### 8. Buat Storage Symlink

Upload gambar profil, portofolio, attachment, dan hasil pekerjaan disajikan melalui disk `public`.

```bash
php artisan storage:link
```

### 9. Install Dependency Frontend

```bash
npm install
```

### 10. Jalankan Asset Frontend

Development:

```bash
npm run dev
```

Production:

```bash
npm run build
```

> Catatan: layout publik dan dashboard saat ini masih memuat Tailwind CDN serta beberapa CSS/JS statis dari `public/`. Vite tetap tersedia untuk bundle `resources/*` dan workflow development.

### 11. Jalankan Aplikasi

Rekomendasi:

```bash
composer dev
```

Untuk fitur realtime negotiation/chat pada local development, jalankan Laravel Reverb di terminal terpisah:

```bash
php artisan reverb:start
```

Alternatif manual:

```bash
php artisan serve
php artisan reverb:start
npm run dev
```

### 12. Akses Aplikasi

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
- Web app utama tetap memakai session multi-guard.
- `Laravel Sanctum` dipakai untuk autentikasi API `/api/v1`.
- Surface utama project tetap web-first, tetapi REST API sudah tersedia untuk resource penting seperti auth, services, freelancers, orders, offers, negotiations, results, reviews, transactions, dan loker.

---

# 🧪 Data Demo dan Login

Setelah menjalankan:

```bash
php artisan migrate --seed
```

Data `SkomdaStudent` akan diimpor dari file Excel lokal `database/seeders/data/siswa.xlsx`.

Akun berikut dapat digunakan:

| Role | Email | Password |
| --- | --- | --- |
| Administrator | `admin1@email.com` | `admin123` |
| Administrator | `admin2@email.com` | `admin123` |
| Client | `client1@email.com` | `client123` |

### Akun Tambahan Seeder
- Pada environment local/development, factory client tambahan memakai password default:
  ```text
  password
  ```

- Freelancer seeded/factory menggunakan email milik relasi:
  ```text
  skomda_student.email
  ```

  dengan password default:
  ```text
  password
  ```

Pada environment production, `ProductionSeeder` juga membuat data demo yang lebih terkurasi untuk service, loker, order story, dan AI pool freelancer.

Jika tidak mengetahui email seeded account, pengguna tetap dapat melakukan registrasi manual melalui halaman publik.

---

# 🌐 Endpoint Utama

## Web Routes
- `/`
- `/services`
- `/login`
- `/admin`
- `/client`
- `/freelancer`
- `/client/ai-recommendations`
- `/freelancer/career-mapping`

## API & Dokumentasi
- `/docs/api`
- `/docs/api.json`
- `/api/test`
- `/api/v1/auth/*`
- `/api/v1/services`
- `/api/v1/orders`
- `/api/v1/lokers`

---

# 📂 Struktur Proyek

```text
app/                # Controllers, Models, Form Requests
app/Support/        # Helper pendukung seperti optimasi upload gambar
routes/             # web.php dan api.php
resources/views/    # Blade templates
resources/css/      # Input Tailwind/Vite
resources/js/       # Entry Vite, bootstrap Axios/Echo
public/js/          # Vanilla JavaScript
public/css/         # Styling dashboard
database/           # Migrations, factories, seeders
tests/              # PHPUnit tests
docs/               # Dokumentasi internal
```

---

# ⚠️ Keterbatasan Saat Ini

- UI masih hybrid: Blade server-rendered sebagai surface utama, dengan Tailwind CDN, asset statis `public/*`, dan Vite bundle `resources/*` berjalan berdampingan
- Web auth dan API auth memakai mekanisme berbeda: session multi-guard untuk web, Sanctum untuk API
- Realtime negotiation/chat membutuhkan proses `Laravel Reverb` aktif saat development lokal
- Payment gateway belum diimplementasikan
- Test coverage sudah mulai berkembang, tetapi masih perlu diperluas untuk flow bisnis utama yang lebih kompleks

---

# 🖼️ Optimasi Gambar

- Upload gambar profil dan portofolio dikonversi dan disimpan sebagai WebP di disk `public`.
- Konversi gambar memakai helper `App\Support\ImageStorage` dan PHP GD.
- Path yang disimpan di database tetap relatif terhadap `storage/app/public`, contohnya `profiles/xxxxx.webp`.
- Placeholder default foto profil menggunakan `profiles/placeholder.webp`.
- Beberapa gambar list/card memakai lazy loading agar halaman lebih ringan saat discroll.

---

# 🧪 Testing

Jalankan seluruh test:

```bash
php artisan test
```

Atau melalui script Composer:

```bash
composer test
```

Coverage yang sudah ada mencakup:
- smoke test halaman publik dan layout frontend
- login request validation
- flow Skomda Student menjadi Freelancer
- notification drawer/composer
- canonical REST API routes
- loker management, apply validation, dan checkout flow terkait
- helper konversi gambar ke WebP

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
