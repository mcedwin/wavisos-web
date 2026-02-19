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
    $pendiente = $this->intenciones->obtenerPendiente($numero);
    $intencion = $this->detectarIntencion($texto) ?? ($pendiente['intencion'] ?? null);
    $datos = $intencion ? $this->extraerDatosConGPT($intencion, $texto) : [];

    $datos = $intencion ? $this->extraerDatosConGPT($intencion, $texto) : [];

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
      if (preg_match('/\b(hola|buenos d[ií]as|buenas tardes|buenas noches|ad[ií]os|hasta luego|chao)\b/i', $texto)) {
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

  protected function extraerDatosConGPT(string $intencion, string $texto): array
  {
    $campos = implode(', ', $this->requisitos[$intencion]);
    $prompt = "Extrae los siguientes datos para la intención '{$intencion}': {$campos}. Texto del usuario: \"$texto\". Devuélvelo en JSON válido y sin explicaciones.";

    $respuesta = $this->gpt->responder($prompt);
    file_put_contents("respuesta.txt", $respuesta);
    $json = json_decode($respuesta, true);

    return is_array($json) ? $json : [];
  }

  protected function tieneTodo(string $intencion, array $datos): bool
  {
    if (!isset($this->requisitos[$intencion])) return false;

    foreach ($this->requisitos[$intencion] as $campo) {
      if (empty($datos[$campo])) return false;

      // Validación especial para anuncios
      if ($intencion === 'anuncio' && $campo === 'contenido' && strlen(trim($datos['contenido'])) < 50) {
        return false;
      }
    }

    return true;
  }

  protected function obtenerDatosFaltantes(string $intencion, array $datos): array
  {
    $faltantes = [];
    if (!isset($this->requisitos[$intencion])) return $faltantes;
    foreach ($this->requisitos[$intencion] as $campo) {
      if (empty($datos[$campo])) $faltantes[] = $campo;
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
