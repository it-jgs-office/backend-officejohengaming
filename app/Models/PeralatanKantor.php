<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PeralatanKantor extends Model
{
    protected $table = 'peralatan_kantor';

    protected $fillable = [
        'kode_aset',
        'barcode',
        'foto',
        'nama_barang',
        'jumlah',
        'detail',
        'sub_kategori',
        'tim',
        'keterangan',
        'lokasi_unit',
        'ruangan',
        'milik',
        'pengadaan_tahun',
        'tanggal_pembelian',
        'kategori_nilai',
        'kategori_ukuran',
        'nilai',
        'waktu_pakai_per_hari',
        'estimasi_waktu_barang',
        'pengurangan_harga_per_hari',
        'harga_per_hari_ini',
        'pic',
        'jabatan',
        'atasan',
        'jabatan_atasan',
        'kondisi',
        'barcode_ditempel',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'pengadaan_tahun' => 'integer',
            'tanggal_pembelian' => 'date',
            'nilai' => 'decimal:2',
            'waktu_pakai_per_hari' => 'integer',
            'estimasi_waktu_barang' => 'integer',
            'pengurangan_harga_per_hari' => 'decimal:2',
            'harga_per_hari_ini' => 'decimal:2',
            'barcode_ditempel' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (PeralatanKantor $model) {
            if (empty($model->kode_aset)) {
                $model->kode_aset = self::generateKodeAset();
            }
            if (empty($model->barcode)) {
                $model->barcode = $model->kode_aset;
            }
        });
    }

    public function maintenances()
    {
        return $this->hasMany(PeralatanKantorMaintenance::class)->orderByDesc('maintenance_date')->orderByDesc('id');
    }

    public function scopeOfTim($query, ?string $tim)
    {
        if (! $tim) {
            return $query;
        }

        $teamIds = Team::where('name', $tim)->pluck('id');

        $hasKoordinator = User::where('role', 'koordinator')
            ->whereIn('team_id', $teamIds)
            ->exists();

        if (! $hasKoordinator) {
            return $query->where('tim', $tim);
        }

        $memberNames = User::whereIn('team_id', $teamIds)
            ->get(['name', 'username'])
            ->flatMap(fn ($user) => [$user->name, $user->username])
            ->map(fn ($name) => mb_strtolower(trim((string) $name)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $query->where(function ($q) use ($tim, $memberNames) {
            $q->where('tim', $tim);

            if ($memberNames !== []) {
                $q->orWhereIn(DB::raw('LOWER(TRIM(pic))'), $memberNames);
            }
        });
    }

    public static function generateKodeAset(): string
    {
        $year = date('Y');
        $prefix = 'PK-'.$year.'-';

        $last = self::where('kode_aset', 'like', $prefix.'%')
            ->orderByDesc('kode_aset')
            ->value('kode_aset');

        if ($last) {
            $seq = (int) substr($last, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix.str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
