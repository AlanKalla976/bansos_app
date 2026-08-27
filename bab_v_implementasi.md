# BAB V IMPLEMENTASI PROGRAM

## 5.3 Implementasi Program

Bagian ini membahas mengenai rincian implementasi perangkat lunak dari sistem penentuan penerimaan bantuan sosial berbasis web menggunakan metode AHP (*Analytical Hierarchy Process*) dan MOORA (*Multi-Objective Optimization on the basis of Ratio Analysis*). Implementasi disajikan dalam bentuk potongan kode pemrograman (*source code*) pada sisi pengendali (*controller*) yang dibangun di atas kerangka kerja Laravel 12.

---

### 5.3.1 Implementasi Login

Proses masuk (*login*) ke dalam sistem membedakan hak akses berdasarkan penggolongan guard pengguna. Berikut adalah potongan listing kode untuk penanganan otentikasi login aktor administrator:

##### Listing 5.1 Implementasi Login Multi-Role (Admin, Petugas, Lurah)
##### Nama File: `app/Http/Controllers/Auth/AdminAuthController.php`
```php
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $admin = User::where('email', $request->email)
                     ->whereIn('role', ['admin', 'petugas', 'lurah'])
                     ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        // Tentukan guard secara dinamis berdasarkan role user
        $guard = $admin->role; 
        Auth::guard($guard)->login($admin, $request->boolean('remember'));

        if ($guard === 'petugas') {
            return redirect()->route('admin.petugas.dashboard')
                             ->with('success', 'Login berhasil! Selamat datang, ' . $admin->name . '.');
        } elseif ($guard === 'lurah') {
            return redirect()->route('admin.lurah.dashboard')
                             ->with('success', 'Login berhasil! Selamat datang, ' . $admin->name . '.');
        }

        return redirect()->route('admin.dashboard')
                         ->with('success', 'Login berhasil! Selamat datang, ' . $admin->name . '.');
    }
```

Method `login()` pada `AdminAuthController` berfungsi sebagai pintu gerbang autentikasi dinamis untuk aktor bertingkat, yaitu Administrator, Petugas, dan Lurah. Validasi masukan dilakukan menggunakan mekanisme bawaan Laravel untuk memverifikasi kesesuaian format `email` dan `password`. Kueri SQL ke tabel `users` menyaring email masukan serta mensyaratkan peran (`role`) berada dalam daftar `'admin'`, `'petugas'`, atau `'lurah'`.

Kata sandi murni dicocokkan dengan teks terenkripsi Bcrypt di database menggunakan `Hash::check()`. Jika kredensial valid, sistem mendeteksi peran pengguna secara dinamis dan mendaftarkan status sesi otentikasi melalui guard Laravel yang sesuai (`Auth::guard($guard)->login()`). Alur diakhiri dengan pengalihan rute ke dashboard spesifik perannya masing-masing (dashboard petugas, dashboard lurah, atau dashboard admin utama) disertai penayangan flash message sambutan.

---

### 5.3.2 Implementasi Kelola User

Manajemen data user dalam hal ini adalah penambahan akun masyarakat baru yang dilakukan oleh administrator sistem. Berikut potongan listing kodenya:

##### Listing 5.2 Implementasi Kelola User (Akun Masyarakat)
##### Nama File: `app/Http/Controllers/Admin/AkunMasyarakatController.php`
```php
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'      => ['required', 'digits:16', 'unique:users,nik'],
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nik.required'       => 'NIK wajib diisi.',
            'nik.digits'         => 'NIK harus 16 digit.',
            'nik.unique'         => 'NIK sudah terdaftar.',
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        AkunMasyarakat::create([
            'nik'      => $validated['nik'],
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'masyarakat',
        ]);

        return redirect()
            ->route('admin.akunmasyarakat.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }
```

Method `store()` pada `AkunMasyarakatController` mengelola proses penyimpanan informasi warga baru ke dalam basis data sistem. Filter awal dilakukan dengan menerapkan validasi input yang meliputi keunikan dan keabsahan NIK (16 digit), nama lengkap, keunikan surat elektronik (email), serta kecocokan kata sandi (`password`) yang diwajibkan memiliki panjang minimal 8 karakter dan sesuai dengan kolom konfirmasinya. Aturan ini dipasang guna mencegah terjadinya redundansi pendaftaran akun serta menjamin integritas data pengguna.

Setelah semua parameter masukan memenuhi kriteria validasi, sistem memanggil fungsi database `AkunMasyarakat::create()` untuk menyimpan data ke database. Pustaka keamanan `Hash::make()` dijalankan untuk mengonversi kata sandi menjadi teks acak terenkripsi Bcrypt satu arah sebelum disimpan ke database guna memproteksi data kredensial warga. Peran (*role*) akun secara default diklasifikasikan sebagai `'masyarakat'`. Setelah data sukses disimpan, sistem mengalihkan halaman ke tampilan indeks daftar masyarakat lengkap dengan penayangan notifikasi sukses.

---

### 5.3.3 Implementasi Kelola Kriteria

Pengelolaan kriteria penilaian penerima bantuan sosial dilakukan dengan mendefinisikan kriteria baru di dalam sistem. Berikut adalah potongan listing kodenya:

##### Listing 5.3 Implementasi Kelola Kriteria
##### Nama File: `app/Http/Controllers/Admin/KriteriaController.php`
```php
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:benefit,cost',
        ]);

        // Kode otomatis: C1, C2, C3, ... berdasarkan jumlah kriteria + 1
        $urutan       = Kriteria::count() + 1;
        $kodeKriteria = 'C' . $urutan;

        Kriteria::create([
            'kode_kriteria' => $kodeKriteria,
            'nama'          => $request->nama,
            'tipe'          => $request->tipe,
            'bobot'         => 0,
        ]);

        return back()->with('success', 'Kriteria berhasil ditambahkan. Perbarui perbandingan dan hitung ulang AHP.');
    }
```

Method `store()` pada `KriteriaController` digunakan untuk menambahkan elemen kriteria baru ke dalam sistem yang bertindak sebagai parameter evaluasi penerimaan bansos. Validasi sistem mewajibkan pengisian nama kriteria dan pembatasan tipe kriteria yang hanya terdiri dari kategori `'benefit'` (keuntungan) atau `'cost'` (biaya). Tipe ini krusial untuk menentukan orientasi arah dalam perhitungan normalisasi bobot keputusan pada metode MOORA nanti.

Dalam mempermudah klasifikasi data kriteria secara sistematis, sistem mengimplementasikan pembentukan kode kriteria secara otomatis. Caranya adalah dengan menghitung jumlah data kriteria saat ini (`Kriteria::count()`), menambahkan angka 1 sebagai indeks urutan berikutnya, lalu menggabungkannya dengan karakter `'C'` sehingga terbentuk pola kode berurutan seperti C1, C2, C3, dst. Setelah record kriteria baru disimpan ke tabel `kriterias` dengan nilai bobot *default* awal sebesar 0, halaman akan dialihkan kembali dengan memicu instruksi bagi administrator untuk memperbarui perbandingan kriteria berpasangan dan memicu ulang kalkulasi pembobotan AHP.

---

### 5.3.4 Implementasi Kelola Sub Kriteria

Sub-kriteria bertindak sebagai parameter klasifikasi nilai dari kriteria utama. Berikut adalah potongan listing kodenya:

##### Listing 5.4 Implementasi Kelola Sub Kriteria
##### Nama File: `app/Http/Controllers/Admin/SubKriteriaController.php`
```php
    public function store(Request $request)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriterias,kriteria_id',
            'nama'        => 'required|string|max:100',
            'nilai'       => 'required|numeric',
        ]);

        SubKriteria::create([
            'kriteria_id' => $request->kriteria_id,
            'nama'        => $request->nama,
            'nilai'       => (float) $request->nilai,
        ]);

        return redirect()->route('admin.subkriteria.index')
            ->with('success', 'Sub Kriteria berhasil ditambahkan.');
    }
```

Method `store()` pada `SubKriteriaController` ditujukan untuk menangani penyimpanan variabel pilihan dari kriteria utama beserta representasi bobot nilainya. Validasi menjamin keterkaitan relasi antar-tabel dengan menerapkan aturan `exists` pada `kriteria_id` ke tabel `kriterias`, serta tipe data `nilai` dikawal ketat agar berformat numerik.

Data sub-kriteria yang tersimpan di dalam database akan dipetakan ke dalam bentuk objek sub-kriteria baru. Kolom bobot nilai dikonversi secara eksplisit menjadi tipe data pecahan berpresisi tinggi (`(float)`) sebelum dicatatkan pada database guna menjamin konsistensi matematis nilai saat dikalikan dengan bobot kriteria. Alur ditutup dengan pemindahan rute ke indeks daftar sub-kriteria disertai penyematan notifikasi sukses.

---

### 5.3.5 Implementasi Kelola Pengajuan

Warga mendaftarkan permohonan penerimaan bantuan sosial dengan mengisi form pengajuan dan mengunggah dokumen persyaratan fisik. Berikut adalah potongan listing kodenya:

##### Listing 5.5 Implementasi Kelola Pengajuan Warga
##### Nama File: `app/Http/Controllers/User/PengajuanController.php`
```php
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'bantuan_sosial_id'  => 'required|exists:bantuan_sosials,id',
            'nama'               => 'required|string|max:100',
            'nik'                => 'required|string|max:20',
            'alamat'             => 'required|string|max:255',
            'no_telepon'         => 'required|string|max:20',
            'jenis_kelamin'      => 'required|in:L,P',
            'tanggal_lahir'      => 'required|date',
            'pendidikan'         => 'required|string|max:50',
            'penghasilan'        => 'required|numeric|min:0',
            'jumlah_tanggungan'  => 'required|integer|min:0',
            'pekerjaan'          => 'required|string|max:100',
            'kepemilikan_rumah'  => 'required|string|max:50',
            'kepemilikan_aset'   => 'nullable|array',
            'kepemilikan_aset.*' => 'string|max:100',
            'foto_ktp'           => 'required|image|max:2048',
            'foto_kk'            => 'required|image|max:2048',
            'foto_sktm'          => 'required|image|max:2048',
            'foto_rumah'         => 'required|image|max:2048',
        ]);

        // Cek duplikasi: user hanya boleh mengajukan 1x per jenis bantuan
        $exists = Pengajuan::where('user_id', $user->users_id)
            ->where('bantuan_sosial_id', $request->bantuan_sosial_id)
            ->exists();

        if ($exists) {
            $bantuan = BantuanSosial::find($request->bantuan_sosial_id);
            return redirect()->route('user.pengajuan.index')
                ->with('error', 'Anda sudah pernah mengajukan bantuan "' . ($bantuan->nama_bantuan ?? '') . '". Silakan pilih jenis bantuan lainnya.');
        }

        $fotos = [];
        foreach (['foto_ktp', 'foto_kk', 'foto_sktm', 'foto_rumah'] as $foto) {
            $fotos[$foto] = $request->file($foto)->store('pengajuan', 'public');
        }

        $pengajuan = Pengajuan::create([
            'user_id'           => $user->users_id,
            'bantuan_sosial_id' => $request->bantuan_sosial_id,
            'nama'              => $request->nama,
            'nik'               => $request->nik,
            'alamat'            => $request->alamat,
            'no_telepon'        => $request->no_telepon,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'tanggal_lahir'     => $request->tanggal_lahir,
            'pendidikan'        => $request->pendidikan,
            'penghasilan'       => $request->penghasilan,
            'jumlah_tanggungan' => $request->jumlah_tanggungan,
            'pekerjaan'         => $request->pekerjaan,
            'kepemilikan_rumah' => $request->kepemilikan_rumah,
            'kepemilikan_aset'  => $request->kepemilikan_aset ?? [],
            'status'            => 'Menunggu',
            ...$fotos,
        ]);

        return redirect()->route('user.pengajuan.success', $pengajuan->id);
    }
```

