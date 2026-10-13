<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsoIa extends Model
{
    protected $table = 'usos_ia';

    protected $fillable = ['user_id', 'tipo', 'modelo', 'tokens_entrada', 'tokens_salida', 'costo_usd'];

    protected $casts = [
        'tokens_entrada' => 'integer',
        'tokens_salida'  => 'integer',
        'costo_usd'      => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Usos del mes calendario actual. */
    public function scopeEsteMes($query)
    {
        return $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
    }
}
