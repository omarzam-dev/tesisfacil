<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevisionApa extends Model
{
    use HasFactory;

    protected $table = 'revisiones_apa';

    protected $fillable = [
        'user_id',
        'nombre_archivo',
        'ruta_archivo',
        'edicion_apa',
        'estado',
        'resultado_estructura',
        'error_mensaje',
    ];

    protected $casts = [
        'resultado_estructura' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