Method `store()` pada `PengajuanController` milik warga mengelola berkas permohonan bantuan sosial baru. Kriteria pengajuan divalidasi secara ketat meliputi keabsahan identitas (nama, NIK, alamat), kondisi finansial (penghasilan, tanggungan), serta unggahan berkas administratif wajib (Foto KTP, KK, SKTM, dan kondisi rumah) dengan kapasitas maksimal masing-masing 2MB. Untuk mencegah redundansi, sistem melakukan pengecekan duplikasi pengajuan berdasarkan kombinasi `user_id` dan `bantuan_sosial_id`. Jika ditemukan pengajuan sejenis yang sudah aktif, sistem membatalkan proses dan mengirim pesan kesalahan.

Jika proses pengecekan lolos, berkas dokumen gambar yang diunggah diproses menggunakan perulangan `foreach` untuk disimpan ke dalam direktori publik `storage/app/public/pengajuan` via fungsi bawaan Laravel `store()`, dan nama jalur file (*file path*) disimpan ke array `$fotos`. Selanjutnya, seluruh data pengajuan disimpan ke database dengan nilai awal kolom `status` diset otomatis menjadi `'Menunggu'`. Alur diakhiri dengan mengarahkan pemohon ke rute sukses pengajuan yang memuat konfirmasi pengajuan berhasil dikirimkan.

---

### 5.3.6 Implementasi Kelola Penilaian

Setelah berkas pengajuan warga diverifikasi, administrator harus memasukkan penilaian angka riil untuk masing-masing kriteria. Berikut potongan listing kodenya:

##### Listing 5.6 Implementasi Kelola Penilaian Alternatif
##### Nama File: `app/Http/Controllers/Admin/PenilaianController.php`
```php
    public function store(Request $request)
    {
        $request->validate([
            'pengajuan_id'               => 'required|exists:pengajuans,id',
            'penilaian'                  => 'required|array',
            'penilaian.*.kriteria_id'    => 'required|exists:kriterias,kriteria_id',
            'penilaian.*.subkriteria_id' => 'required|exists:sub_kriterias,subkriteria_id',
            'penilaian.*.nilai'          => 'required|numeric',
        ]);

        $pengajuan = Pengajuan::findOrFail($request->pengajuan_id);
        if ($pengajuan->status !== 'Diverifikasi') {
            return redirect()->route('admin.penilaian.index')
                ->with('error', 'Penilaian hanya dapat diinput untuk pengajuan yang telah divalidasi (status Diverifikasi).');
        }

        // Cek apakah sebelumnya sudah ada penilaian untuk pengajuan ini (untuk pesan yang sesuai)
        $isUpdate = Penilaian::where('pengajuan_id', $request->pengajuan_id)->exists();

        foreach ($request->penilaian as $item) {
            Penilaian::updateOrCreate(
                [
                    'pengajuan_id' => $request->pengajuan_id,
                    'kriteria_id'  => $item['kriteria_id'],
                ],
                [
                    'subkriteria_id' => $item['subkriteria_id'],
                    'nilai'          => $item['nilai'],
                ]
            );
        }

        $pesan = $isUpdate ? 'Penilaian berhasil diperbarui.' : 'Penilaian berhasil disimpan.';

        return redirect()->route('admin.penilaian.index')
            ->with('success', $pesan);
    }
```

Method `store()` pada `PenilaianController` digunakan oleh Administrator untuk merekam data parameter kriteria pengajuan warga (alternatif). Form validasi memverifikasi keberadaan data pengajuan, kriteria, dan subkriteria. Pengecekan tambahan dilakukan untuk memastikan bahwa pengajuan alternatif wajib berstatus `'Diverifikasi'` (telah divalidasi petugas) sebelum nilai diinputkan; jika tidak, sistem membatalkan pemrosesan guna menjamin kualitas validitas data alternatif.

Sistem juga mendeteksi riwayat pengisian sebelumnya dengan memeriksa keberadaan record penilaian melalui kueri `exists()`. Skenario penyimpanan menggunakan iterasi `foreach` yang mengeksekusi Eloquent `updateOrCreate()`. Jika kombinasi kunci alternatif dan kriteria terdeteksi ganda, kolom subkriteria dan nilai langsung diperbarui (*update*), dan record baru (*create*) dibentuk jika data belum pernah diisi. Variabel notifikasi `$pesan` diatur secara dinamis untuk memberikan penegasan status penambahan baru atau pembaruan data sebelum dialihkan ke halaman utama penilaian.

---

### 5.3.7 Implementasi Perhitungan AHP

Perhitungan AHP digunakan untuk menentukan nilai bobot dari masing-masing kriteria secara konsisten berdasarkan matriks perbandingan berpasangan. Berikut potongan listing kodenya:

##### Listing 5.7 Perhitungan Pembobotan Kriteria AHP
##### Nama File: `app/Http/Controllers/Admin/KriteriaController.php`
```php
    public function hitungAhp()
    {
        $hasil = $this->ahpService->hitung();

        if ($hasil['error']) {
            return back()->with('error', $hasil['error']);
        }

        // Serialisasi Collection ke plain array agar tidak corrupt di session
        $hasilSession = [
            'kriterias'   => $hasil['kriterias']->map(fn($k) => [
                'kriteria_id'   => $k->kriteria_id,
                'kode_kriteria' => $k->kode_kriteria,
                'nama'          => $k->nama,
                'tipe'          => $k->tipe,
                'bobot'         => $k->bobot,
            ])->toArray(),
            'n'           => $hasil['n'],
            'matrix'      => $hasil['matrix'],
            'jumlahKolom' => $hasil['jumlahKolom'],
            'normalized'  => $hasil['normalized'],
            'eigenVector' => $hasil['eigenVector'],
            'weightedSum' => $hasil['weightedSum'],
            'lambdaMax'   => $hasil['lambdaMax'],
            'ci'          => $hasil['ci'],
            'ri'          => $hasil['ri'],
            'cr'          => $hasil['cr'],
            'konsisten'   => $hasil['konsisten'],
        ];

        if ($hasil['konsisten']) {
            $this->ahpService->simpanBobot($hasil['eigenVector'], $hasil['kriterias']);

            return back()
                ->with('success', 'Perbandingan Konsisten! Bobot kriteria berhasil disimpan ke database.')
                ->with('hasil_ahp', $hasilSession);
        }

        return back()
            ->with('error', 'Perbandingan Tidak Konsisten! CR = ' . number_format($hasil['cr'], 4) . '. Silakan perbaiki nilai perbandingan dan hitung ulang.')
            ->with('hasil_ahp', $hasilSession);
    }
```

Method `hitungAhp()` di dalam `KriteriaController` bertanggung jawab sebagai orkestrator utama perhitungan pembobotan kriteria menggunakan metode Analytical Hierarchy Process (AHP). Pemrosesan aljabar linier didelegasikan sepenuhnya pada `AhpService->hitung()`, yang menghitung normalisasi matriks perbandingan, prioritas vektor (*eigen vector*), lamda maks, indeks konsistensi (CI), dan rasio konsistensi (CR). Hasil kalkulasi mentah berupa Collection diserialisasikan ke dalam bentuk *plain array* pada variabel `$hasilSession` agar data dapat tersimpan dengan aman di dalam session Laravel tanpa resiko corrupt.

Sebelum menyimpan bobot kriteria secara permanen ke database, sistem melakukan pengujian konsistensi matematis yang tersimpan di `$hasil['konsisten']`. Berdasarkan teori AHP Saaty, perbandingan dikatakan valid dan konsisten secara logis jika nilai *Consistency Ratio* (CR) bernilai kurang dari atau sama dengan 0.10. Jika kondisi tersebut terpenuhi (`$hasil['konsisten']` bernilai `true`), sistem mengeksekusi `simpanBobot()` untuk memperbarui kolom `bobot` pada database tabel `kriterias` dan mengembalikan respons sukses. Namun, apabila nilai CR > 0.10, data pembobotan ditolak untuk disimpan dan admin diperingatkan melalui pesan error untuk melakukan evaluasi ulang terhadap matriks perbandingan berpasangan.

---

### 5.3.8 Implementasi Perhitungan MOORA

Pemeringkatan pengajuan calon penerima bantuan sosial diselesaikan menggunakan pemrosesan matriks keputusan dengan algoritma MOORA. Berikut potongan listing kodenya:

##### Listing 5.8 Pemeringkatan Alternatif Menggunakan Metode MOORA
##### Nama File: `app/Http/Controllers/Admin/PenilaianController.php`
```php
    public function hitungMoora()
    {
        $hasil = $this->mooraService->hitung();

        if ($hasil['error']) {
            return redirect()->route('admin.penilaian.index')
                ->with('error', $hasil['error']);
        }

        // Tentukan status Layak/Tidak Layak berdasarkan kuota per jenis bantuan
        $rankedWithStatus = $this->tentukanStatusByKuota($hasil['ranked']);

        // Konversi agar aman di session
        $hasilSession = [
            'kriterias'   => $hasil['kriterias']->map(fn($k) => [
                'kriteria_id'   => $k->kriteria_id,
                'kode_kriteria' => $k->kode_kriteria,
                'nama'          => $k->nama,
                'bobot'         => $k->bobot,
                'tipe'          => $k->tipe,
            ])->toArray(),
            'pengajuans'  => $hasil['pengajuans']->map(fn($p) => [
                'id'            => $p->id,
                'nama'          => $p->nama,
                'nik'           => $p->nik,
                'jenis_bantuan' => $p->bantuanSosial->nama_bantuan ?? '-',
            ])->toArray(),
            'matrix'      => $hasil['matrix'],
            'akarKuadrat' => $hasil['akarKuadrat'],
            'normalized'  => $hasil['normalized'],
            'yi'          => $hasil['yi'],
            'ranked'      => array_map(fn($r) => [
                'index'         => $r['index'],
                'nama'          => $r['pengajuan']->nama,
                'nik'           => $r['pengajuan']->nik,
                'jenis_bantuan' => $r['pengajuan']->bantuanSosial->nama_bantuan ?? '-',
                'kuota'         => $r['pengajuan']->bantuanSosial->kuota ?? 0,
                'yi'            => $r['yi'],
                'status'        => $r['status'],
            ], $rankedWithStatus),
            'n' => $hasil['n'],
            'm' => $hasil['m'],
        ];

        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Perhitungan MOORA berhasil! Hasil telah disimpan.')
            ->with('hasil_moora', $hasilSession);
    }
```

