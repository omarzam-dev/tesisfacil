<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tesis extends Model
{
    protected $table = 'tesis';

    protected $fillable = ['user_id', 'titulo', 'linea_investigacion', 'estado'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function capitulos(): HasMany
    {
        return $this->hasMany(Capitulo::class)->orderBy('orden');
    }

    public function progreso(): int
    {
        $total = $this->capitulos()->count();

        if ($total === 0) {
            return 0;
        }

        $aprobados = $this->capitulos()->where('estado', 'aprobado')->count();

        return (int) round(($aprobados / $total) * 100);
    }
}
