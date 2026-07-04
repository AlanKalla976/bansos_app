<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Hasil Akhir Kelayakan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { font-size: 15px; font-weight: 800; color: #1e3a5f; }
        .header p  { font-size: 10px; color: #64748b; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th {
            background: #1e3a5f;
            color: #fff;
            padding: 7px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        .layak     { color: #065f46; font-weight: 700; }
        .tdk-layak { color: #991b1b; font-weight: 700; }
        .rank      { text-align: center; font-weight: 800; }
        .footer    { margin-top: 24px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Hasil Akhir Kelayakan Bantuan Sosial</h2>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:60px;text-align:center;">Ranking</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Jenis Bantuan</th>
                <th style="width:130px;">Nilai Yi</th>
                <th style="width:90px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hasilAkhirs as $h)
            <tr>
                <td class="rank">{{ $h->ranking }}</td>
                <td>{{ $h->pengajuan->nama ?? '-' }}</td>
                <td>{{ $h->pengajuan->nik  ?? '-' }}</td>
                <td>{{ $h->pengajuan->bantuanSosial->nama_bantuan ?? '-' }}</td>
                <td style="font-family:monospace;">{{ number_format($h->nilai_yi, 8) }}</td>
                <td class="{{ $h->status === 'Layak' ? 'layak' : 'tdk-layak' }}">
                    {{ $h->status }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">
                    Tidak ada data.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total: {{ $hasilAkhirs->count() }} data &nbsp;|&nbsp;
        Layak: {{ $hasilAkhirs->where('status','Layak')->count() }} &nbsp;|&nbsp;
        Tidak Layak: {{ $hasilAkhirs->where('status','Tidak Layak')->count() }}
    </div>
</body>
</html>