Method `hitungMoora()` di dalam `PenilaianController` memproses algoritma optimasi pemeringkatan multi-kriteria untuk alternatif pengajuan warga. Proses kalkulasi dioperasikan melalui `MooraService->hitung()`, yang menyusun matriks keputusan, menormalkan data, mengalikan dengan bobot kriteria AHP, serta mengkalkulasikan nilai optimasi $Y_i$. Modifikasi baru pada sistem melibatkan penentuan status kelayakan (Layak/Tidak Layak) secara lebih adaptif dengan mengeksekusi fungsi `tentukanStatusByKuota()`. Fungsi ini mengelompokkan kandidat berdasarkan program bantuan, membandingkan posisi rangking dengan kuota masing-masing bansos yang terdaftar pada database, kemudian menentukan kelayakan secara akurat tanpa batasan ambang batas (*threshold*) statis.

Hasil kalkulasi diubah menjadi struktur array `$hasilSession` untuk meminimalisir kegagalan pembacaan data session pada Laravel. Array ini menyajikan detail matriks perhitungan, data parameter, nilai akhir optimasi $Y_i$, kuota jenis bantuan, serta status kelayakan yang dihasilkan. Alur diakhiri dengan mengembalikan respons ke rute utama penilaian alternatif sembari mengirimkan payload session hasil MOORA untuk dirender secara dinamis di antarmuka admin.

---

### 5.3.9 Implementasi Hasil Ranking

Hasil akhir dari pemrosesan pemeringkatan disajikan dalam bentuk daftar rangking penerima bantuan sosial. Berikut potongan listing kodenya:

##### Listing 5.9 Menampilkan Laporan Hasil Ranking Pemeringkatan MOORA
##### Nama File: `app/Http/Controllers/Admin/HasilAkhirController.php`
```php
    public function index(Request $request)
    {
        // Ambil SEMUA data dulu (tanpa filter jenis_bantuan/status, tanpa pagination)
        // supaya global_ranking, ranking_in_bantuan, dan status per-kuota dihitung
        // dari populasi data yang lengkap dan benar.
        $baseQuery = HasilAkhir::with(['pengajuan.bantuanSosial']);

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->whereHas('pengajuan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',  'like', "%{$search}%");
            });
        }

        // Ambil semua data (sudah kena filter search jika ada), lalu urutkan
        // berdasarkan nilai_yi (bukan kolom ranking) supaya tidak bolong.
        $allItems = $baseQuery->get()->sortByDesc('nilai_yi')->values();
        $allItems = $this->attachStatusByKuota($allItems);

        $jenisBantuanId = $request->input('jenis_bantuan');

        // Filter jenis bantuan diterapkan SETELAH ranking dihitung, supaya
        // ranking_in_bantuan tetap konsisten (mulai dari 1 untuk jenis itu).
        $filtered = $allItems;
        if ($jenisBantuanId) {
            $filtered = $filtered->filter(function ($h) use ($jenisBantuanId) {
                return ($h->pengajuan->bantuanSosial->id ?? null) == $jenisBantuanId;
            })->values();
        }

        // Filter status (Layak/Tidak Layak) diterapkan setelah status dihitung dari kuota
        if ($request->filled('status')) {
            $filtered = $filtered->filter(function ($h) use ($request) {
                return $h->status_computed === $request->status;
            })->values();
        }

        // Tentukan ranking yang ditampilkan di tabel:
        // - Tidak difilter jenis bantuan -> pakai global_ranking
        // - Difilter jenis bantuan tertentu -> pakai ranking_in_bantuan (mulai dari 1 lagi)
        foreach ($filtered as $h) {
            $h->ranking_display = $jenisBantuanId ? $h->ranking_in_bantuan : $h->global_ranking;
        }

        // Pagination manual
        $perPage = 10;
        $page    = $request->get('page', 1);
        $offset  = ($page - 1) * $perPage;

        $itemsForPage = $filtered->slice($offset, $perPage)->values();

        $hasilAkhirs = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForPage,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $jenisBantuanList = BantuanSosial::pluck('nama_bantuan', 'id');

        // Statistik total berdasarkan status hasil perhitungan kuota (bukan kolom status di DB)
        // Dihitung dari SELURUH data (tanpa filter apapun) supaya statistik tetap global.
        $allForStats = $this->attachStatusByKuota(
            HasilAkhir::with(['pengajuan.bantuanSosial'])->get()
        );

        $total           = $allForStats->count();
        $totalLayak      = $allForStats->where('status_computed', 'Layak')->count();
        $totalTidakLayak = $allForStats->where('status_computed', 'Tidak Layak')->count();

        return view('admin.hasilakhir.index', compact(
            'hasilAkhirs',
            'jenisBantuanList',
            'total',
            'totalLayak',
            'totalTidakLayak'
        ));
    }
```

Method `index()` pada `HasilAkhirController` bertugas mengolah dan menyajikan laporan kelayakan akhir penerima bansos. Untuk mencegah kesalahan kalkulasi ranking, sistem mengambil populasi data secara menyeluruh (`$baseQuery->get()`) sebelum melakukan filter jenis bantuan atau status kelayakan. Data kemudian diurutkan berdasarkan `nilai_yi` secara desendens guna menetapkan `global_ranking` dan `ranking_in_bantuan` serta status kelayakan dinamis (`status_computed`) berdasarkan kuota melalui fungsi penolong `attachStatusByKuota()`. 

Setelah struktur ranking dihitung secara utuh, sistem menerapkan filter pencarian nama/NIK, penyaringan program bantuan, dan penyaringan status kelayakan secara bertahap. Sistem kemudian menentukan ranking visual (`ranking_display`) yang akan disajikan pada tabel secara dinamis. Karena data berupa Collection yang difilter secara manual di level aplikasi, sistem mengimplementasikan pembagian halaman secara manual (*manual pagination*) menggunakan `LengthAwarePaginator` Laravel. Terakhir, panel statistik mengukur persentase penerima dengan menjumlahkan data kelayakan yang konsisten secara global sebelum data dialirkan ke halaman visualisasi.

---

### 5.3.10 Implementasi Monitoring Penyaluran

Dashboard menyajikan statistik agregat monitoring pendaftaran pengajuan dan visualisasi grafik penyaluran bantuan sosial. Berikut potongan listing kodenya:

##### Listing 5.10 Monitoring Penyaluran Bantuan Sosial
##### Nama File: `app/Http/Controllers/Admin/DashboardController.php`
```php
    public function index(Request $request)
    {
        if ($request->is('admin/petugas') || $request->is('admin/petugas/*')) {
            $admin = Auth::guard('petugas')->user();
        } elseif ($request->is('admin/lurah') || $request->is('admin/lurah/*')) {
            $admin = Auth::guard('lurah')->user();
        } else {
            $admin = Auth::guard('admin')->user();
        }

        // ── DASHBOARD PETUGAS ──
        if ($admin->role === 'petugas') {
            $stats = [
                'menunggu_validasi' => \App\Models\Pengajuan::where('status', 'Menunggu')->count(),
                'pengajuan_valid'    => \App\Models\Pengajuan::where('status', 'Diverifikasi')->count(),
                'pengajuan_tidak_valid' => \App\Models\Pengajuan::where('status', 'Ditolak')->count(),
                'belum_dijadwalkan'  => \App\Models\Penyaluran::where('status', 'Belum Dijadwalkan')->count(),
                'sudah_dijadwalkan'  => \App\Models\Penyaluran::where('status', 'Sudah Dijadwalkan')->count(),
                'bantuan_diambil'    => \App\Models\Penyaluran::where('status', 'Sudah Diambil')->count(),
                'tepat_waktu'        => \App\Models\Monitoring::where('ketepatan_waktu', 'Tepat Waktu')->count(),
                'terlambat'          => \App\Models\Monitoring::where('ketepatan_waktu', 'Terlambat')->count(),
                'sesuai_sasaran'     => \App\Models\Monitoring::where('ketepatan_sasaran', 'Sesuai Sasaran')->count(),
                'tidak_sesuai'       => \App\Models\Monitoring::where('ketepatan_sasaran', 'Tidak Sesuai Sasaran')->count(),
            ];

            return view('admin.dashboard.index', compact('admin', 'stats'));
        }

        // ── DASHBOARD LURAH ──
        if ($admin->role === 'lurah') {
            $stats = [
                'total_calon'        => \App\Models\HasilAkhir::count(),
                'menunggu_setuju'    => \App\Models\HasilAkhir::where('persetujuan_status', 'Menunggu Persetujuan')->count(),
                'disetujui'          => \App\Models\HasilAkhir::where('persetujuan_status', 'Disetujui')->count(),
                'ditolak'            => \App\Models\HasilAkhir::where('persetujuan_status', 'Ditolak')->count(),
                'total_penyaluran'   => \App\Models\Penyaluran::where('status', 'Sudah Diambil')->count(),
                'tepat_waktu'        => \App\Models\Monitoring::where('ketepatan_waktu', 'Tepat Waktu')->count(),
                'terlambat'          => \App\Models\Monitoring::where('ketepatan_waktu', 'Terlambat')->count(),
                'sesuai_sasaran'     => \App\Models\Monitoring::where('ketepatan_sasaran', 'Sesuai Sasaran')->count(),
                'tidak_sesuai'       => \App\Models\Monitoring::where('ketepatan_sasaran', 'Tidak Sesuai Sasaran')->count(),
                ...
            ];

            return view('admin.dashboard.index', compact('admin', 'stats'));
        }

        // ── DASHBOARD ADMIN (Lama/Default) ──
        $totalMasyarakat = User::where('role', 'masyarakat')->count();
        $totalPengajuan  = Pengajuan::count();

        $totalBPNT = Pengajuan::whereHas('bantuanSosial', fn($q) =>
            $q->where('nama_bantuan', 'LIKE', '%BPNT%')
        )->count();

        $totalBLT = Pengajuan::whereHas('bantuanSosial', fn($q) =>
            $q->where('nama_bantuan', 'LIKE', '%BLT%')
        )->count();

        $totalPKH = Pengajuan::whereHas('bantuanSosial', fn($q) =>
            $q->where('nama_bantuan', 'LIKE', '%PKH%')
        )->count();

        // Hitung Layak/Tidak Layak berdasarkan kuota per jenis bantuan
        $totalLayak      = 0;
        $totalTidakLayak = 0;

        $semuaBantuan = BantuanSosial::all();
        foreach ($semuaBantuan as $bantuan) {
            $kuota = $bantuan->kuota ?? 0;
            $hasil = HasilAkhir::whereHas('pengajuan', fn($q) =>
                $q->where('bantuan_sosial_id', $bantuan->id)
            )->orderBy('ranking')->get();

            foreach ($hasil as $item) {
                if ($item->ranking <= $kuota) {
                    $totalLayak++;
                } else {
                    $totalTidakLayak++;
                }
            }
        }

        // Filter Dashboard
        $tahunList = HasilAkhir::selectRaw('YEAR(created_at) as tahun')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        if ($tahunList->isEmpty()) {
            $tahunList = collect([date('Y')]);
        }

        $filterTahun   = $request->tahun ?? date('Y');
        $filterBantuan = $request->jenis_bantuan ?? 'semua';

        $jenisBantuanList = BantuanSosial::all();

        // Monitoring Chart
        $chartData = $this->getChartData($filterTahun, $filterBantuan);

        // 5 Hasil Terbaru
        $hasilTerbaru = HasilAkhir::with([
                'pengajuan',
                'pengajuan.user',
                'pengajuan.bantuanSosial',
            ])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'admin',
            'totalMasyarakat',
            'totalPengajuan',
            'totalBPNT',
            'totalBLT',
            'totalPKH',
            'totalLayak',
            'totalTidakLayak',
            'tahunList',
            'filterTahun',
            'filterBantuan',
            'jenisBantuanList',
            'chartData',
            'hasilTerbaru'
        ));
    }
```

