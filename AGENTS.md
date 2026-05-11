# AGENTS.md

## Snapshot
- Project ini adalah Laravel 12 monolith, web-first, untuk marketplace jasa multi-role.
- Flow utama yang benar-benar dipakai: `Service -> Order -> Negotiation/Offer -> Transaction/Result -> Review`.
- Identitas domain penting: `Freelancer` terhubung ke `SkomdaStudent`; `Client` dan `Administrator` berdiri sendiri.

## Struktur Aktual
- `app/Http/Controllers`: 18 controller; satu controller sering menangani beberapa role lewat method seperti `clientIndex`, `freelancerIndex`, `profile`, `store`, `updateStatus`.
- `app/Http/Requests`: 27 `FormRequest`; validasi tersebar antara `FormRequest` dan inline `$request->validate()`.
- `app/Models`: 14 model Eloquent; tidak ada `Services/`, `Repositories/`, `Actions/`, atau `Policies/`.
- `resources/views`: Blade role-based di `public/`, `dashboard/admin/`, `dashboard/client/`, `dashboard/freelancer/`, plus shared `layouts/` dan `components/`.
- `public/js/dashboard/*` dan `public/css/dashboard/*`: page-specific vanilla JS/CSS. `resources/js/*` hanya entry Vite, Echo bootstrap, dan API experiment ringan.

## Pola Koding
- Project ini controller-first: query, authorization, transform data, redirect, dan flash message umumnya langsung di controller.
- Query Eloquent dominan memakai `with()`, `whereHas()`, `latest()`, `paginate()`, dan kadang map/transform collection untuk shape data view.
- Validasi CRUD yang sudah rapi biasanya lewat `FormRequest`; action kecil/status update/file upload masih banyak pakai inline validation.
- Response default adalah `redirect()->route(...)->with(...)`. JSON hanya ad hoc: `expectsJson()`, middleware, `/api/test`, dan beberapa AJAX endpoint admin. Tidak ada API response envelope yang konsisten.
- Auth utama memakai session guards `administrator`, `client`, `freelancer`. Login dilakukan manual per guard; freelancer login via email milik relasi `skomda_student`.
- State frontend tidak memakai framework. Data server di-inject ke `window.__PAGE__ = @json(...)`, lalu diolah oleh global object di file `public/js/dashboard/...`.
- Reusable UI yang benar-benar dipakai: Blade components `x-sidebar`, `x-header`, `x-navbar`, `x-footer`, `x-notification-drawer`, `x-flash`, plus global `window.showToast`.
- Database transaction belum dipakai. Tidak ada `DB::transaction` di `app/` atau `database/`.

## Naming Dan Convention
- Route name umumnya berbasis role: `admin.*`, `client.*`, `freelancer.*`.
- Nama method controller cenderung role-prefixed: `clientShow`, `clientStore`, `freelancerIndex`, `showReviewByOrderId`.
- Nama relasi jangan “dibersihkan” sembarangan:
  - `skomda_student` tetap snake_case.
  - `Service` punya `category()` dan alias legacy `service_category()`.
  - Domain spelling yang dipakai adalah `Portofolio`, bukan `Portfolio`.
- Status enum harus mengikuti casing yang ada di migration/controller: `Pending`, `Negotiated`, `Paid`, `In Progress`, `Revision`, `Completed`, `Cancelled`, `Approved`, `Rejected`, `Sent`.
- Import umumnya explicit satu per line; tidak ada helper layer atau base service custom.

## Dependency Penting
- Backend: `php ^8.2`, `laravel/framework v12.53.0`, `laravel/sanctum v4.3.1`, `laravel/reverb v1.10.0`, `dedoc/scramble v0.13.14`.
- Dev/test: `phpunit/phpunit 11.5.55`, `laravel/pint v1.27.1`.
- Frontend/build: `vite 5.4.21`, `laravel-vite-plugin 1.3.0`, `tailwindcss 3.4.19`, `axios 1.13.6`, `laravel-echo 2.3.4`, `pusher-js 8.5.0`.
- Tailwind dipakai campuran: ada Vite pipeline, tapi layout Blade juga inject `tailwindcdn` dan config inline.

## Workflow Development
- Entry app nyata ada di `routes/web.php`; `routes/api.php` masih mostly commented/stub.
- `composer dev` menjalankan `php artisan serve`, `queue:listen`, `pail`, dan `npm run dev` secara paralel.
- Docs OpenAPI disediakan oleh Scramble di `/docs/api`, tapi surface API project saat ini belum mature.
- Seeder dan factory cukup kaya; `DatabaseSeeder` membangun flow domain lengkap untuk demo data.

## Testing
- Jalankan `composer test` atau `php artisan test`.
- Test config memakai `sqlite` in-memory lewat `phpunit.xml`.
- Suite saat ini minimal: hanya `tests/Feature/ExampleTest.php` dan `tests/Unit/ExampleTest.php`.
- Kalau menambah behavior, prefer tambah feature test; jangan mengandalkan seed data sebagai “test”.

## Boundaries Dan Hal Non-Obvious
- Jangan introduksi `Service Layer` atau `Repository Pattern` hanya demi kerapian. Codebase sekarang belum memakai itu; default yang paling aman adalah tetap controller-first + FormRequest + Eloquent relation.
- Jangan pindah ke React/Vue/state library. Frontend existing adalah Blade + vanilla JS + injected page data.
- Jangan rename legacy identifier tanpa sweep penuh. Yang masih aktif dipakai: `service_category`, `skomda_student`, `Portofolio`.
- Jangan mengasumsikan route name/action route selalu rapi. Cek `php artisan route:list` dulu untuk route non-CRUD; ada route name malformed seperti `admin.admin.services.updateStatus`, `admin.admin.freelancers.verify`, dan alias `/freelancer/dashboard` bernama `freelancer.`.
- Jangan mengasumsikan view reference sinkron dengan filesystem. Beberapa controller mengarah ke Blade yang belum ada, jadi cek file nyata sebelum meniru pattern return view.
- Jangan mengasumsikan schema sinkron dengan semua controller/request. Ada mismatch nyata yang harus diverifikasi sebelum extend:
  - `StoreOfferRequest` memakai field typo `desciption`, sementara tabel memakai `description`.
  - `Order` model/controller masih menyentuh `freelancer_id`, tetapi migration `orders` tidak punya kolom itu.
  - `Result` code memakai `message`, sementara migration `results` mendefinisikan `version`.
  - `NegotiationController` sempat membandingkan status lowercase (`pending`, `negotiated`) padahal enum order uppercase phrase.
- Jangan mengasumsikan realtime chat sudah fully live. Echo/Reverb/channel config memang ada, tapi `NegotiationSent` belum implement `ShouldBroadcast`, jadi wiring broadcast belum sepenuhnya selesai.
- Sanctum ada di model/config, tetapi auth project yang aktif saat ini tetap session multi-guard. API token flow belum jadi pola utama.
