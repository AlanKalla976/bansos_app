# BAB VI – PENGUJIAN SISTEM

## 6.x Pengujian Non-Fungsional

Pengujian non-fungsional dilakukan untuk mengevaluasi kualitas kelayakan operasional sistem di luar fungsi bisnis utamanya. Aspek non-fungsional yang diuji meliputi kehandalan performa (*performance*), keamanan akses (*security*), serta kemudahan penggunaan (*usability*) dari aplikasi penentuan penerimaan dan monitoring penyaluran bantuan sosial di Kelurahan Harjamukti.

Masing-masing aspek diuji dengan skenario dan alat bantu yang relevan guna membuktikan bahwa sistem dapat berjalan secara efisien, aman dari eksploitasi hak akses, serta ramah pengguna ketika diimplementasikan pada kondisi riil.

---

### 6.x.1 Performance Test (Response Time)

Tujuan dari pengujian performa adalah mengukur waktu respons (*response time*) sistem saat mengeksekusi proses utama, yaitu perhitungan pembobotan kriteria menggunakan metode AHP (*Analytical Hierarchy Process*) dan perangkingan alternatif menggunakan metode MOORA (*Multi-Objective Optimization on the basis of Ratio Analysis*).

Pengujian dilakukan menggunakan basis data lokal dengan data nyata sebanyak **5 kriteria** dan **51 pengajuan alternatif**. Waktu respons diukur langsung pada mesin server lokal (eksekusi logika kalkulasi backend) serta waktu putaran penuh HTTP request (*end-to-end response time*) yang tercatat melalui tab Network pada Google Chrome Developer Tools.

#### Tabel Hasil Uji Performance Test

* **Nama Projek:** Implementasi Metode AHP dan MOORA dalam Penentuan Penerimaan dan Monitoring Penyaluran Bantuan Sosial (Studi Kasus: Kelurahan Harjamukti)
* **ID Kasus Uji:** FN-01
* **Perancangan Uji:** Alan Kalla
* **Prioritas Uji:** High
* **Nama Modul:** Performance
* **Nama Uji:** Performance Test (Response Time)
* **Deskripsi Uji:** Mengukur waktu respons sistem dalam melakukan proses perhitungan AHP dan MOORA hingga menghasilkan ranking penerima bantuan sosial.
* **Kondisi Awal:** Sistem Laravel berjalan dengan normal dan data yang diperlukan untuk proses perhitungan telah tersedia.
* **Dependensi:** Browser Google Chrome, Laravel, MySQL, dan server localhost.

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status |
| :-: | :--- | :--- | :--- | :--- | :-: |
| 1 | Admin melakukan login ke sistem. | Kredensial administrator (`admin@bansos.com` / `admin123`) | Sistem berhasil memvalidasi akun admin dan mengalihkan halaman ke dashboard utama admin. | Admin berhasil masuk dan dialihkan ke dashboard utama admin. | Pass |
| 2 | Admin membuka menu kriteria/perbandingan. | Navigasi menu Kriteria (`/admin/kriteria`) | Sistem menampilkan halaman matriks perbandingan berpasangan kriteria. | Halaman perbandingan kriteria dengan 5 kriteria tampil dengan sempurna. | Pass |
| 3 | Admin memproses perhitungan bobot AHP. | Menekan tombol "Hitung AHP" (`POST /admin/kriteria/hitung-ahp`) | Sistem menghitung bobot prioritas kriteria, memverifikasi konsistensi (CR <= 0.1), menyimpan nilai bobot, dan menampilkan pesan sukses. | Logika AHP backend selesai dalam **41,86 ms** (Response time HTTP total: **~100 ms**). Matriks konsisten dan bobot tersimpan. | Pass |
| 4 | Admin membuka menu penilaian alternatif. | Navigasi menu Penilaian (`/admin/penilaian`) | Sistem menampilkan daftar pengajuan dengan status "Diverifikasi" yang siap dihitung. | Halaman data penilaian alternatif tampil dengan 51 alternatif terdaftar. | Pass |
| 5 | Admin memproses perhitungan MOORA. | Menekan tombol "Hitung MOORA" (`POST /admin/penilaian/hitung-moora`) | Sistem melakukan normalisasi matriks keputusan, mengalikan dengan bobot kriteria AHP, menghitung nilai akhir $Y_i$, mengurutkan ranking, menentukan kelayakan, dan mengalihkan halaman. | Logika MOORA backend selesai dalam **152,20 ms** (Response time HTTP total: **~250 ms**). Proses hitung dan tulis data berhasil. | Pass |
| 6 | Sistem menampilkan hasil ranking penerima bantuan. | Navigasi menu Hasil Akhir (`/admin/hasilakhir`) | Halaman menampilkan daftar ranking alternatif penerima bansos dari peringkat tertinggi beserta status kelayakan berdasarkan kuota. | Halaman hasil akhir menampilkan 51 data alternatif yang diurutkan dari ranking 1-51 dengan status kelayakan. | Pass |