Method `index()` pada `DashboardController` menyajikan antarmuka pemantauan data statistik yang disesuaikan secara adaptif berdasarkan peran dari aktor yang terautentikasi (Multi-Role Dashboard). Pengguna diidentifikasi melalui validasi rute URL request (`$request->is()`), kemudian dialihkan menggunakan guard otentikasi yang bersangkutan (Petugas, Lurah, atau Admin Utama). Di sisi Petugas, sistem merangkum jumlah berkas yang menunggu divalidasi, pengajuan ditolak, status penjadwalan penyaluran, serta indikator ketepatan waktu pengiriman. Di sisi Lurah, sistem memonitor akumulasi calon penerima, persetujuan status lurah, total penyaluran terealisasi, dan penilaian dampak kebermanfaatan bansos.

Pada sisi Administrator Utama, sistem menyusun data statistik berupa volume pengajuan kualitatif, status program khusus (BPNT, BLT, PKH), serta visualisasi statistik kelayakan. Data jumlah penerima layak (`totalLayak`) dan tidak layak (`totalTidakLayak`) dihitung secara otomatis dengan membandingkan parameter ranking alternatif terhadap kuota riil masing-masing jenis program bantuan sosial yang tersimpan di database (`$bantuan->kuota`). Data komprehensif ini digabungkan bersama grafik series waktu tahunan dan riwayat kalkulasi terbaru untuk diumpankan ke view dashboard.

---

### 5.3.11 Implementasi Kelola Bantuan Sosial

Data program bantuan sosial didaftarkan ke dalam sistem agar warga dapat mengajukan permohonan bantuan tersebut. Berikut potongan listing kodenya:

##### Listing 5.11 Menyimpan Data Program Bantuan Sosial Baru
##### Nama File: `app/Http/Controllers/Admin/BantuanSosialController.php`
```php
    public function store(Request $request)
    {
        $request->validate([
            'nama_bantuan' => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
            'kuota'        => 'required|integer|min:0',
        ]);

        BantuanSosial::create($request->only('nama_bantuan', 'deskripsi', 'kuota'));

        return redirect()->route('admin.bantuansosial.index')
                         ->with('success', 'Data bantuan sosial berhasil ditambahkan.');
    }
```

Method `store()` pada `BantuanSosialController` memproses perekaman jenis program bantuan sosial yang baru ke dalam basis data. Selain nama program bantuan dan deskripsi detailnya, sistem mewajibkan penginputan atribut `kuota` penyaluran dalam bentuk bilangan bulat non-negatif (`integer|min:0`). Penambahan kuota ini penting sebagai dasar pembatas kapasitas maksimum penerimaan bantuan pada proses perankingan dan visualisasi statistik kelayakan sistem.

Data bansos yang tervalidasi kemudian diumpankan ke method `BantuanSosial::create()` secara aman melalui filter parameter `$request->only()`. Penggunaan pembatasan input ini ditujukan untuk memblokir anomali pengiriman data ilegal di luar schema (*mass assignment protection*). Terakhir, sistem mengalihkan rute kembali ke tampilan daftar bantuan sosial serta menyematkan notifikasi sukses ke session.

---

### 5.3.12 Implementasi Logout

Proses penutupan sesi yang sedang aktif dan penghancuran token otentikasi. Berikut potongan listing kodenya:

##### Listing 5.12 Proses Logout Multi-Guard dan Pembersihan Sesi
##### Nama File: `app/Http/Controllers/Auth/AdminAuthController.php`
```php
    public function logout(Request $request)
    {
        // Logout guard yang sedang aktif
        foreach (['admin', 'petugas', 'lurah'] as $g) {
            if (Auth::guard($g)->check()) {
                Auth::guard($g)->logout();
            }
        }

        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
                         ->with('success', 'Anda berhasil logout.');
    }
```

Method `logout()` di dalam `AdminAuthController` berfungsi mengakhiri sesi aktif pengguna secara aman bagi seluruh jajaran peran yang terdaftar (Multi-Guard Logout). Sistem melakukan perulangan `foreach` untuk memindai status otentikasi pada guard `'admin'`, `'petugas'`, dan `'lurah'`. Apabila terdeteksi guard yang aktif, fungsi `Auth::guard($g)->logout()` dieksekusi guna mencabut kredensial pengguna.

Untuk menghindari celah kerentanan pembajakan token (*session fixation*), token CSRF diubah dengan memanggil `$request->session()->regenerateToken()`. Pengguna yang sukses keluar sistem kemudian dialihkan ke rute utama login admin formal dibarengi flash session pemberitahuan keluar sistem sukses.

---

### 5.3.13 Implementasi Landing Page

Halaman Beranda (*landing page*) publik merupakan komponen visual awal yang menjelaskan fungsionalitas sistem dan memfasilitasi akses warga maupun administrator. Berikut potongan struktur listing kodenya:

##### Listing 5.13 Struktur Halaman Landing Page Publik
##### Nama File: `resources/views/welcome.blade.php`
```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Penerimaan & Monitoring Bantuan Sosial (AHP-MOORA)</title>
    <!-- font & style sheets (...) -->
</head>
<body>

    <!-- ── NAV HEADER ── -->
    <header>
        <a href="/" class="logo-container">
            <div class="logo-icon">B</div>
            <div class="logo-text">Bansos<span>AHP-MOORA</span></div>
        </a>

        <div class="nav-menu">
            <a href="#cara-kerja" class="nav-link">Cara Kerja</a>
            <a href="#kriteria" class="nav-link">Metode Seleksi</a>
        </div>

        <div class="nav-btn-group">
            @auth
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline">Dashboard</a>
            @else
                <a href="{{ route('user.register') }}" class="btn btn-ghost">Daftar Warga</a>
                <a href="{{ route('user.login') }}" class="btn btn-primary">Login Warga</a>
            @endauth
        </div>
    </header>

    <!-- ── HERO SECTION ── -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="pulse"></span>
                Sistem Pendukung Keputusan
            </div>
            <h1>Penyaluran Bansos <span>Lebih Adil</span> dan <span>Tepat Sasaran</span></h1>
            <p class="hero-description">
                Sistem penentuan kelayakan penerima bantuan sosial menggunakan perpaduan ilmiah metode AHP untuk merancang bobot kriteria secara konsisten, serta MOORA untuk melakukan pemeringkatan secara objektif...
            </p>
            <div class="hero-btn-group">
                @auth
                    <a href="{{ route('user.dashboard') }}" class="btn btn-lg btn-primary">Buka Dashboard Anda</a>
                @else
                    <a href="{{ route('user.login') }}" class="btn btn-lg btn-primary">Ajukan Bantuan</a>
                    <a href="{{ route('admin.login') }}" class="btn btn-lg btn-outline">Portal Administrator →</a>
                @endauth
            </div>
        </div>

        <div class="hero-illustration">
            <!-- Representasi grafis hasil pemeringkatan MOORA (...) -->
            <div class="illustration-card">
                <div class="illustration-header">
                    <span class="illustration-title">Penerimaan Bansos</span>
                    <span class="status-badge status-verified">Terhitung MOORA</span>
                </div>
                <div class="illustration-body">
                    <!-- Iterasi data pemeringkatan (...) -->
                </div>
            </div>
        </div>
    </section>

    <!-- ── STATS SECTION & METHODOLOGY FLOW (...) -->

</body>
</html>
```

Halaman Beranda (*landing page*) publik pada berkas `welcome.blade.php` merupakan representasi antarmuka publik yang diakses langsung oleh pengunjung sistem tanpa perlu melewati mekanisme autentikasi. Halaman ini memegang peranan penting sebagai gerbang informasi awal bagi masyarakat umum dan administrator mengenai integrasi sistem pendukung keputusan (DSS) menggunakan metode AHP dan MOORA. Tampilan antarmuka ini dirancang secara dinamis menggunakan direktif blade `@auth` untuk menyajikan tombol akses menu secara kontekstual, di mana pengguna yang telah terautentikasi akan disajikan tombol pintas menuju dashboard, sedangkan pengguna baru diarahkan ke tautan registrasi dan login.

Dari aspek arsitektural informasi, landing page ini dibagi menjadi beberapa bagian terstruktur, yaitu header navigasi, bagian hero yang menyajikan konsep utama penelitian, panel statistik agregasi kinerja sistem, deskripsi ringkas alur metodologi perhitungan (pengajuan berkas, kalkulasi bobot kriteria AHP, dan optimasi pemeringkatan MOORA), serta footer navigasi. Penyajian informasi secara hierarkis ini bertujuan untuk mengedukasi warga mengenai transparansi proses seleksi berbasis kriteria objektif kemiskinan yang diterapkan secara merata, guna meminimalisir kecemburuan sosial dalam distribusi bantuan.

---

## 5.4 Pengujian Sistem

Bagian ini membahas mengenai pengujian fungsionalitas (*functional testing*) pada Sistem Informasi Manajemen Bantuan Sosial (Bansos-App). Pengujian dilakukan untuk memastikan seluruh unit fungsional sistem berjalan dengan baik dan menghasilkan keluaran yang sesuai dengan rancangan. Format pengujian mengacu pada format standar tabel pengujian sistem yang disajikan secara terstruktur menggunakan parameter kasus uji, kondisi awal, dependensi, langkah-langkah pengujian, data masukan, serta hasil akhir pengujian.

Berikut adalah tabel hasil pengujian fungsionalitas sistem untuk setiap modul yang terdapat dalam aplikasi:

### 5.4.1 Modul 1: Autentikasi

#### AUTH-01: Registrasi Akun Warga Baru

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pendaftaran akun warga baru dengan mengisi seluruh data pendaftaran yang valid. |
| **Kondisi Awal** | Pengguna berada di halaman registrasi warga (`/register`) dan NIK belum terdaftar. |
| **Dependesi** | Koneksi basis data (database MySQL/MariaDB) dalam kondisi aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Akses menu registrasi warga lewat URL browser. | URL: `/register` | Sistem menampilkan halaman formulir pendaftaran warga baru dengan kolom NIK, nama, email, password, dan konfirmasi password. | Halaman formulir pendaftaran warga baru ditampilkan secara lengkap. | Sukses | Sesuai |
| 2 | Masukkan data registrasi valid pada seluruh kolom input lalu klik tombol "Daftar". | NIK: `3273012345678901`<br>Nama: `Ahmad Sodikin`<br>Email: `ahmad@example.com`<br>Password: `password123`<br>Konfirmasi: `password123` | Sistem memvalidasi input, menyimpan record baru ke database, dan mengalihkan warga ke halaman login dengan pesan sukses. | Warga berhasil mendaftar, data tersimpan di database, dan dialihkan ke login dengan pesan sukses. | Sukses | Sesuai (Lihat Listing 5.2) |

