# Audit To-Do List — Digitalance

Date: 2026-05-28

> Daftar kerja hasil komparasi audit, disusun per panel lalu diurutkan berdasarkan urgensi.

## P0 - Blockers

### Admin Panel

- Admin Transactions: perbaiki filter, pagination, dan modal detail yang rusak.

### Freelancer Panel

- Alur fiksasi order: perbaiki error di `OrderController` dan alur lanjutannya.

### Client Panel

- Alur fiksasi order: perbaiki error yang sama dari sisi client.

### Global

- Notification Header: pulihkan update badge/drawer agar sinkron di seluruh panel.

## P1 - High Priority

### Admin Panel

- Freelancer Verifications: rapikan modal/UI verifikasi.
- Platform revenue: lengkapi riwayat/slider pendapatan.
- Services approve/reject: tambahkan modal konfirmasi, notifikasi approve, dan polish alasan reject.
- Offers Data: pastikan relasi lowongan/order tampil dan terhubung benar.

### Freelancer Panel

- Acc service: kirim notifikasi realtime saat service di-approve.
- View card Services: tampilkan starting price konsisten.
- Edit card Services: benahi modal dan aksi edit.
- Delete Services: munculkan tombol/hapus flow yang konsisten.
- Alur acc order: rapikan UX acceptance flow.
- Alur offers lowongan kerja: benahi seed data dan akses "my offers".
- Edit account: perbaiki edit profile/bio freelancer.

### Client Panel

- Detail card Katalog Jasa: rapikan layout detail.
- Alur bikin order: dukung upload PDF/file bila dibutuhkan.
- Alur buat payment: lengkapi flow pembayaran.
- Alur Hasil Kerja: selesaikan view/detail dan revisi flow.
- Edit account: lengkapi edit bio dan default name/email.

### Public Pages

- Responsivitas Login: benahi issue responsive.
- Fungsionalitas Registration: revisi dedupe NIS freelancer.
- Responsivitas Registration: benahi layout mobile.

## P2 - Medium Priority

### Admin Panel

- View table Transactions: stabilkan server-side pagination dan filter.
- View detail Transactions: samakan handler JS modal.

### Freelancer Panel

- View card Services: benahi variasi tampilan yang belum konsisten.
- Edit porto: tambah modal edit.
- Detail card Portofolios: tambahkan detail view.
- Alur terima payment / in progress / revisian / finalisasi / review: selesaikan polishing flow.
- Alur panduan newbie freelancer: lanjutkan penyempurnaan onboarding.

### Client Panel

- Alur in progress / revisian / finalisasi / review: lengkapi alur status order.
- View/Detail History: selesaikan halaman history.
- Alur revisian order Hasil Kerja: implementasikan flow revisi.

### Public Pages

- Alert Login: kurangi noise alert.
- Responsivitas Login dan Registration: cek ulang breakpoint kecil.

## P3 - Low Priority / Polish

### Admin Panel

- Disputes order: audit efisiensi bila blocker selesai.
- Stats mini, Users, Orders, Reviews, Services, Results, Settings, Logout, Sidebar, Search: pertahankan sebagai verified/done.

### Freelancer Panel

- Dashboard stats, Messages, Reviews, Results, Transactions, Portofolios, Lowongan Kerja, Settings, Logout, Sidebar, Search: pertahankan sebagai verified/done.
- Notification Header: verifikasi ulang setelah global fix.

### Client Panel

- Dashboard, Katalog Jasa, Find Talent, My Projects, Lowongan Kerja, Messages, Payment, Settings, Logout, Sidebar, Search: pertahankan sebagai verified/done.
- Notification Header: verifikasi ulang setelah global fix.