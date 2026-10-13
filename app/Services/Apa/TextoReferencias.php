<?php

namespace App\Services\Apa;

/**
 * Utilidades compartidas por los validadores ISO 690:
 * ubicar la sección de referencias y comparar apellidos sin tildes ni mayúsculas.
 */
class TextoReferencias
{
    // "Referencias", "Referencias bibliográficas", "Bibliografía", "Fuentes de información"
    // con o sin numeración delante ("VI. REFERENCIAS BIBLIOGRÁFICAS").
    public const ENCABEZADO = '/^\s*(?:(?:cap[ií]tulo\s+)?[IVXLC\d]+\s*[\.\)\-:]?\s*)?(referencias(?:\s+bibliogr[aá]ficas)?|bibliograf[ií]a|fuentes\s+de\s+informaci[oó]n|lista\s+de\s+referencias)\s*:?\s*$/iu';

    // Lo que viene después de las referencias y no debe tomarse como referencia
    public const FIN_REFERENCIAS = '/^\s*(?:[IVXLC\d]+\s*[\.\)\-:]?\s*)?(anexos?|ap[eé]ndices?)\b/iu';

    /**
     * Índice del encabezado de referencias. Se toma el ÚLTIMO que aparezca,
     * para no confundirse con el índice/tabla de contenidos.
     */
    public static function indiceEncabezado(array $parrafos): ?int
    {
        $indice = null;

        foreach ($parrafos as $i => $texto) {
            if (preg_match(self::ENCABEZADO, $texto)) {
                $indice = $i;
            }
        }

        return $indice;
    }

    /** Párrafos del cuerpo (antes de las referencias). */
    public static function cuerpo(array $parrafos): array
    {
        $i = self::indiceEncabezado($parrafos);

        return $i === null ? $parrafos : array_slice($parrafos, 0, $i);
    }

    /** Entradas de la lista de referencias (se detiene en Anexos/Apéndices). */
    public static function entradas(array $parrafos): array
    {
        $i = self::indiceEncabezado($parrafos);

        if ($i === null) {
            return [];
        }

        $entradas = [];

        foreach (array_slice($parrafos, $i + 1) as $p) {
            if (preg_match(self::FIN_REFERENCIAS, $p)) {
                break;
            }

            if (mb_strlen(trim($p)) > 15) {
                $entradas[] = trim($p);
            }
        }

        return $entradas;
    }

    /** "Pérez" / "PÉREZ" / "perez" => "perez" */
    public static function normalizar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto), 'UTF-8');

        return strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'ñ' => 'n', 'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
        ]);
    }

    /** Quita la numeración inicial de una entrada: "[3] PÉREZ..." => "PÉREZ..." */
    public static function sinNumero(string $entrada): string
    {
        return preg_replace('/^\s*[\[\(]?\d{1,3}[\]\)\.]?\s+/u', '', $entrada);
    }

    /** Número escrito al inicio de la entrada: "[3] ..." / "3. ..." / "(3) ..." */
    public static function numeroInicial(string $entrada): ?int
    {
        return preg_match('/^\s*(?:\[(\d{1,3})\]|\((\d{1,3})\)|(\d{1,3})[\.\)])\s+/u', $entrada, $m)
            ? (int) (($m[1] ?? '') ?: (($m[2] ?? '') ?: ($m[3] ?? '')))
            : null;
    }

    /** Primer autor (texto antes de la primera coma), sin numeración. */
    public static function primerAutor(string $entrada): string
    {
        $limpia = self::sinNumero($entrada);

        return trim(explode(',', $limpia)[0] ?? $limpia);
    }

    public static function recortar(string $texto, int $largo = 70): string
    {
        return mb_strlen($texto) > $largo ? mb_substr($texto, 0, $largo) . '...' : $texto;
    }
}