**Kondisi Akhir:** Data akun warga baru tersimpan di tabel `users` dengan peran (`role`) `'masyarakat'` dan password terenkripsi Bcrypt.

---

#### AUTH-02: Registrasi Akun dengan NIK/Email Duplikat

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penolakan sistem ketika pendaftaran menggunakan NIK atau Email yang sudah ada di database. |
| **Kondisi Awal** | Pengguna berada di halaman registrasi warga dan NIK `3273012345678901` atau Email `ahmad@example.com` sudah terdaftar. |
| **Dependesi** | Koneksi basis data aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan data pendaftaran baru menggunakan NIK yang sudah terdaftar di database lalu klik "Daftar". | NIK: `3273012345678901`<br>Nama: `Budi Hartono`<br>Email: `budi@example.com`<br>Password: `password123`<br>Konfirmasi: `password123` | Sistem mendeteksi duplikasi NIK, membatalkan registrasi, dan memunculkan pesan validasi kesalahan. | Sistem menolak registrasi dan memunculkan pesan validasi bahwa NIK sudah terdaftar. | Sukses | Sesuai (Lihat Listing 5.2) |
| 2 | Masukkan data pendaftaran baru menggunakan Email yang sudah terdaftar di database lalu klik "Daftar". | NIK: `3273012345678902`<br>Nama: `Budi Hartono`<br>Email: `ahmad@example.com`<br>Password: `password123`<br>Konfirmasi: `password123` | Sistem mendeteksi duplikasi Email, membatalkan registrasi, dan memunculkan pesan validasi kesalahan. | Sistem menolak registrasi dan memunculkan pesan validasi bahwa Email sudah terdaftar. | Sukses | Sesuai (Lihat Listing 5.2) |

**Kondisi Akhir:** Tidak ada data baru yang tersimpan ke tabel `users`.

---

#### AUTH-03: Login Warga dengan Kredensial Valid

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji proses masuk (login) akun warga menggunakan email dan password yang valid. |
| **Kondisi Awal** | Pengguna berada di halaman login warga (`/login`) dan akun warga terdaftar. |
| **Dependesi** | Koneksi basis data aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan email dan password warga yang valid pada kolom input lalu klik "Login". | Email: `ahmad@example.com`<br>Password: `password123` | Sistem memvalidasi kredensial, membuat session user, dan mengarahkan warga ke Dashboard Warga. | Sistem berhasil melakukan autentikasi, membuat session, dan mengalihkan warga ke Dashboard. | Sukses | Sesuai |

**Kondisi Akhir:** Warga berhasil masuk ke halaman `/user/dashboard` dengan session aktif.

---

#### AUTH-04: Login Warga dengan Kredensial Salah

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penolakan sistem saat login warga menggunakan data kredensial yang salah. |
| **Kondisi Awal** | Pengguna berada di halaman login warga. |
| **Dependesi** | Koneksi basis data aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan email terdaftar dengan password yang salah lalu klik "Login". | Email: `ahmad@example.com`<br>Password: `passwordSalah` | Login gagal, sistem menampilkan pesan kesalahan "kredensial tidak cocok" dan tetap di halaman login. | Login gagal, sistem menampilkan pesan error "kredensial tidak cocok" dan mengembalikan user ke halaman login. | Sukses | Sesuai |

**Kondisi Akhir:** Tidak ada session terbuat, pengguna tetap berada pada halaman `/login`.

---

#### AUTH-05: Login Admin dengan Kredensial Valid

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-05 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji login aktor administrator menggunakan email dan password yang valid. |
| **Kondisi Awal** | Administrator berada di halaman login admin (`/admin/login`). |
| **Dependesi** | Akun admin terdaftar di database dengan `role = 'admin'`. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan email dan password admin yang benar lalu klik tombol "Login". | Email: `admin@bansos.com`<br>Password: `adminpassword` | Sistem memvalidasi kredensial admin, membuat session admin, dan mengarahkan ke Dashboard Admin. | Kredensial berhasil divalidasi, session admin dibuat, dan dialihkan ke Dashboard Admin. | Sukses | Sesuai (Lihat Listing 5.1) |

**Kondisi Akhir:** Administrator berhasil masuk ke halaman `/admin/dashboard` dengan guard `admin` aktif.

---

#### AUTH-06: Login Admin dengan Kredensial Salah

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-06 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penolakan sistem saat admin login menggunakan kredensial yang salah. |
| **Kondisi Awal** | Administrator berada di halaman login admin. |
| **Dependesi** | Koneksi basis data aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan email admin valid namun password salah, lalu klik "Login". | Email: `admin@bansos.com`<br>Password: `salahpassword` | Login gagal, sistem memunculkan pesan error dan tetap berada pada halaman login admin. | Login gagal, sistem menolak login dan memunculkan pesan error "Email atau password salah." | Sukses | Sesuai (Lihat Listing 5.1) |

**Kondisi Akhir:** Session admin tidak dibuat, admin tetap berada pada formulir login admin.

---

#### AUTH-07: Proteksi Halaman Admin (Middleware)

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-07 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji fungsionalitas middleware pengaman halaman admin dari akses langsung tanpa login. |
| **Kondisi Awal** | Pengguna dalam status tidak login (Guest / Non-Login). |
| **Dependesi** | Route middleware `auth:admin` terpasang secara tepat. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Akses langsung URL dashboard admin melalui address bar browser. | URL: `/admin/dashboard` | Sistem memblokir akses langsung ke URL `/admin/*` dan mengalihkan pengunjung ke halaman login admin. | Akses ditolak oleh middleware dan browser dialihkan secara otomatis ke `/admin/login`. | Sukses | Sesuai |

**Kondisi Akhir:** Pengunjung diarahkan ke halaman login admin.

---

#### AUTH-08: Proteksi Halaman Warga (Middleware)

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-08 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji middleware untuk memblokir akses pengunjung non-login ke halaman portal warga. |
| **Kondisi Awal** | Pengguna dalam status tidak login (Guest / Non-Login). |
| **Dependesi** | Route middleware `auth` (web guard) terpasang. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Akses langsung URL dashboard warga melalui address bar browser. | URL: `/user/dashboard` | Sistem memblokir akses langsung ke URL `/user/*` (kecuali login/register) dan mengalihkan pengunjung ke halaman login warga. | Akses ditolak dan browser dialihkan secara otomatis ke halaman login warga (`/login`). | Sukses | Sesuai |

**Kondisi Akhir:** Pengunjung diarahkan ke halaman login warga.

---

#### AUTH-09: Logout Akun Warga

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-09 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji proses keluar sistem (logout) bagi pengguna dengan peran warga. |
| **Kondisi Awal** | Warga dalam status masuk (login) dan berada di Dashboard Warga. |
| **Dependesi** | Session warga aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Keluar" atau "Logout" pada menu navigasi dashboard warga. | Aksi Klik Tombol | Session warga dihapus, warga berhasil keluar dan diarahkan kembali ke halaman utama / login warga. | Sesi warga dihapus sepenuhnya dari database/cookies, dialihkan ke halaman utama. | Sukses | Sesuai |

**Kondisi Akhir:** Session warga hancur, status kembali menjadi tamu (guest).

---

#### AUTH-10: Logout Akun Admin

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AUTH-10 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Autentikasi |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji proses keluar sistem (logout) dan pembersihan session bagi admin secara aman. |
| **Kondisi Awal** | Administrator dalam status masuk (login) dan berada di Dashboard Admin. |
| **Dependesi** | Session admin aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Logout" pada menu profil admin. | Aksi Klik Tombol | Session admin dihapus, admin berhasil keluar dan diarahkan kembali ke halaman login admin. | Sesi admin dibersihkan (`invalidate()` dan `regenerateToken()`), dialihkan ke `/admin/login` dengan pesan "Anda berhasil logout." | Sukses | Sesuai (Lihat Listing 5.12) |

**Kondisi Akhir:** Session admin terhapus dan token CSRF diperbarui untuk mencegah session fixation.

---

### 5.4.2 Modul 2: Manajemen Akun Masyarakat

#### AKUN-01: Menampilkan Daftar Akun Masyarakat

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AKUN-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Akun Masyarakat |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji ketersediaan daftar akun masyarakat terdaftar di sisi admin. |
| **Kondisi Awal** | Admin sudah login dan berada di Dashboard Admin. |
| **Dependesi** | Data akun masyarakat tersimpan di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu navigasi "Kelola Akun Masyarakat". | URL: `/admin/akunmasyarakat` | Sistem menampilkan tabel daftar seluruh akun masyarakat yang terdaftar dengan opsi edit/hapus. | Tabel daftar akun masyarakat ditampilkan dengan kolom NIK, nama, email, dan aksi edit/hapus. | Sukses | Sesuai |

**Kondisi Akhir:** Admin berada di halaman daftar akun masyarakat.

---

#### AKUN-02: Menambahkan Akun Masyarakat Baru

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AKUN-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Akun Masyarakat |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pembuatan akun masyarakat secara manual oleh admin. |
| **Kondisi Awal** | Admin berada di formulir tambah akun masyarakat. |
| **Dependesi** | NIK dan email yang diinput belum terdaftar sebelumnya. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan data warga pada input form lalu klik "Simpan". | NIK: `3273023456789012`<br>Nama: `Budi Budiman`<br>Email: `budi.budiman@mail.com`<br>Password: `password123`<br>Konfirmasi: `password123` | Akun baru berhasil dibuat oleh admin, data masuk ke database, dan masuk dalam daftar warga. | Akun baru tersimpan ke database dengan role `masyarakat`, dialihkan ke daftar akun dengan notifikasi sukses. | Sukses | Sesuai (Lihat Listing 5.2) |

**Kondisi Akhir:** Akun warga bertambah pada tabel `users` dengan enkripsi Bcrypt.

---

#### AKUN-03: Mengubah Data Akun Masyarakat

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AKUN-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Akun Masyarakat |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pengubahan informasi akun masyarakat oleh admin. |
| **Kondisi Awal** | Formulir edit akun warga untuk NIK `3273023456789012` terbuka. |
| **Dependesi** | Akun warga target terdaftar di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Ubah nama warga pada kolom input lalu klik "Update". | Nama baru: `Budi Budiman Sitorus` | Perubahan data (nama, email, status) berhasil disimpan dan diperbarui di tabel daftar akun masyarakat. | Sistem memperbarui baris data di database dan menampilkan nama yang telah diubah ke tabel daftar. | Sukses | Sesuai |

**Kondisi Akhir:** Kolom `name` berubah di basis data.

---

