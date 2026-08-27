<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilAkhir extends Model
{
    protected $table      = 'hasil_akhirs';
    protected $primaryKey = 'hasil_id';

    protected $fillable = [
        'pengajuan_id',
        'nilai_yi',
        'ranking',
        'status',
        // Persetujuan Lurah
        'persetujuan_status',
        'alasan_penolakan_lurah',
        'persetujuan_oleh',
        'persetujuan_at',
    ];

    protected $casts = [
        'nilai_yi'        => 'float',
        'persetujuan_at'  => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
    }

    /**
     * Lurah yang memberikan persetujuan / penolakan.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'persetujuan_oleh', 'users_id');
    }

    /**
     * Data Penyaluran terkait hasil akhir ini.
     */
    public function penyaluran()
    {
        return $this->hasOne(Penyaluran::class, 'hasil_id', 'hasil_id');
    }

    // ── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Apakah keputusan Lurah sudah diambil (bukan Menunggu)?
     */
    public function sudahDiproses(): bool
    {
        return $this->persetujuan_status !== 'Menunggu Persetujuan';
    }

    /**
     * Apakah calon penerima ini sudah disetujui Lurah dan boleh masuk penyaluran?
     */
    public function bisaDisalurkan(): bool
    {
        return $this->persetujuan_status === 'Disetujui';
    }
}