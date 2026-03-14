import { test } from "./services/auth.service";

// Nahh di dalam file ini adalah tempat implementasi API yang sebenarnya...

async function handleTest() {
    console.log('getting data...')
    try {
        const res = await test(); // <-- ini fungsi "test" dari file auth.service.js yang telah dibuat sebelumnya, bisa kamu pahami dulu flow/alurnya

        if (res.data.status) {
            alert(res.data.message) // <-- kalau status true, maka akan ada alert message

            // nahh, tantangannya ada di blok kosong ini, karena kita tidak pakai framework front-end kayak React (jangan dipusingin dulu wkwk), jadinya agak sulit buat mengelola datanya, karena pure harus menggunakan JavaScript murni (ingat2 lagi konsep DOM manipulation, pasti dan akan selalu berurusan dengan DOM JavaScript, karena untuk menampilkan data)

            // JADI, nanti blok ini isinya adalah kode, logika, atau flow dari API nya --> SETELAH API itu berhasil, kita ngapain? nah itu semua ditulis di dalam blok kode ini.
        }
    } catch (error) {
        // Blok kode ini khusus untuk ERROR Handling, artinya? -> integrasi API ada error? tampilkan di sini! atur deh, mau alert, atau mau pesan muncul, semua yang berurusan dengan ERROR ada di sini.
        console.log(error.response.data.message)
    }
}

handleTest()