#### AKUN-04: Menghapus Data Akun Masyarakat

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | AKUN-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Akun Masyarakat |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penghapusan akun warga secara permanen. |
| **Kondisi Awal** | Admin berada di halaman daftar akun masyarakat. |
| **Dependesi** | Akun masyarakat yang dipilih tersedia di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Hapus" pada baris Budi Budiman Sitorus, lalu konfirmasi popup penghapusan. | ID User: `5` | Akun masyarakat terpilih berhasil dihapus dari database serta daftar di dashboard admin. | Record akun dengan ID 5 dihapus dari database dan tabel daftar ter-refresh otomatis. | Sukses | Sesuai |

**Kondisi Akhir:** Akun terhapus dari tabel `users`.

---

### 5.4.3 Modul 3: Manajemen Bantuan Sosial

#### BANSOS-01: Menampilkan Daftar Bantuan Sosial

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | BANSOS-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Bantuan Sosial |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji ketersediaan tabel program bansos di sistem. |
| **Kondisi Awal** | Admin berada di Dashboard Admin. |
| **Dependesi** | Data bansos (PKH, BPNT, BLT) tersimpan di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu navigasi "Kelola Bantuan Sosial". | URL: `/admin/bantuansosial` | Menampilkan daftar seluruh program bantuan sosial (contoh: PKH, BPNT, BLT) yang pernah dibuat. | Daftar bansos (PKH, BPNT, BLT) ditampilkan dalam tabel secara rapi beserta detail deskripsinya. | Sukses | Sesuai |

**Kondisi Akhir:** Admin berada di halaman daftar bantuan sosial.

---

#### BANSOS-02: Menambahkan Jenis Bantuan Sosial Baru

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | BANSOS-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Bantuan Sosial |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pembuatan program bansos baru oleh admin. |
| **Kondisi Awal** | Admin berada di halaman form tambah bantuan sosial. |
| **Dependesi** | Koneksi basis data aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan nama bantuan dan deskripsi lalu klik "Simpan". | Nama Bantuan: `Bantuan Lansia 2026`<br>Deskripsi: `Bantuan tunai khusus warga lanjut usia.` | Program bansos baru berhasil ditambahkan beserta kuota dan periode pendaftaran. | Data program bansos berhasil disimpan di database, dialihkan ke indeks dengan pesan sukses. | Sukses | Sesuai (Lihat Listing 5.11) |

**Kondisi Akhir:** Data bansos baru bertambah di tabel `bantuan_sosials`.

---

#### BANSOS-03: Mengubah Data Bantuan Sosial

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | BANSOS-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Bantuan Sosial |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pengeditan informasi program bantuan sosial. |
| **Kondisi Awal** | Formulir edit bansos `Bantuan Lansia 2026` terbuka. |
| **Dependesi** | Program bansos bersangkutan tersedia di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Ubah isi deskripsi bansos lalu klik tombol "Update". | Deskripsi baru: `Bantuan tunai untuk warga lansia di atas 60 tahun.` | Perubahan informasi (nama bansos, kuota, periode, status aktif) berhasil disimpan. | Sistem berhasil menyimpan pembaruan ke database dan menampilkan data terbaru di tabel bansos. | Sukses | Sesuai |

**Kondisi Akhir:** Atribut `deskripsi` ter-update di database.

---

#### BANSOS-04: Menghapus Data Bantuan Sosial

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | BANSOS-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Bantuan Sosial |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penghapusan program bansos dari sistem. |
| **Kondisi Awal** | Admin berada di halaman daftar bantuan sosial. |
| **Dependesi** | Program bansos terpilih tersedia di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Hapus" pada baris program bansos, lalu konfirmasi popup penghapusan. | ID Bansos: `3` | Program bansos terpilih berhasil dihapus dari sistem. | Baris data bansos dihapus dari database dan tabel daftar langsung diperbarui secara otomatis. | Sukses | Sesuai |

**Kondisi Akhir:** Record terhapus dari tabel `bantuan_sosials`.

---

### 5.4.4 Modul 4: Pendaftaran Pengajuan Bansos (Warga)

#### PGB-W-01: Menampilkan Daftar Bansos Aktif

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PGB-W-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Pendaftaran Pengajuan Bansos |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penampilan opsi program bansos aktif yang bisa diajukan oleh warga. |
| **Kondisi Awal** | Warga telah login dan berada di Dashboard Warga. |
| **Dependesi** | Terdapat bantuan sosial terdaftar di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu "Ajukan Bantuan". | URL: `/user/pengajuan/create` | Sistem menampilkan daftar program bansos yang sedang membuka pendaftaran. | Opsi program bantuan sosial aktif muncul dalam formulir pilihan pendaftaran warga. | Sukses | Sesuai |

**Kondisi Akhir:** Warga berada di halaman formulir pengisian data pengajuan.

---

#### PGB-W-02: Mengajukan Bansos (Mengisi Kriteria & Unggah Berkas)

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PGB-W-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Pendaftaran Pengajuan Bansos |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji kesuksesan proses pendaftaran pengajuan bansos lengkap beserta unggahan berkas gambar. |
| **Kondisi Awal** | Warga berada pada halaman formulir pengajuan bansos. |
| **Dependesi** | Warga belum pernah mengajukan bansos yang sama (untuk menghindari duplikasi). |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Isi seluruh formulir pengajuan secara lengkap, unggah file syarat (KTP, KK, SKTM, Foto Rumah) masing-masing berukuran di bawah 2MB, lalu klik "Kirim Pengajuan". | NIK: `3273012345678901`<br>Penghasilan: `1500000`<br>Tanggungan: `3`<br>Berkas: File `.jpg` valid berukuran 1.2MB | Pengajuan bansos berhasil dikirim setelah mengisi seluruh kriteria dan mengunggah berkas syarat. Pengguna diarahkan ke halaman sukses. | Sistem mengunggah file ke `storage/app/public/pengajuan`, menyimpan record pengajuan berstatus 'Menunggu', dan mengalihkan warga ke halaman sukses. | Sukses | Sesuai (Lihat Listing 5.5) |

**Kondisi Akhir:** Data pengajuan terekam di tabel `pengajuans` dengan status default `'Menunggu'`.

---

#### PGB-W-03: Validasi Formulir Pengajuan Kosong/Kurang Lengkap

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PGB-W-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Pendaftaran Pengajuan Bansos |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penolakan sistem dan validasi form ketika ada data wajib yang kosong atau berkas yang belum diunggah. |
| **Kondisi Awal** | Warga berada di formulir pengajuan bansos. |
| **Dependesi** | Validasi kustom form Laravel aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Kosongkan isian Penghasilan dan biarkan berkas SKTM tidak diunggah, kemudian klik "Kirim Pengajuan". | Penghasilan: (kosong)<br>Berkas SKTM: (kosong) | Sistem menolak pengiriman data dan memunculkan pesan validasi pada kolom yang belum terisi/berkas belum diunggah. | Sistem membatalkan penyimpanan, mengembalikan warga ke form pengisian, dan menyorot pesan kesalahan validasi input. | Sukses | Sesuai (Lihat Listing 5.5) |

**Kondisi Akhir:** Data pengajuan tidak bertambah di database.

---

#### PGB-W-04: Menampilkan Riwayat Pengajuan Bansos

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PGB-W-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Pendaftaran Pengajuan Bansos |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji ketersediaan halaman riwayat status berkas pengajuan bagi warga. |
| **Kondisi Awal** | Warga login dan berada di Dashboard Warga. |
| **Dependesi** | Warga bersangkutan memiliki minimal satu riwayat pengajuan. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu navigasi "Riwayat Pengajuan". | URL: `/user/pengajuan/history` | Warga dapat melihat daftar bansos yang telah diajukan beserta status verifikasinya (Diproses, Diverifikasi, Ditolak). | Halaman riwayat menampilkan program bansos yang diajukan beserta status terverifikasi/menunggu saat ini. | Sukses | Sesuai |

**Kondisi Akhir:** Warga berhasil memantau status berkas pengajuannya.

---

### 5.4.5 Modul 5: Manajemen Pengajuan Bansos (Admin)

#### PGB-A-01: Menampilkan Daftar Semua Pengajuan

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PGB-A-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Pengajuan Bansos |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji halaman pemantauan seluruh berkas pengajuan masuk bagi administrator. |
| **Kondisi Awal** | Admin sudah login ke dashboard admin. |
| **Dependesi** | Terdapat record pengajuan dari warga di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu navigasi "Daftar Pengajuan". | URL: `/admin/pengajuan` | Admin dapat melihat seluruh daftar pengajuan dari berbagai warga beserta detail data kriteria dan berkas pendukung. | Tabel daftar seluruh berkas pengajuan warga termuat lengkap dengan berkas pendukung. | Sukses | Sesuai |

**Kondisi Akhir:** Admin berada di halaman daftar berkas pengajuan warga.

---

#### PGB-A-02: Verifikasi Berkas Pengajuan Warga

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PGB-A-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Pengajuan Bansos |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji proses verifikasi berkas administratif warga dari status 'Menunggu' menjadi 'Diverifikasi'. |
| **Kondisi Awal** | Admin membuka detail berkas pengajuan berstatus 'Menunggu' milik warga Ahmad Sodikin. |
| **Dependesi** | Berkas KTP, KK, SKTM, dan foto rumah terlihat jelas secara visual. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol aksi "Verifikasi Berkas" setelah memeriksa keaslian file syarat. | ID Pengajuan: `12` | Admin dapat mengubah status pengajuan menjadi "Diverifikasi" (jika berkas valid) agar siap dinilai dalam perhitungan MOORA. | Status berubah menjadi "Diverifikasi" di database dan sistem memunculkan flash message sukses. | Sukses | Sesuai |

**Kondisi Akhir:** Status pengajuan warga pada tabel `pengajuans` ter-update menjadi `'Diverifikasi'`.

---

#### PGB-A-03: Ekspor Daftar Pengajuan ke Excel

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PGB-A-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Pengajuan Bansos |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pengeksporan rekap data pengajuan warga ke spreadsheet Excel. |
| **Kondisi Awal** | Admin berada di halaman daftar pengajuan. |
| **Dependesi** | Paket library `maatwebsite/excel` aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Ekspor ke Excel". | Aksi Klik Tombol | Sistem berhasil mengunduh file `.xlsx` berisi seluruh data pengajuan bansos yang sesuai filter/pilihan. | File `.xlsx` ter-generate secara otomatis dan proses download dipicu oleh browser. | Sukses | Sesuai |

**Kondisi Akhir:** File Excel berisi daftar pengajuan terunduh di lokal komputer admin.

---

#### PGB-A-04: Ekspor Daftar Pengajuan ke PDF

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PGB-A-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Pengajuan Bansos |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pengeksporan rekap data pengajuan warga ke dokumen cetak PDF. |
| **Kondisi Awal** | Admin berada di halaman daftar pengajuan. |
| **Dependesi** | Library rendering PDF (DomPDF) aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Ekspor ke PDF". | Aksi Klik Tombol | Sistem berhasil menghasilkan dan mengunduh berkas laporan format `.pdf` berisi daftar pengajuan. | Berkas PDF berisi daftar pengajuan berformat cetakan formal berhasil diunduh. | Sukses | Sesuai |

**Kondisi Akhir:** File PDF daftar pengajuan terunduh di lokal komputer admin.

---

