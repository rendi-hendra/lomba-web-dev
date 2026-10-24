<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_aduan',
        'id_penilaian',
        'id_target',
    ];

    public function aduan()
    {
        return $this->belongsTo(Aduan::class, 'id_aduan', 'id_aduan');
    }

    public function penilaianPrioritas()
    {
        return $this->belongsTo(PenilaianPrioritas::class, 'id_penilaian', 'id_penilaian');
    }

    public function targetRespon()
    {
        return $this->belongsTo(TargetRespon::class, 'id_target', 'id_target');
    }
}
