@extends('admin.layouts.app')

@section('title', 'Detail Pengajuan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-person me-2"></i>Detail Pengajuan</h4>
    </div>

    <div class="row g-4">
        {{-- Info Pemohon --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold text-primary bg-white border-bottom">
                    <i class="bi bi-person me-1"></i> Data Pemohon
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="180">Nama</th><td>{{ $pengajuan->nama }}</td></tr>
                        <tr><th>NIK</th><td>{{ $pengajuan->nik }}</td></tr>
                        <tr><th>No. Telepon</th><td>{{ $pengajuan->no_telepon }}</td></tr>
                        <tr><th>Jenis Kelamin</th><td>{{ $pengajuan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                        <tr><th>Tanggal Lahir</th><td>{{ $pengajuan->tanggal_lahir?->format('d/m/Y') }}</td></tr>
                        <tr><th>Pendidikan</th><td>{{ $pengajuan->pendidikan }}</td></tr>
                        <tr><th>Alamat</th><td>{{ $pengajuan->alamat }}</td></tr>
                        <tr><th>Bantuan Sosial</th><td>{{ $pengajuan->bantuanSosial->nama_bantuan ?? '-' }}</td></tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $badge = ['Menunggu'=>'warning','Diverifikasi'=>'info','Ditolak'=>'danger','Diterima'=>'success'][$pengajuan->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badge }} fs-6">{{ $pengajuan->status }}</span>
                            </td>
                        </tr>
                        @if($pengajuan->status === 'Ditolak' && $pengajuan->alasan_penolakan)
                        <tr>
                            <th class="text-danger">Alasan Penolakan</th>
                            <td class="text-danger">{{ $pengajuan->alasan_penolakan }}</td>
                        </tr>
                        @endif
                        <tr><th>Tgl. Pengajuan</th><td>{{ $pengajuan->created_at->format('d/m/Y H:i') }}</td></tr>
                    </table>
                </div>
            </div>

            {{-- Data Ekonomi --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold text-primary bg-white border-bottom">
                    <i class="bi bi-cash-stack me-1"></i> Data Ekonomi
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="180">Penghasilan</th>
                            <td>
                                @if($pengajuan->penghasilan !== null)
                                    {{-- ✅ Format: Rp 1.500.000 tanpa desimal --}}
                                    Rp {{ number_format($pengajuan->penghasilan, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah Tanggungan</th>
                            <td>{{ $pengajuan->jumlah_tanggungan ?? '-' }} orang</td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td>{{ $pengajuan->pekerjaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kepemilikan Rumah</th>
                            <td>{{ $pengajuan->kepemilikan_rumah ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Dokumen --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold text-primary bg-white border-bottom">
                    <i class="bi bi-folder me-1"></i> Dokumen
                </div>
                <div class="card-body">
                    @foreach(['foto_ktp' => 'KTP', 'foto_kk' => 'KK', 'foto_sktm' => 'SKTM', 'foto_rumah' => 'Foto Rumah'] as $field => $label)
                    <div class="mb-3">
                        <p class="fw-semibold mb-1">{{ $label }}</p>
                        @if($pengajuan->$field)
                            <a href="{{ asset('storage/' . $pengajuan->$field) }}" target="_blank">
                                <img src="{{ asset('storage/' . $pengajuan->$field) }}"
                                     class="img-thumbnail w-100" style="max-height:150px; object-fit:cover;">
                            </a>
                        @else
                            <p class="text-muted">-</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('admin.pengajuan.edit', $pengajuan) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form action="{{ route('admin.pengajuan.destroy', $pengajuan) }}" method="POST"
              onsubmit="return confirm('Yakin hapus pengajuan ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger"><i class="bi bi-trash me-1"></i> Hapus</button>
        </form>
    </div>
</div>
@endsection