### 5.4.6 Modul 6: Manajemen Kriteria & Perhitungan AHP

#### KRI-AHP-01: Menampilkan Daftar Kriteria

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | KRI-AHP-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Kriteria & AHP |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penampilan daftar kriteria penilaian beserta bobot kriteria. |
| **Kondisi Awal** | Admin login dan berada di Dashboard Admin. |
| **Dependesi** | Data kriteria (C1-C4) terdaftar di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu navigasi "Kelola Kriteria". | URL: `/admin/kriteria` | Menampilkan daftar kode kriteria (C1, C2, dst.), nama kriteria, tipe (Benefit/Cost), dan bobot saat ini. | Tabel daftar kriteria (C1-C4) beserta tipe dan bobotnya ditampilkan secara lengkap. | Sukses | Sesuai |

**Kondisi Akhir:** Admin berada di halaman kelola kriteria.

---

#### KRI-AHP-02: Menambahkan Kriteria Baru

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | KRI-AHP-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Kriteria & AHP |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penambahan kriteria baru dan pembentukan kode kriteria otomatis secara berurutan. |
| **Kondisi Awal** | Admin berada di halaman form tambah kriteria. |
| **Dependesi** | Kode kriteria digenerate dinamis berdasarkan hitungan record kriteria (`count() + 1`). |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan nama kriteria baru dan pilih tipe kriteria, lalu klik "Simpan". | Nama: `Kepemilikan Rumah`<br>Tipe: `cost` | Kriteria baru berhasil ditambahkan dan sistem menggenerate kode kriteria berurutan secara otomatis (misal C5 setelah C4). | Kriteria baru tersimpan dengan kode C5, bobot diset awal 0, muncul notifikasi untuk memperbarui perbandingan AHP. | Sukses | Sesuai (Lihat Listing 5.3) |

**Kondisi Akhir:** Record kriteria C5 tersimpan di tabel `kriterias` dengan nilai bobot default 0.

---

#### KRI-AHP-03: Mengubah Nama/Tipe Kriteria

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | KRI-AHP-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Kriteria & AHP |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pembaruan informasi kriteria. |
| **Kondisi Awal** | Formulir edit kriteria C5 terbuka. |
| **Dependesi** | Record kriteria terpilih terdaftar di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Ubah nama kriteria C5 lalu klik "Simpan". | Nama Baru: `Kondisi Rumah` | Perubahan nama kriteria atau jenis tipe (benefit/cost) berhasil disimpan. | Sistem berhasil menyimpan pembaruan nama kriteria dan langsung menayangkannya ke tabel daftar. | Sukses | Sesuai |

**Kondisi Akhir:** Kolom `nama` kriteria C5 berubah di database.

---

#### KRI-AHP-04: Menghapus Kriteria Penilaian

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | KRI-AHP-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Kriteria & AHP |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penghapusan kriteria dan pemulihan urutan kode kriteria (reindexing). |
| **Kondisi Awal** | Admin berada di halaman daftar kriteria. |
| **Dependesi** | Fungsi pemicu penataan kode kriteria aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Hapus" pada kriteria C5, lalu konfirmasi penghapusan. | ID Kriteria: `C5` | Kriteria terhapus dari sistem, dan sistem otomatis melakukan *reindex* kode kriteria (C1, C2, dst.) agar berurutan kembali. | Kriteria C5 dihapus dan sistem secara otomatis mengatur ulang sisa kode kriteria agar tetap berurutan tanpa jeda indeks. | Sukses | Sesuai |

**Kondisi Akhir:** Kriteria C5 terhapus dari tabel database dan kode kriteria tetap rapi terurut.

---

#### KRI-AHP-05: Menginput Nilai Matriks Perbandingan Berpasangan

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | KRI-AHP-05 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Kriteria & AHP |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penyimpanan nilai perbandingan kepentingan relatif antarkriteria (skala Saaty 1-9). |
| **Kondisi Awal** | Halaman Matriks Perbandingan Kriteria terbuka. |
| **Dependesi** | Koneksi basis data aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Pilih skor kepentingan (1-9) antar-kombinasi kriteria pada input dropdown lalu klik "Simpan Perbandingan". | Matriks: C1 vs C2 = 3<br>C1 vs C3 = 2<br>C2 vs C3 = 1/2 | Admin berhasil mengisi dan menyimpan nilai perbandingan berpasangan antarkriteria (skala 1-9). | Sistem merekam seluruh nilai perbandingan berpasangan ke dalam database secara terstruktur. | Sukses | Sesuai |

**Kondisi Akhir:** Nilai perbandingan terekam di tabel `perbandingan_kriteria` untuk kalkulasi AHP.

---

#### KRI-AHP-06: Menghitung AHP dengan Matriks Konsisten (CR <= 0.1)

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | KRI-AHP-06 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Kriteria & AHP |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji kalkulasi AHP dan penyimpanan bobot baru ketika matriks perbandingan bernilai konsisten. |
| **Kondisi Awal** | Nilai matriks perbandingan terisi secara logis dan tidak kontradiktif. |
| **Dependesi** | Kelas `AhpService` terintegrasi dengan baik. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Hitung Bobot AHP". | Aksi Klik Tombol | Perhitungan AHP berhasil dijalankan, menampilkan nilai Consistency Ratio (CR) <= 0.1, dan secara otomatis memperbarui nilai bobot kriteria di database. | Perhitungan sukses menghasilkan CR = `0.024` (CR <= 0.1), status konsisten bernilai `true`. Bobot baru disimpan ke database dengan pesan sukses. | Sukses | Sesuai (Lihat Listing 5.7) |

**Kondisi Akhir:** Kolom `bobot` pada tabel `kriterias` di database ter-update dengan nilai bobot prioritas hasil perhitungan AHP.

---

#### KRI-AHP-07: Menghitung AHP dengan Matriks Tidak Konsisten (CR > 0.1)

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | KRI-AHP-07 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Kriteria & AHP |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penolakan sistem terhadap penyimpanan bobot baru jika nilai rasio konsistensi tidak logis (CR > 0.10). |
| **Kondisi Awal** | Nilai perbandingan diisi secara acak/kontradiktif. |
| **Dependesi** | Kelas `AhpService` terintegrasi. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Hitung Bobot AHP". | Matriks diisi acak: C1 > C2, C2 > C3, C3 > C1 secara kontradiktif. | Perhitungan menampilkan pesan error bahwa matriks tidak konsisten (CR > 0.1). Bobot baru tidak disimpan, dan admin diminta memperbaiki nilai perbandingan. | Perhitungan menghasilkan CR = `0.235` (CR > 0.1). Sistem menolak menyimpan bobot baru, menampilkan pesan error "Perbandingan Tidak Konsisten! CR = 0.2350...". | Sukses | Sesuai (Lihat Listing 5.7) |

**Kondisi Akhir:** Nilai bobot kriteria pada tabel `kriterias` tetap menggunakan nilai bobot lama di database.

---

### 5.4.7 Modul 7: Manajemen Sub Kriteria

#### SUB-KRI-01: Menampilkan Daftar Sub-Kriteria per Kriteria

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | SUB-KRI-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Sub Kriteria |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penampilan daftar sub-kriteria bersadarkan filter kriteria induk yang dipilih. |
| **Kondisi Awal** | Admin membuka menu Kelola Sub Kriteria. |
| **Dependesi** | Relasi kunci asing `kriteria_id` terpasang di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Pilih kriteria "Penghasilan" pada dropdown filter kriteria. | ID Kriteria: `1` (Penghasilan) | Menampilkan daftar sub-kriteria beserta range nilai atau indikator deskriptif dan nilai skor bobotnya untuk kriteria yang dipilih. | Tabel menampilkan sub-kriteria Penghasilan: `< 1 jt` (skor 5), `1-2 jt` (skor 4), dst. | Sukses | Sesuai |

**Kondisi Akhir:** Admin melihat daftar sub-kriteria dari kriteria Penghasilan.

---

#### SUB-KRI-02: Menambahkan Sub-Kriteria Baru

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | SUB-KRI-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Sub Kriteria |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pembuatan item sub-kriteria baru di bawah kriteria utama. |
| **Kondisi Awal** | Form tambah sub-kriteria terbuka. |
| **Dependesi** | Relasi database valid. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Isi kriteria induk, nama sub-kriteria, dan nilai bobot skor, lalu klik "Simpan". | Kriteria: `Penghasilan` (ID: 1)<br>Nama Sub: `> 5 juta`<br>Nilai: `1` | Sub-kriteria beserta skor nilainya berhasil ditambahkan di bawah kriteria induknya. | Data tersimpan ke database, dialihkan ke indeks dengan pesan sukses "Sub Kriteria berhasil ditambahkan." | Sukses | Sesuai (Lihat Listing 5.4) |

**Kondisi Akhir:** Record sub-kriteria baru bertambah di tabel `sub_kriterias`.

---

#### SUB-KRI-03: Mengubah Data Sub-Kriteria

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | SUB-KRI-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Sub Kriteria |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pembaruan informasi sub-kriteria. |
| **Kondisi Awal** | Form edit sub-kriteria target terbuka. |
| **Dependesi** | Record sub-kriteria terdaftar di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Ubah nilai skor sub-kriteria lalu klik "Simpan". | Nilai skor baru: `0.5` | Perubahan nama/parameter sub-kriteria beserta nilai skornya berhasil diperbarui. | Data tersimpan ke database dan nilai skor sub-kriteria yang baru ditampilkan di tabel. | Sukses | Sesuai |

**Kondisi Akhir:** Kolom `nilai` ter-update di database.

---

#### SUB-KRI-04: Menghapus Sub-Kriteria

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | SUB-KRI-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Sub Kriteria |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penghapusan item sub-kriteria. |
| **Kondisi Awal** | Admin berada di halaman daftar sub-kriteria. |
| **Dependesi** | Record sub-kriteria terpilih tersedia di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Hapus" pada baris sub-kriteria, lalu konfirmasi penghapusan. | ID Sub-kriteria: `9` | Sub-kriteria terpilih berhasil dihapus dari daftar penilaian. | Data dihapus dari database dan tabel daftar ter-refresh secara otomatis. | Sukses | Sesuai |

**Kondisi Akhir:** Record sub-kriteria terhapus dari tabel `sub_kriterias`.

---

### 5.4.8 Modul 8: Penilaian Pengajuan & Perhitungan MOORA

#### NIL-MOORA-01: Menampilkan Pengajuan Siap Nilai

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | NIL-MOORA-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Penilaian & MOORA |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penampilan berkas pendaftaran terverifikasi yang siap diberikan input nilai kriteria. |
| **Kondisi Awal** | Admin membuka menu Penilaian Alternatif. |
| **Dependesi** | Terdapat pengajuan berstatus 'Diverifikasi' di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu navigasi "Penilaian Alternatif". | URL: `/admin/penilaian` | Menampilkan daftar pengajuan warga berstatus "Diverifikasi" yang belum diinput nilai kriteria penilaiannya. | Tabel memuat seluruh pengajuan warga dengan status berkas 'Diverifikasi' yang siap dinilai. | Sukses | Sesuai |

**Kondisi Akhir:** Admin berada di halaman daftar siap nilai.

