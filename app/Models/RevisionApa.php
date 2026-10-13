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
        'perfil_formato_id',
        'perfil_nombre',
        'estado',
        'resultado_estructura',
        'resultado_citas',
        'resultado_referencias',
        'error_mensaje',
    ];

    protected $casts = [
        'resultado_estructura' => 'array',
        'resultado_citas' => 'array',
        'resultado_referencias' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function perfilFormato(): BelongsTo
    {
        return $this->belongsTo(PerfilFormato::class);
    }

    /** "APA 7ma edición", "ISO 690 numérico"... (edicion_apa guarda el código de la norma) */
    public function normaNombre(): string
    {
        return config("apa_rules.{$this->edicion_apa}.nombre", $this->edicion_apa);
    }

    public function esIso(): bool
    {
        return config("apa_rules.{$this->edicion_apa}.tipo") === 'iso';
    }
}
