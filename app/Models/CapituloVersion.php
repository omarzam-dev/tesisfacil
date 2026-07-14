<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapituloVersion extends Model
{
    protected $table = 'capitulo_versiones';

    protected $fillable = ['capitulo_id', 'numero_version', 'nombre_archivo', 'ruta_archivo', 'comentario'];

    public function capitulo(): BelongsTo
    {
        return $this->belongsTo(Capitulo::class);
    }
}