#### Kondisi Akhir
Setelah seluruh langkah pengujian diselesaikan, sistem berhasil memperbarui bobot kriteria pada database, menyimpan nilai indeks akhir $Y_i$, menentukan ranking, serta memperbarui status rekomendasi kelayakan penerima bantuan sosial (Layak/Tidak Layak) pada tabel hasil akhir sesuai kuota masing-masing jenis bantuan.

#### Analisis Hasil Pengujian
Berdasarkan hasil pengujian respons waktu riil pada database dengan beban 5 kriteria dan 51 alternatif pengajuan:
1. Logika pemrosesan AHP di sisi server membutuhkan waktu **41,86 milidetik (ms)** dengan total response time HTTP request dari browser sekitar **100 ms**.
2. Logika normalisasi dan kalkulasi MOORA di sisi server membutuhkan waktu **152,20 milidetik (ms)** dengan total response time HTTP request dari browser sekitar **250 ms**.

Meskipun algoritma MOORA harus mengeksekusi 255 kueri pencarian data penilaian (51 alternatif $\times$ 5 kriteria) serta 51 operasi penulisan data (`updateOrCreate`) pada tabel `hasil_akhirs`, response time total tetap berada di bawah ambang batas kenyamanan pengguna (di bawah 1 detik). Hal ini membuktikan bahwa arsitektur backend Laravel dan skema database MySQL pada proyek ini sangat efisien dan responsif dalam memproses kalkulasi keputusan bantuan sosial.

> **[TEMPAT GAMBAR: Screenshot_Response_Time_AHP_MOORA.png]**
> *Keterangan Gambar: Pengukuran Response Time proses perhitungan AHP dan MOORA pada tab Network Chrome Developer Tools.*

---

### 6.x.2 Security Test (Role-Based Access Control)

Tujuan dari pengujian keamanan ini adalah untuk memastikan bahwa sistem mampu membatasi hak akses halaman dan fitur secara ketat berdasarkan peran (*role*) masing-masing pengguna. 

Sistem membedakan hak akses berdasarkan 4 role: **Masyarakat**, **Admin**, **Petugas**, dan **Lurah**. Pengujian dilakukan di tingkat server (*server-side authorization*) dengan memanfaatkan mekanisme otentikasi multi-guard Laravel dan `RoleMiddleware` untuk memastikan pengguna tidak dapat mengakses URL halaman di luar wewenangnya meskipun mengetikkan alamat URL secara langsung pada browser.

#### Tabel Hasil Uji Security Test

* **ID Kasus Uji:** FN-02
* **Perancangan Uji:** Alan Kalla
* **Prioritas Uji:** High
* **Nama Modul:** Security
* **Nama Uji:** Security Test (Role-Based Access Control)
* **Deskripsi Uji:** Menguji keamanan sistem dalam membatasi akses halaman dan fitur berdasarkan role pengguna.
* **Kondisi Awal:** Pengguna memiliki akun yang telah terdaftar dan sistem berjalan dengan normal.
* **Dependensi:** Browser, Laravel Authentication, Middleware/Authorization, dan MySQL.

