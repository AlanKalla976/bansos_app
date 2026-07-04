# Rencana Pengujian Sistem Aplikasi Bantuan Sosial (Bansos-App)

Dokumen ini berisi rencana pengujian fungsionalitas (*functional testing*) untuk **Sistem Informasi Manajemen Bantuan Sosial (Bansos-App)** menggunakan metode Analytical Hierarchy Process (AHP) untuk pembobotan kriteria dan Multi-Objective Optimization on the basis of Ratio Analysis (MOORA) untuk pemeringkatan kelayakan.

*Catatan: Kolom **Akor** merujuk pada aktor/peran pengguna (Admin, Warga, Guest) yang menjalankan butir uji.*

| Kelas Uji | Kode Butir Uji | Nama Butir Uji | Akor | Hasil yang Diharapkan |
| :--- | :--- | :--- | :--- | :--- |
| **1. Autentikasi** | AUTH-01 | Registrasi Akun Warga Baru | Guest (Warga) | Warga berhasil mendaftar, data tersimpan di database, dan dialihkan ke halaman login dengan pesan sukses. |
| | AUTH-02 | Registrasi Akun dengan NIK/Email Duplikat | Guest (Warga) | Sistem menolak registrasi dan memunculkan pesan validasi bahwa NIK atau Email sudah terdaftar. |
| | AUTH-03 | Login Warga dengan Kredensial Valid | Guest (Warga) | Sistem memvalidasi kredensial, membuat session user, dan mengarahkan warga ke Dashboard Warga. |
| | AUTH-04 | Login Warga dengan Kredensial Salah | Guest (Warga) | Login gagal, sistem memunculkan pesan error "kredensial tidak cocok" dan mengembalikan user ke halaman login. |
| | AUTH-05 | Login Admin dengan Kredensial Valid | Guest (Admin) | Sistem memvalidasi kredensial admin, membuat session admin, dan mengarahkan ke Dashboard Admin. |
| | AUTH-06 | Login Admin dengan Kredensial Salah | Guest (Admin) | Login gagal, sistem memunculkan pesan error dan tetap di halaman login admin. |
| | AUTH-07 | Proteksi Halaman Admin (Middleware) | Guest (Non-Login) | Sistem memblokir akses langsung ke URL `/admin/*` dan mengalihkan pengunjung ke halaman login admin. |
| | AUTH-08 | Proteksi Halaman Warga (Middleware) | Guest (Non-Login) | Sistem memblokir akses langsung ke URL `/user/*` (kecuali login/register) dan mengalihkan pengunjung ke halaman login warga. |
| | AUTH-09 | Logout Akun Warga | Warga | Session warga dihapus, warga berhasil keluar dan diarahkan kembali ke halaman utama / login warga. |
| | AUTH-10 | Logout Akun Admin | Admin | Session admin dihapus, admin berhasil keluar dan diarahkan kembali ke halaman login admin. |
| **2. Manajemen Akun Masyarakat** | AKUN-01 | Menampilkan Daftar Akun Masyarakat | Admin | Sistem menampilkan tabel daftar seluruh akun masyarakat yang terdaftar dengan opsi edit/hapus. |
| | AKUN-02 | Menambahkan Akun Masyarakat Baru | Admin | Akun baru berhasil dibuat oleh admin, data masuk ke database, dan masuk dalam daftar warga. |
| | AKUN-03 | Mengubah Data Akun Masyarakat | Admin | Perubahan data (nama, email, status) berhasil disimpan dan diperbarui di tabel daftar akun masyarakat. |
| | AKUN-04 | Menghapus Data Akun Masyarakat | Admin | Akun masyarakat terpilih berhasil dihapus dari database serta daftar di dashboard admin. |
| **3. Manajemen Bantuan Sosial** | BANSOS-01 | Menampilkan Daftar Bantuan Sosial | Admin | Menampilkan daftar seluruh program bantuan sosial (contoh: PKH, BPNT, BLT) yang pernah dibuat. |
| | BANSOS-02 | Menambahkan Jenis Bantuan Sosial Baru | Admin | Program bansos baru berhasil ditambahkan beserta kuota dan periode pendaftaran. |
| | BANSOS-03 | Mengubah Data Bantuan Sosial | Admin | Perubahan informasi (nama bansos, kuota, periode, status aktif) berhasil disimpan. |
| | BANSOS-04 | Menghapus Data Bantuan Sosial | Admin | Program bansos terpilih berhasil dihapus dari sistem. |
| **4. Pendaftaran Pengajuan Bansos (Warga)** | PGB-W-01 | Menampilkan Daftar Bansos Aktif | Warga | Sistem menampilkan daftar program bansos yang sedang membuka pendaftaran. |
| | PGB-W-02 | Mengajukan Bansos (Mengisi Kriteria & Unggah Berkas) | Warga | Pengajuan bansos berhasil dikirim setelah mengisi seluruh kriteria dan mengunggah berkas syarat. Pengguna diarahkan ke halaman sukses. |
| | PGB-W-03 | Validasi Formulir Pengajuan Kosong/Kurang Lengkap | Warga | Sistem menolak pengiriman data dan memunculkan pesan validasi pada kolom yang belum terisi/berkas belum diunggah. |
| | PGB-W-04 | Menampilkan Riwayat Pengajuan Bansos | Warga | Warga dapat melihat daftar bansos yang telah diajukan beserta status verifikasinya (Diproses, Diverifikasi, Ditolak). |
| **5. Manajemen Pengajuan Bansos (Admin)** | PGB-A-01 | Menampilkan Daftar Semua Pengajuan | Admin | Admin dapat melihat seluruh daftar pengajuan dari berbagai warga beserta detail data kriteria dan berkas pendukung. |
| | PGB-A-02 | Verifikasi Berkas Pengajuan Warga | Admin | Admin dapat mengubah status pengajuan menjadi "Diverifikasi" (jika berkas valid) agar siap dinilai dalam perhitungan MOORA. |
| | PGB-A-03 | Ekspor Daftar Pengajuan ke Excel | Admin | Sistem berhasil mengunduh file `.xlsx` berisi seluruh data pengajuan bansos yang sesuai filter/pilihan. |
| | PGB-A-04 | Ekspor Daftar Pengajuan ke PDF | Admin | Sistem berhasil menghasilkan dan mengunduh berkas laporan format `.pdf` berisi daftar pengajuan. |
| **6. Manajemen Kriteria & Perhitungan AHP** | KRI-AHP-01 | Menampilkan Daftar Kriteria | Admin | Menampilkan daftar kode kriteria (C1, C2, dst.), nama kriteria, tipe (Benefit/Cost), dan bobot saat ini. |
| | KRI-AHP-02 | Menambahkan Kriteria Baru | Admin | Kriteria baru berhasil ditambahkan dan sistem menggenerate kode kriteria berurutan secara otomatis (misal C5 setelah C4). |
| | KRI-AHP-03 | Mengubah Nama/Tipe Kriteria | Admin | Perubahan nama kriteria atau jenis tipe (benefit/cost) berhasil disimpan. |
| | KRI-AHP-04 | Menghapus Kriteria Penilaian | Admin | Kriteria terhapus dari sistem, dan sistem otomatis melakukan *reindex* kode kriteria (C1, C2, dst.) agar berurutan kembali. |
| | KRI-AHP-05 | Menginput Nilai Matriks Perbandingan Berpasangan | Admin | Admin berhasil mengisi dan menyimpan nilai perbandingan berpasangan antarkriteria (skala 1-9). |
| | KRI-AHP-06 | Menghitung AHP dengan Matriks Konsisten (CR <= 0.1) | Admin | Perhitungan AHP berhasil dijalankan, menampilkan nilai Consistency Ratio (CR) <= 0.1, dan secara otomatis memperbarui nilai bobot kriteria di database. |
| | KRI-AHP-07 | Menghitung AHP dengan Matriks Tidak Konsisten (CR > 0.1) | Admin | Perhitungan menampilkan pesan error bahwa matriks tidak konsisten (CR > 0.1). Bobot baru tidak disimpan, dan admin diminta memperbaiki nilai perbandingan. |
| **7. Manajemen Sub Kriteria** | SUB-KRI-01 | Menampilkan Daftar Sub-Kriteria per Kriteria | Admin | Menampilkan daftar sub-kriteria beserta range nilai atau indikator deskriptif dan nilai skor bobotnya untuk kriteria yang dipilih. |
| | SUB-KRI-02 | Menambahkan Sub-Kriteria Baru | Admin | Sub-kriteria beserta skor nilainya berhasil ditambahkan di bawah kriteria induknya. |
| | SUB-KRI-03 | Mengubah Data Sub-Kriteria | Admin | Perubahan nama/parameter sub-kriteria beserta nilai skornya berhasil diperbarui. |
| | SUB-KRI-04 | Menghapus Sub-Kriteria | Admin | Sub-kriteria terpilih berhasil dihapus dari daftar penilaian. |
| **8. Penilaian Pengajuan & Perhitungan MOORA** | NIL-MOORA-01 | Menampilkan Pengajuan Siap Nilai | Admin | Menampilkan daftar pengajuan warga berstatus "Diverifikasi" yang belum diinput nilai kriteria penilaiannya. |
| | NIL-MOORA-02 | Menginput Nilai Penilaian Kriteria Warga | Admin | Admin berhasil memilih sub-kriteria (nilai alternatif) untuk pengajuan warga dan menyimpannya. |
| | NIL-MOORA-03 | Mengubah Nilai Penilaian Kriteria Warga | Admin | Admin berhasil memperbarui pilihan nilai sub-kriteria pada pengajuan warga yang sudah pernah dinilai. |
| | NIL-MOORA-04 | Menjalankan Perhitungan MOORA | Admin | Sistem melakukan normalisasi matriks keputusan, mengalikan dengan bobot kriteria AHP, menghitung nilai optimasi (Yi), melakukan pemeringkatan otomatis, dan menyimpannya ke session/database hasil. |
| **9. Laporan Hasil Akhir & Rangking** | HASIL-01 | Menampilkan Rangking Hasil Akhir MOORA | Admin | Sistem menampilkan tabel rangking penerima bansos terurut dari nilai Yi tertinggi, lengkap dengan nama warga, NIK, jenis bansos, nilai akhir Yi, dan keterangan kelayakan. |
| | HASIL-02 | Ekspor Laporan Pemeringkatan ke Excel | Admin | Laporan rangking hasil akhir MOORA berhasil diekspor ke file Excel `.xlsx`. |
| | HASIL-03 | Ekspor Laporan Pemeringkatan ke PDF | Admin | Laporan rangking hasil akhir MOORA berhasil diekspor ke berkas `.pdf`. |
| | HASIL-04 | Menampilkan Pengumuman Hasil Kelayakan di Sisi Warga | Warga | Halaman Hasil Akhir pada akun warga bersangkutan menampilkan status apakah yang bersangkutan layak menerima bansos berdasarkan batas kelayakan sistem. |
| **10. Manajemen Profil Warga** | PROFIL-01 | Menampilkan Detail Profil Warga | Warga | Sistem menampilkan nama, NIK, alamat, email, dan detail akun warga saat ini. |
| | PROFIL-02 | Memperbarui Informasi Profil | Warga | Warga berhasil mengedit data profil seperti alamat dan nomor telepon. Perubahan langsung tersimpan di database. |
| | PROFIL-03 | Mengubah Password dengan Password Lama yang Benar | Warga | Password berhasil diubah setelah sistem memverifikasi bahwa password lama yang diinput adalah benar. |
| | PROFIL-04 | Mengubah Password dengan Password Lama yang Salah | Warga | Sistem memblokir perubahan password dan memunculkan pesan error "Password lama tidak sesuai". |
