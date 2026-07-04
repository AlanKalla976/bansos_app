<?php

namespace App\Exports;

use App\Models\Pengajuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengajuanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $bantuanSosialId;

    public function __construct($bantuanSosialId = null)
    {
        $this->bantuanSosialId = $bantuanSosialId;
    }

    public function collection()
    {
        $query = Pengajuan::with(['bantuanSosial'])->latest();

        if ($this->bantuanSosialId) {
            $query->where('bantuan_sosial_id', $this->bantuanSosialId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pengajuan',
            'Nama',
            'NIK',
            'Jenis Bantuan',
            'Alamat',
            'No. Telepon',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Pendidikan',
            'Pekerjaan',
            'Penghasilan',
            'Jumlah Tanggungan',
            'Kepemilikan Rumah',
            'Status',
            'Alasan Penolakan',
        ];
    }

    public function map($pengajuan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            optional($pengajuan->created_at)->format('d-m-Y'),
            $pengajuan->nama,
            $pengajuan->nik,
            $pengajuan->bantuanSosial->nama_bantuan ?? '-',
            $pengajuan->alamat,
            $pengajuan->no_telepon,
            $pengajuan->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            optional($pengajuan->tanggal_lahir ? \Carbon\Carbon::parse($pengajuan->tanggal_lahir) : null)->format('d-m-Y'),
            $pengajuan->pendidikan,
            $pengajuan->pekerjaan,
            $pengajuan->penghasilan,
            $pengajuan->jumlah_tanggungan,
            $pengajuan->kepemilikan_rumah,
            $pengajuan->status,
            $pengajuan->alasan_penolakan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A5F'],
            ]],
        ];
    }
}