<?php

namespace App\Exports;

use App\Models\HasilAkhir;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HasilAkhirExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Hitung status Layak/Tidak Layak berdasarkan kuota per jenis bantuan.
     */
    private function attachStatusByKuota($items)
    {
        $grouped = $items->groupBy(function ($h) {
            return $h->pengajuan->bantuanSosial->id ?? 'unknown';
        });

        foreach ($grouped as $bantuanId => $group) {
            $kuota = optional($group->first()->pengajuan->bantuanSosial)->kuota ?? 0;

            $sorted = $group->sortBy(function ($h) {
                return $h->ranking;
            })->values();

            foreach ($sorted as $index => $h) {
                $h->status_computed = ($index < $kuota) ? 'Layak' : 'Tidak Layak';
            }
        }

        return $items;
    }

    public function collection()
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

        $items = $query->get();
        $items = $this->attachStatusByKuota($items);

        if (!empty($this->filters['status'])) {
            $items = $items->filter(function ($h) {
                return $h->status_computed === $this->filters['status'];
            })->values();
        }

        return $items;
    }

    public function headings(): array
    {
        return [
            'Ranking',
            'Nama',
            'NIK',
            'Jenis Bantuan',
            'Total Skor',
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
            $row->status_computed,
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