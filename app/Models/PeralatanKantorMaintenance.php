<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeralatanKantorMaintenance extends Model
{
    protected $fillable = [
        'maintenance_date',
        'description',
        'cost',
        'vendor',
        'next_maintenance_date',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_date' => 'date',
            'next_maintenance_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function asset()
    {
        return $this->belongsTo(PeralatanKantor::class, 'peralatan_kantor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