---

#### NIL-MOORA-02: Menginput Nilai Penilaian Kriteria Warga

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | NIL-MOORA-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Penilaian & MOORA |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penyimpanan input nilai sub-kriteria (penilaian alternatif) untuk data warga. |
| **Kondisi Awal** | Formulir input penilaian warga Ahmad Sodikin terbuka. |
| **Dependesi** | Pengajuan berstatus diverifikasi. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Pilih parameter sub-kriteria untuk seluruh kriteria penilaian, lalu klik "Simpan Penilaian". | ID Pengajuan: `12`<br>Input: C1=4 (1-2 Juta), C2=5 (Tanggungan 3), dst. | Admin berhasil memilih sub-kriteria (nilai alternatif) untuk pengajuan warga dan menyimpannya. | Sistem mengeksekusi `updateOrCreate()` untuk mencatat penilaian, dialihkan ke indeks dengan pesan "Penilaian berhasil disimpan." | Sukses | Sesuai (Lihat Listing 5.6) |

**Kondisi Akhir:** Record penilaian tersimpan di database pada tabel `penilaians`.

---

#### NIL-MOORA-03: Mengubah Nilai Penilaian Kriteria Warga

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | NIL-MOORA-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Penilaian & MOORA |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pembaruan (edit) penilaian kriteria warga tanpa menduplikasi baris baru. |
| **Kondisi Awal** | Formulir input penilaian warga dibuka kembali. |
| **Dependesi** | Record penilaian kriteria warga target sudah ada di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Ubah pilihan sub-kriteria pada salah satu kriteria lalu klik "Simpan Penilaian". | Mengubah C1 Penghasilan menjadi skor 3 (2-3 Juta). | Admin berhasil memperbarui pilihan nilai sub-kriteria pada pengajuan warga yang sudah pernah dinilai. | Sistem mendeteksi data lama, melakukan pembaruan (update) kolom nilai di database secara langsung tanpa menduplikasi data. | Sukses | Sesuai (Lihat Listing 5.6) |

**Kondisi Akhir:** Nilai sub-kriteria ter-update di tabel `penilaians`.

---

#### NIL-MOORA-04: Menjalankan Perhitungan MOORA

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | NIL-MOORA-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Penilaian & MOORA |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji eksekusi algoritma MOORA (normalisasi, perkalian bobot AHP, kalkulasi nilai Yi, dan pemeringkatan). |
| **Kondisi Awal** | Seluruh warga berstatus 'Diverifikasi' telah diinput penilaian kriterianya. |
| **Dependesi** | Kelas `MooraService` terintegrasi. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Hitung MOORA". | Aksi Klik Tombol (memicu `hitungMoora` di controller). | Sistem melakukan normalisasi matriks keputusan, mengalikan dengan bobot kriteria AHP, menghitung nilai optimasi (Yi), melakukan pemeringkatan otomatis, dan menyimpannya ke session/database hasil. | Sistem menghitung matriks ternormalisasi, mengalikan bobot AHP, memperoleh nilai Yi alternatif, mengurutkan peringkat, menyimpan peringkat ke database, dan menampilkan payload hasil. | Sukses | Sesuai (Lihat Listing 5.8) |

**Kondisi Akhir:** Pemeringkatan terekam permanen pada tabel `hasil_akhirs` di database.

---

### 5.4.9 Modul 9: Laporan Hasil Akhir & Rangking

#### HASIL-01: Menampilkan Rangking Hasil Akhir MOORA

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | HASIL-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Laporan Hasil & Rangking |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji visualisasi rangking pemenang kelayakan bansos terurut berdasarkan nilai Yi tertinggi. |
| **Kondisi Awal** | Perhitungan MOORA telah selesai dilakukan dan data tersimpan di database. |
| **Dependesi** | Tabel `hasil_akhirs` terhubung dengan kueri Eloquent secara aman. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu navigasi "Hasil Akhir & Rangking". | URL: `/admin/hasilakhir` | Sistem menampilkan tabel rangking penerima bansos terurut dari nilai Yi tertinggi, lengkap dengan nama warga, NIK, jenis bansos, nilai akhir Yi, dan keterangan kelayakan. | Tabel rangking terurut (ranking 1, 2, 3...) dimuat secara berurutan dengan data NIK, Nama, Yi, dan Status Kelayakan (Layak/Tidak Layak). | Sukses | Sesuai (Lihat Listing 5.9) |

**Kondisi Akhir:** Tampilan hasil kelayakan termuat secara visual.

---

#### HASIL-02: Ekspor Laporan Pemeringkatan ke Excel

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | HASIL-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Laporan Hasil & Rangking |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji ekspor hasil akhir pemeringkatan MOORA ke format Microsoft Excel. |
| **Kondisi Awal** | Admin berada di halaman hasil akhir. |
| **Dependesi** | Pustaka pengekspor Excel dikonfigurasi. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Ekspor Hasil ke Excel". | Aksi Klik Tombol | Laporan rangking hasil akhir MOORA berhasil diekspor ke file Excel `.xlsx`. | Browser memicu unduhan berkas spreadsheet `.xlsx` berisi daftar pemeringkatan kelayakan. | Sukses | Sesuai |

**Kondisi Akhir:** File Excel laporan rangking tersimpan di komputer admin.

---

#### HASIL-03: Ekspor Laporan Pemeringkatan ke PDF

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | HASIL-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Laporan Hasil & Rangking |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji ekspor hasil akhir pemeringkatan MOORA ke berkas PDF. |
| **Kondisi Awal** | Admin berada di halaman hasil akhir. |
| **Dependesi** | Library rendering PDF (DomPDF) dikonfigurasi. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik tombol "Ekspor Hasil ke PDF". | Aksi Klik Tombol | Laporan rangking hasil akhir MOORA berhasil diekspor ke berkas `.pdf`. | Dokumen biner PDF berisi rangking kelayakan ter-generate dan terunduh otomatis. | Sukses | Sesuai |

**Kondisi Akhir:** File PDF laporan rangking tersimpan di komputer admin.

---

#### HASIL-04: Menampilkan Pengumuman Hasil Kelayakan di Sisi Warga

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | HASIL-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Laporan Hasil & Rangking |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji akses dan kesesuaian pengumuman kelayakan di sisi akun warga yang bersangkutan. |
| **Kondisi Awal** | Warga Ahmad Sodikin login ke portal warga. |
| **Dependesi** | Hasil perhitungan MOORA warga bersangkutan terekam di database. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu navigasi "Hasil Kelayakan". | URL: `/user/hasil-kelayakan` | Halaman Hasil Akhir pada akun warga bersangkutan menampilkan status apakah yang bersangkutan layak menerima bansos berdasarkan batas kelayakan sistem. | Sistem menampilkan halaman pengumuman personal status kelayakan (misal: "Dinyatakan LAYAK menerima PKH"). | Sukses | Sesuai |

**Kondisi Akhir:** Warga berhasil melihat status kelayakannya secara transparan.

---

### 5.4.10 Modul 10: Manajemen Profil Warga

#### PROFIL-01: Menampilkan Detail Profil Warga

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PROFIL-01 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Profil Warga |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji ketersediaan halaman profil warga beserta kelengkapan data diri. |
| **Kondisi Awal** | Warga sudah login ke sistem. |
| **Dependesi** | Sesi warga aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Klik menu navigasi "Profil Saya". | URL: `/user/profile` | Sistem menampilkan nama, NIK, alamat, email, dan detail akun warga saat ini. | Halaman menampilkan data NIK, Nama, Email, Alamat, dan Telepon milik warga yang login. | Sukses | Sesuai |

**Kondisi Akhir:** Warga berada di halaman detail profil.

---

#### PROFIL-02: Memperbarui Informasi Profil

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PROFIL-02 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Sedang |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Profil Warga |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji pengeditan informasi profil oleh warga. |
| **Kondisi Awal** | Halaman profil warga terbuka. |
| **Dependesi** | Koneksi basis data aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Ubah data alamat dan nomor telepon lalu klik "Simpan Perubahan". | Alamat: `Jl. Merdeka No. 45, Bandung`<br>Telepon: `081234567890` | Warga berhasil mengedit data profil seperti alamat dan nomor telepon. Perubahan langsung tersimpan di database. | Perubahan data tersimpan ke database, halaman di-refresh dengan pesan sukses pembaruan profil. | Sukses | Sesuai |

**Kondisi Akhir:** Informasi kontak warga ter-update di tabel database.

---

#### PROFIL-03: Mengubah Password dengan Password Lama yang Benar

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PROFIL-03 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Profil Warga |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji kesuksesan ubah password warga dengan memverifikasi password lama yang benar. |
| **Kondisi Awal** | Form ubah password warga terbuka. |
| **Dependesi** | Pustaka hashing password (Bcrypt) Laravel aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan password lama yang benar, lalu ketik password baru beserta konfirmasinya, kemudian klik "Ubah Password". | Password Lama: `password123`<br>Password Baru: `rahasiabaru123`<br>Konfirmasi: `rahasiabaru123` | Password berhasil diubah setelah sistem memverifikasi bahwa password lama yang diinput adalah benar. | Sistem memvalidasi password lama, menyandikan password baru dengan hash, dan memicu notifikasi sukses ubah password. | Sukses | Sesuai |

**Kondisi Akhir:** Password terenkripsi baru terekam di database tabel `users`.

---

#### PROFIL-04: Mengubah Password dengan Password Lama yang Salah

| **Atribut Uji** | **Detail Pengujian** | **Atribut Uji** | **Detail Pengujian** |
| :--- | :--- | :--- | :--- |
| **Nama Projek** | Sistem Informasi Manajemen Bantuan Sosial (Bansos-App) | **ID Kasus Uji** | PROFIL-04 |
| **Perancangan Uji** | Muhammad Hidayat | **Prioritas Uji** | Tinggi |
| **Tanggal Rancangan** | 4 Juli 2026 | **Nama Modul** | Manajemen Profil Warga |
| **Pelaksanaan Uji** | Muhammad Hidayat | **Tanggal Uji** | 4 Juli 2026 |
| **Deskripsi Uji** | Menguji penolakan sistem saat mengubah password warga menggunakan password lama yang salah. |
| **Kondisi Awal** | Form ubah password warga terbuka. |
| **Dependesi** | Validasi input Laravel aktif. |

**Tabel Skenario Langkah Pengujian:**

| No | Langkah Uji | Data Uji | Hasil Yang Diharapkan | Hasil Yang Terjadi | Status | Keterangan |
| :-: | :--- | :--- | :--- | :--- | :-: | :--- |
| 1 | Masukkan password lama yang salah, lalu ketik password baru, kemudian klik "Ubah Password". | Password Lama: `passSalah12`<br>Password Baru: `rahasiabaru123`<br>Konfirmasi: `rahasiabaru123` | Sistem memblokir perubahan password dan memunculkan pesan error "Password lama tidak sesuai". | Perubahan dibatalkan oleh validasi sistem, password tidak berubah, dan muncul pesan error "Password lama tidak sesuai". | Sukses | Sesuai |

**Kondisi Akhir:** Password warga di database tidak berubah (tetap password lama).