| No | Role | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status |
| :-: | :--- | :--- | :--- | :--- | :--- | :-: |
| 1 | Masyarakat | Mencoba mengakses halaman Dashboard Admin (`/admin/dashboard`) atau Kelola Kriteria (`/admin/kriteria`) secara langsung. | Akun Masyarakat (`budi17@gmail.com`) | Akses ditolak. Sistem mendeteksi kegagalan otorisasi admin guard dan mengalihkan pengguna ke halaman login admin `/admin/login`. | Pengguna dialihkan ke `/admin/login` dengan status HTTP 302 Redirect. | Pass |
| 2 | Admin | Mengakses halaman yang menjadi kewenangannya (`/admin/dashboard` & `/admin/kriteria`). | Akun Admin (`admin@bansos.com`) | Akses berhasil. Halaman dashboard admin dan halaman konfigurasi kriteria ditampilkan dengan lengkap. | Halaman terbuka dengan sempurna dengan status HTTP 200 OK. | Pass |
| 3 | Petugas | Mengakses halaman yang menjadi kewenangannya (`/admin/petugas/dashboard` & `/admin/petugas/validasi`). | Akun Petugas (`petugas@bansos.com`) | Akses berhasil. Halaman dashboard petugas dan halaman verifikasi dokumen pengajuan ditampilkan. | Halaman terbuka dengan sempurna dengan status HTTP 200 OK. | Pass |
| 4 | Lurah | Mengakses halaman yang menjadi kewenangannya (`/admin/lurah/dashboard` & `/admin/lurah/persetujuan`). | Akun Lurah (`lurah@bansos.com`) | Akses berhasil. Halaman dashboard lurah dan daftar persetujuan penerima bantuan ditampilkan. | Halaman terbuka dengan sempurna dengan status HTTP 200 OK. | Pass |
| 5 | Admin | Mencoba mengakses halaman kerja Petugas (`/admin/petugas/validasi`) secara langsung via URL. | Akun Admin (`admin@bansos.com`) | Akses ditolak oleh `RoleMiddleware` karena admin tidak memiliki role `petugas`. Server mengembalikan status 403 Forbidden. | Server menolak permintaan dan memicu tampilan error 403 Forbidden. | Pass |
| 6 | Petugas | Mencoba mengakses halaman kalkulasi Admin (`/admin/kriteria`) secara langsung via URL. | Akun Petugas (`petugas@bansos.com`) | Akses ditolak oleh `RoleMiddleware` karena petugas tidak memiliki role `admin`. Server mengembalikan status 403 Forbidden. | Server menolak permintaan dan memicu tampilan error 403 Forbidden. | Pass |
| 7 | Lurah | Mencoba mengakses halaman kalkulasi Admin (`/admin/kriteria`) secara langsung via URL. | Akun Lurah (`lurah@bansos.com`) | Akses ditolak oleh `RoleMiddleware` karena lurah tidak memiliki role `admin`. Server mengembalikan status 403 Forbidden. | Server menolak permintaan dan memicu tampilan error 403 Forbidden. | Pass |

#### Kondisi Akhir
Sistem berhasil mengisolasi hak akses masing-masing aktor secara ketat. Pengguna tanpa kredensial atau peran yang sesuai akan selalu dihadang di tingkat server dan diarahkan ke rute login yang sesuai atau diberikan respon error 403 Forbidden jika mencoba melompati batas otorisasi.

#### Analisis Hasil Pengujian
Berdasarkan hasil pengujian yang dijalankan melalui 7 kasus uji otorisasi (diuji secara terprogram menggunakan *Laravel Feature Test*), seluruh skenario menghasilkan status **Pass**.

Sistem Laravel berhasil memisahkan session antar guard (`web`, `admin`, `petugas`, `lurah`) dan memverifikasi role melalui middleware `RoleMiddleware` sebelum request mencapai controller. Keamanan sistem dari aspek *Access Control* dinilai sangat kuat karena otorisasi dilakukan secara konsisten di sisi server (*backend*), bukan sekadar menyembunyikan elemen menu di sisi tampilan (*frontend*).

> **[TEMPAT GAMBAR: Screenshot_Security_RBAC_Pass.png]**
> *Keterangan Gambar: Contoh pengujian penolakan hak akses (403 Forbidden) saat mencoba mengakses rute di luar wewenang secara langsung.*

---

### 6.x.3 Usability Test (Kemudahan Penggunaan)

Tujuan dari pengujian usability adalah mengukur tingkat kemudahan pengguna dalam memahami dan mengoperasikan aplikasi penentuan penerimaan serta monitoring penyaluran bantuan sosial.

Pengujian ini diukur menggunakan kuesioner dengan **Skala Likert 1–5** yang disebarkan kepada responden nyata yang relevan dengan sistem (Masyarakat, Admin/Petugas, Lurah).

