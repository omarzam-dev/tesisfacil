<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Capitulo extends Model
{
    protected $table = 'capitulos';

    protected $fillable = ['tesis_id', 'nombre', 'orden', 'estado'];

    public function tesis(): BelongsTo
    {
        return $this->belongsTo(Tesis::class);
    }

    public function versiones(): HasMany
    {
        return $this->hasMany(CapituloVersion::class)->orderByDesc('numero_version');
    }

    public function ultimaVersion(): ?CapituloVersion
    {
        return $this->versiones()->first();
    }
}
