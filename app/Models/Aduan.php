<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aduan extends Model
{
    protected $table = 'aduan';
    protected $primaryKey = 'id_aduan';

    protected $fillable = [
        'kode_tiket',
        'judul_aduan',
        'deskripsi_aduan',
        'latitude',
        'longitude',
        'alamat',
        'nohp',
        'status_aduan',
        'tanggapan',
        'petugas_penanggung_jawab',
        'waktu_pengaduan',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'waktu_pengaduan' => 'datetime',
    ];

    public function penilaianPrioritas()
    {
        return $this->hasOne(PenilaianPrioritas::class, 'id_aduan', 'id_aduan');
    }

    public function laporan()
    {
        return $this->hasOne(Laporan::class, 'id_aduan', 'id_aduan');
    }
}
