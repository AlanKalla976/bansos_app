<?php

namespace App\Exports;

use App\Models\HasilAkhir;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HasilAkhirExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = HasilAkhir::with(['pengajuan.bantuanSosial'])
            ->orderBy('ranking');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas('pengajuan', fn($q) =>
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',  'like', "%{$search}%")
            );
        }

        if (!empty($this->filters['jenis_bantuan'])) {
            $query->whereHas('pengajuan.bantuanSosial', fn($q) =>
                $q->where('id', $this->filters['jenis_bantuan'])
            );
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Ranking',
            'Nama',
            'NIK',
            'Jenis Bantuan',
            'Nilai Yi',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->ranking,
            $row->pengajuan->nama                        ?? '-',
            $row->pengajuan->nik                         ?? '-',
            $row->pengajuan->bantuanSosial->nama_bantuan ?? '-',
            number_format($row->nilai_yi, 8),
            $row->status,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E3A5F']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}