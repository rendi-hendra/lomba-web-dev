<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetRespon extends Model
{
    protected $table = 'target_respon';
    protected $primaryKey = 'id_target';

    protected $fillable = [
        'id_penilaian',
        'batas_respon_menit',
        'tenggat_respon',
        'waktu_respon',
        'status_respon',
    ];

    protected $casts = [
        'tenggat_respon' => 'datetime',
        'waktu_respon' => 'datetime',
    ];

    public function penilaianPrioritas()
    {
        return $this->belongsTo(PenilaianPrioritas::class, 'id_penilaian', 'id_penilaian');
    }
}
