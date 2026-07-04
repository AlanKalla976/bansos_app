<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Pengajuan Bantuan Sosial</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; }
        h2 { text-align: center; margin-bottom: 2px; color: #1E3A5F; }
        p.subtitle { text-align: center; margin-top: 0; color: #555; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #1E3A5F; color: #fff; font-size: 10px; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 10px; text-align: right; color: #555; }
    </style>
</head>
<body>
    <h2>Laporan Data Pengajuan Bantuan Sosial</h2>
    <p class="subtitle">
        @if($bantuan)
            Jenis Bantuan: {{ $bantuan->nama_bantuan }}
        @else
            Seluruh Jenis Bantuan
        @endif
        — Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
    </p>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Jenis Bantuan</th>
                <th>Alamat</th>
                <th>No. Telp</th>
                <th>JK</th>
                <th>Pekerjaan</th>
                <th>Penghasilan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuans as $i => $p)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ optional($p->created_at)->format('d-m-Y') }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->nik }}</td>
                <td>{{ $p->bantuanSosial->nama_bantuan ?? '-' }}</td>
                <td>{{ $p->alamat }}</td>
                <td>{{ $p->no_telepon }}</td>
                <td class="text-center">{{ $p->jenis_kelamin }}</td>
                <td>{{ $p->pekerjaan }}</td>
                <td>Rp {{ number_format($p->penghasilan ?? 0, 0, ',', '.') }}</td>
                <td>{{ $p->status }}</td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center">Tidak ada data pengajuan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Total Data: {{ $pengajuans->count() }} pengajuan</p>
</body>
</html>