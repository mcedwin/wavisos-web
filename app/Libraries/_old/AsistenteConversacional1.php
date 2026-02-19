<?php

namespace App\Libraries;

use App\Models\UsuariosModel;
use App\Models\IntencionesModel;
use App\Models\AnunciosModel;
use App\Models\ReportesModel;
use App\Libraries\GPT;

class AsistenteConversacional
{
    protected $usuarios;
    protected $intenciones;
    protected $anuncios;
    protected $reportes;
    protected $gpt;

    protected $requisitos = [
        'luz' => ['codigo'],
        'agua' => ['codigo'],
        'gas' => ['codigo'],
        'licencia' => ['apellido', 'dni', 'mes'],
        'anuncio' => ['contenido'],
        'reporte' => ['descripcion', 'foto']
    ];

    public function __construct()
    {
        $this->usuarios = new UsuariosModel();
        $this->intenciones = new IntencionesModel();
        $this->anuncios = new AnunciosModel();
        $this->reportes = new ReportesModel();
        $this->gpt = new GPT();
    }

    public function gestionarFlujo(string $numero, string $texto)
    {
        $intencion = $this->detectarIntencion($texto);
        $datos = $this->extraerDatos($texto);

        $pendiente = $this->intenciones->obtenerPendiente($numero);

        if ($intencion && $pendiente && $intencion !== $pendiente['intencion']) {
            $this->intenciones->cancelarPendiente($numero);
            $pendiente = null;
        }

        if ($pendiente) {
            $pendiente = $this->intenciones->agregarDatos($pendiente, $datos);

            if ($this->tieneTodo($pendiente['intencion'], $pendiente['datos'])) {
                $respuesta = $this->procesarIntencion($pendiente, $numero);
                $this->intenciones->marcarCompleto($pendiente['id']);
                return $respuesta;
            } else {
                $faltantes = $this->obtenerDatosFaltantes($pendiente['intencion'], $pendiente['datos']);
                return $this->gpt->generarPreguntaFaltante($pendiente['intencion'], $faltantes);
            }
        } elseif ($intencion) {
            if ($this->tieneTodo($intencion, $datos)) {
                $temp = ['intencion' => $intencion, 'datos' => $datos];
                return $this->procesarIntencion($temp, $numero);
            } else {
                $this->intenciones->crear($numero, $intencion, $datos);
                $faltantes = $this->obtenerDatosFaltantes($intencion, $datos);
                return $this->gpt->generarPreguntaFaltante($intencion, $faltantes);
            }
        } else {
            // Detectar saludos o despedidas simples
            if (preg_match('/(hola|buenos d[ií]as|buenas tardes|buenas noches|ad[ií]os|hasta luego|chao)/i', $texto)) {
                return $this->gpt->responder("$texto");
            }
            return $this->gpt->responderLibre($texto);
        }
    }

    protected function detectarIntencion(string $texto): ?string
    {
        if (strpos($texto, 'luz') !== false) return 'luz';
        if (strpos($texto, 'agua') !== false) return 'agua';
        if (strpos($texto, 'gas') !== false) return 'gas';
        if (strpos($texto, 'licencia') !== false) return 'licencia';
        if (strpos($texto, 'publicar') !== false || strpos($texto, 'anuncio') !== false) return 'anuncio';
        if (strpos($texto, 'reportar') !== false || strpos($texto, 'basura') !== false || strpos($texto, 'problema') !== false) return 'reporte';
        return null;
    }

    protected function extraerDatos(string $texto): array
    {
        $datos = [];

        if (preg_match('/\b\d{5,10}\b/', $texto, $matches)) {
            $datos['codigo'] = $matches[0];
        }

        if (preg_match('/apellido\s+([a-zA-ZáéíóúÁÉÍÓÚñÑ]+)/i', $texto, $matches)) {
            $datos['apellido'] = $matches[1];
        }

        if (preg_match('/dni\s*(\d{8})/i', $texto, $matches)) {
            $datos['dni'] = $matches[1];
        }

        if (preg_match('/enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre/i', $texto, $matches)) {
            $datos['mes'] = strtolower($matches[0]);
        }

        if (preg_match('/(anuncio|publicar):?\s*(.{50,255})/i', $texto, $matches)) {
            $datos['contenido'] = trim($matches[2]);
        }

        if (preg_match('/(reporte|problema|basura):?\s*(.*)/i', $texto, $matches)) {
            $datos['descripcion'] = trim($matches[2]);
        }

        return $datos;
    }

    protected function tieneTodo(string $intencion, array $datos): bool
    {
        if (!isset($this->requisitos[$intencion])) return false;
        foreach ($this->requisitos[$intencion] as $campo) {
            if (!isset($datos[$campo])) return false;
        }
        return true;
    }

    protected function obtenerDatosFaltantes(string $intencion, array $datos): array
    {
        $faltantes = [];
        if (!isset($this->requisitos[$intencion])) return $faltantes;
        foreach ($this->requisitos[$intencion] as $campo) {
            if (!isset($datos[$campo])) $faltantes[] = $campo;
        }
        return $faltantes;
    }

    protected function procesarIntencion(array $pendiente, string $numero): string
    {
        $datos = $pendiente['datos'];

        switch ($pendiente['intencion']) {
            case 'luz':
                $deuda = $this->consultarDeudaLuz($datos['codigo']);
                return $this->gpt->responder("El usuario quiere saber su deuda de luz con el código {$datos['codigo']}. El resultado es: S/ $deuda");

            case 'licencia':
                return $this->gpt->responder("El usuario quiere consultar una licencia con apellido {$datos['apellido']}, DNI {$datos['dni']} y mes {$datos['mes']}.");

            case 'anuncio':
                $this->anuncios->insert([
                    'numero' => $numero,
                    'contenido' => $datos['contenido'],
                    'fecha' => date('Y-m-d H:i:s')
                ]);
                return $this->gpt->responder("Tu anuncio ha sido recibido: '{$datos['contenido']}'. Será publicado en breve.");

            case 'reporte':
                $this->reportes->insert([
                    'numero' => $numero,
                    'descripcion' => $datos['descripcion'],
                    'foto' => $datos['foto'] ?? null,
                    'fecha' => date('Y-m-d H:i:s')
                ]);
                return $this->gpt->responder("Gracias por reportar: '{$datos['descripcion']}'. Vamos a dar seguimiento.");

            default:
                return "No se pudo procesar tu solicitud.";
        }
    }

    protected function consultarDeudaLuz(string $codigo): string
    {
        return "18.50";
    }
}
