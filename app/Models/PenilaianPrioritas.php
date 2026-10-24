<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianPrioritas extends Model
{
    protected $table = 'penilaian_prioritas';
    protected $primaryKey = 'id_penilaian';

    protected $fillable = [
        'id_aduan',
        'nilai_urgensi',
        'nilai_dampak',
        'nilai_jumlah_warga',
        'bobot_urgensi',
        'bobot_dampak',
        'bobot_jumlah_warga',
        'skor_prioritas',
        'tingkat_prioritas',
        'waktu_penilaian',
    ];

    protected $casts = [
        'waktu_penilaian' => 'datetime',
        'nilai_urgensi' => 'decimal:2',
        'nilai_dampak' => 'decimal:2',
        'nilai_jumlah_warga' => 'decimal:2',
        'bobot_urgensi' => 'decimal:4',
        'bobot_dampak' => 'decimal:4',
        'bobot_jumlah_warga' => 'decimal:4',
        'skor_prioritas' => 'decimal:3',
    ];

    public function aduan()
    {
        return $this->belongsTo(Aduan::class, 'id_aduan', 'id_aduan');
    }

    public function targetRespon()
    {
        return $this->hasOne(TargetRespon::class, 'id_penilaian', 'id_penilaian');
    }
}
