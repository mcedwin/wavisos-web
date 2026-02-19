<?php

namespace App\Libraries;

class IntencionDetector
{
    // Diccionario de intenciones con frases o patrones asociados
    protected $intenciones = [
        'luz' => [
            'debo luz', 'deuda luz', 'recibo luz',
            'cuánto debo.*luz', 'consultar luz',
            'sigo debiendo.*luz', 'tengo deuda.*luz', 'luz debo'
        ],
        'agua' => [
            'debo agua', 'deuda agua', 'recibo agua',
            'cuánto debo.*agua', 'consultar agua',
            'tengo deuda.*agua', 'agua debo'
        ],
        'reporte_basura' => [
            'basura', 'montón de basura', 'tiraron basura', 'reportar basura'
        ],
        'servicios' => [
            'gasfitero', 'cocina', 'electricista', 'ayudante', 'albañil', 'servicio técnico'
        ],
        'saludo' => [
            'hola', 'buenos dias', 'buenas tardes', 'buenas noches'
        ]
    ];

    /**
     * Dada una oración, detecta qué intención tiene
     */
    public function detectar(string $texto): ?string
    {
        $texto = strtolower(trim($texto));

        foreach ($this->intenciones as $intencion => $frases) {
            foreach ($frases as $frase) {
                if (@preg_match('/' . $frase . '/i', $texto)) {
                    return $intencion;
                }
            }
        }

        return null; // No detectado
    }
}
