// Oke rev, semoga kamu membaca file ini ya

// sebelumnya, aku minta kamu jalankan kode ini di terminal berikut ini:
// 1. npm install
// 2. npm run dev
// 3. php artisan serve (buat terminal baru aja)
// buat apa sih npm-npm an? intinya -> buat menjalankan library axios, untuk apa axios? untuk integrasi API. Loh kok pake axios? yuk dibaca sampai selesai wkwk (semoga kamu bisa memahaminya dengan baik 😇)...

// Oke aku note dulu yaa, kalau kamu bener2 bingung, gpp, tanyain aja ke aku ATAU tanyain ke AI, copas aja penjelasanku terus minta AI buat ubah bahasanya biar mudah dipahamin juga gpp wkwk, biar cepet paham hehe.

// ...
// Jadi.. ada beberapa penyesuaian untuk integrasi API-nya
// kita akan pake library JS "axios", jadi axios semacam tools untuk integrasi API ke URL tertentu
// contohnya bisa dilihat di bawah ini :)

// di dalam file ini (nama.service.js) adalah file khusus untuk menampung fungsi yang nantinya tinggal dipanggil aja di JavaScript halaman blade nya.
// contoh: aku sudah membuat file test.js di dalam "/resources/js/test.js", silahkan ke sana :)

export async function login(data) {
    const res = axios.post('/api/v1/auth/login', data);
    return res;
}

// UNTUK UJI COBA, GUNAKAN API INI!
export async function test() {
    const res = axios.get('/api/test');
    return res;
}