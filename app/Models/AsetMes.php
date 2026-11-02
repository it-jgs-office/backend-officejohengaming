<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsetMes extends Model
{
    public const PENANGGUNG_JAWAB_MES = [
        'putra' => 'Ridwan Hasan Maulana',
        'putri' => 'Yuliana Sventy Yasmine Aulhia Sugiat',
        'koordinator' => 'Koordinator',
        'johen_streamer' => 'Johen Streamer',
    ];

    public const KATEGORI_LABELS = [
        'putra' => 'Mes Putra',
        'putri' => 'Mes Putri',
        'koordinator' => 'MES Koordinator',
        'johen_streamer' => 'MES Johen Streamer',
    ];

    protected $table = 'aset_mes';

    protected $fillable = [
        'nama_aset',
        'kategori',
        'jumlah',
        'penanggung_jawab',
        'pic',
        'jabatan',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab');
    }

    public function pembayaran()
    {
        return $this->hasOne(PembayaranAsetMes::class, 'aset_mes_id');
    }
}
