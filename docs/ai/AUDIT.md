CLIENT SIDE
- [ ] **Visibility Filter (Urgent)**: `ServiceController@clientIndex` dan `FreelancerController@clientFindTalent` masih menampilkan semua data. WAJIB difilter `status = 'Approved'` agar user tidak melihat jasa/talent yang belum diverifikasi.
- [ ] **Service/Freelancer Detail (Rich Content)**:
    - Tambahkan UI Share (Copy Link) dan Like (Interaction).
    - Tampilkan rating bintang dan jumlah review. Jika kosong, tampilkan "Belum ada review" dengan ajakan "Berikan ulasan pertama".
    - Tambahkan galeri Portofolio di halaman detail jasa/freelancer (saat ini hanya list teks).
    - Tampilkan daftar "Order Selesai" untuk meningkatkan trust.
- [ ] **Find Talent Profil**: Langsung tampilkan profil talent dengan layout yang tertata rapi (Foto, Bio, Skills, Layanan).
- [ ] **Filtering & Search**: Tambahkan input pencarian dan filter kategori di katalog jasa dan find talent.
- [ ] **User History**: Implementasikan halaman atau section "History Aktivitas" yang mencatat log (Membuat order, Negosiasi, Pembayaran).
- [ ] **WhatsApp Style Messages**: Ubah UI daftar pesan dari card menjadi list ala WhatsApp (Avatar bulat, snippet pesan terakhir, timestamp di kanan).
- [ ] **Payment FAQ**: Tambahkan section "Cara Pembayaran" atau FAQ kecil di halaman Checkout untuk memandu klien.
- [ ] **Step-by-Step Order**: Perbaiki UI Stepper di `orders.show` agar lebih informatif dan elegan.
- [ ] **Review Visibility**: Pastikan form rating/review hanya muncul di halaman order jika status sudah `Completed`.
- [ ] **Brief Presentation**: Perbaiki tampilan brief order agar lebih terbaca (gunakan typography yang baik dan card-based layout).
- [ ] **Security (File Upload)**: `OrderController@uploadAttachment` saat ini masih berupa placeholder. Perlu implementasi penyimpanan file yang benar dan relasi ke tabel (atau simpan di kolom brief secara permanen).

FREELANCER SIDE
- [ ] **Negotiation Alerts**: Tambahkan badge "Nego Baru" di dashboard dan halaman order jika ada tawaran baru dari klien yang belum direspon.
- [ ] **Revision Management**:
    - Tambahkan tombol eksplisit "Terima Revisi" dan "Tolak Revisi" di halaman order freelancer saat status `Revision`.
    - Tambahkan modal input alasan jika menolak revisi.
- [ ] **Direct Response Link**: Tambahkan link "Balas Negosiasi" di card pesan agar freelancer bisa langsung menuju section negosiasi di halaman detail order.
- [ ] **Dashboard Stats**: Tambahkan indikator status di card order dashboard (e.g., "Menunggu Pembayaran", "Perlu Revisi").
- [ ] **Result Feedback**: Di detail hasil (results), tampilkan apakah klien sudah melihat/merespon hasil tersebut.
- [ ] **Data Consistency**: Periksa `ResultController@store` agar selalu menggunakan field `version` sesuai database, hindari fallback ke `message` yang tidak konsisten.

ADMIN SIDE
- [x] **Unique Modal IDs (Urgent)**: Ganti semua ID modal generik (seperti `detail-modal-overlay`) menjadi ID unik per modul (e.g., `admin-detail-modal`, `portfolio-detail-modal`) di semua file JS admin. Ini penyebab utama modal tertumpuk atau tidak muncul.
- [x] **Order Store Bug**: `OrderController@store` (Admin) mencoba menyimpan `freelancer_id`, padahal kolom tersebut tidak ada di tabel `orders`. Hapus atau sesuaikan logika ke relasi `service`.
- [x] **Transaction Request Fix**: `StoreTransactionRequest@authorize` saat ini mengembalikan `false`. Ini memblokir Admin saat ingin menambah transaksi secara manual. Ubah ke `true`.
- [x] **Portfolio CRUD**: Tambahkan form Edit Portofolio di `portofolios.js` dan controller terkait (saat ini hanya ada Detail dan Hapus).
- [x] **Admin Edit**: Lengkapi modal Edit Admin di `admins.js` agar bisa mengubah data profil (Nama, Email, Phone, Bio).
- [ ] **Consistent Pagination**: Pastikan semua halaman list (Services, Transactions, dll) menggunakan pagination Blade `$orders->links()` sebagai fallback jika JS gagal memuat data.
- [x] **Advanced Search Integration**: Admin search di dashboard sudah ada, tapi saat diklik menuju halaman Client/Freelancer, halaman tersebut belum menangani filter `?q=`. Tambahkan logika search di `ClientController@index`.
- [x] **Personalized Dashboard**: Ganti teks welcome generik menjadi "Selamat Datang, {Admin Name}!" dan tampilkan tanggal hari ini.

GENERAL EFFICIENCY
- [ ] **Database Queries**: Pastikan semua list menggunakan Eager Loading (`with()`) untuk menghindari masalah N+1 query (terutama pada relasi `freelancer.skomda_student`).
- [ ] **Alpine.js vs Vanilla**: Sesuai `FRONTEND.md`, hindari memperkenalkan library baru. Namun karena Alpine sudah terlanjur digunakan di `orders.show` dan `checkout`, lakukan standarisasi: gunakan Alpine untuk interaksi UI yang kompleks agar kode JS lebih bersih dibanding Vanilla.
- [ ] **Error Handling**: Tambahkan blok `@error` di semua form input yang belum memilikinya (Registration, Profile Update, dll).