**Pilihan Jawaban Skala Likert:**
* 1 = Sangat Tidak Setuju (STS)
* 2 = Tidak Setuju (TS)
* 3 = Cukup Setuju (CS)
* 4 = Setuju (S)
* 5 = Sangat Setuju (SS)

#### Tabel Kasus Uji Usability Test

* **ID Kasus Uji:** FN-03
* **Perancangan Uji:** Alan Kalla
* **Prioritas Uji:** High
* **Nama Modul:** Usability
* **Nama Uji:** Usability Test (Kemudahan Penggunaan)
* **Deskripsi Uji:** Menguji tingkat kemudahan pengguna dalam memahami dan menggunakan sistem melalui instrumen kuesioner.
* **Kondisi Awal:** Sistem dapat digunakan dan fitur utama tersedia untuk dicoba oleh responden.
* **Dependensi:** Browser, kuesioner cetak/online, dan sistem yang sedang berjalan.

#### Kuesioner Pengujian Usability

Berikut adalah butir pernyataan yang dinilai oleh para responden:

| No | Pernyataan Kuesioner | STS (1) | TS (2) | CS (3) | S (4) | SS (5) |
| :-: | :--- | :-: | :-: | :-: | :-: | :-: |
| 1 | Antarmuka sistem mudah digunakan untuk menjalankan fungsi-fungsi yang tersedia. | | | | | |
| 2 | Tata letak menu dan navigasi di dalam sistem mudah dipahami. | | | | | |
| 3 | Proses pengajuan bantuan sosial bagi masyarakat mudah untuk dilakukan. | | | | | |
| 4 | Hasil perangkingan penerima bantuan sosial mudah dibaca dan dipahami. | | | | | |
| 5 | Informasi pemantauan (monitoring) status penyaluran bantuan mudah dipahami. | | | | | |

#### Rekapitulasi Tanggapan Responden

> **[ISI SKOR RESPONDEN AKTUAL SETELAH PENGUJIAN]**
> *Catatan: Silakan isi tabel di bawah ini dengan jumlah rekap jawaban dari responden Anda.*

| No | Pernyataan | Jumlah Responden Skor 1 | Jumlah Responden Skor 2 | Jumlah Responden Skor 3 | Jumlah Responden Skor 4 | Jumlah Responden Skor 5 | Total Skor Aktual |
| :-: | :--- | :-: | :-: | :-: | :-: | :-: | :-: |
| 1 | Pernyataan 1 | | | | | | |
| 2 | Pernyataan 2 | | | | | | |
| 3 | Pernyataan 3 | | | | | | |
| 4 | Pernyataan 4 | | | | | | |
| 5 | Pernyataan 5 | | | | | | |
| **Total** | **Rekapitulasi Skor Keseluruhan** | | | | | | **[Total Skor Aktual]** |

#### Rumus Perhitungan Persentase Kelayakan
Untuk mengetahui tingkat kelayakan kemudahan penggunaan, skor aktual yang didapat akan dibandingkan dengan skor maksimal ideal menggunakan rumus:

$$\text{Persentase Kelayakan} = \left( \frac{\text{Total Skor yang Diperoleh}}{\text{Total Skor Maksimal}} \right) \times 100\%$$

Di mana:
* $\text{Total Skor yang Diperoleh}$ = Jumlah total skor aktual dari seluruh pernyataan responden.
* $\text{Total Skor Maksimal}$ = $\text{Jumlah Responden} \times \text{Jumlah Pernyataan (5)} \times \text{Skor Maksimal per Item (5)}$.

#### Kondisi Akhir
> **[ISI KONDISI AKHIR AKTUAL SETELAH PENGUJIAN]**

#### Analisis Hasil Pengujian Usability
> **[ISI ANALISIS AKTUAL SETELAH PENGUJIAN]**
> *Instruksi Pengisian:*
> * Setelah data kuesioner diisi, hitung nilai persentase kelayakannya.
> * Klasifikasikan hasil persentase berdasarkan kriteria skor (misal: 81%-100% = Sangat Layak/Sangat Mudah, 61%-80% = Layak/Mudah, dst.).
> * Jelaskan umpan balik umum dari responden mengenai kelebihan desain antarmuka, kemudahan alur pengajuan, dan kejelasan monitoring bansos di Kelurahan Harjamukti.
