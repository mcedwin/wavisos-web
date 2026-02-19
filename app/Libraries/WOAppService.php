<?php

namespace App\Libraries;

use App\Models\UsuariosModel;
use App\Models\IntencionesModel;
use App\Models\DirectorioModel;
use App\Models\ReportesModel;

class WOAppService
{
  protected $users, $ints, $anuncios, $reportes;

  private $opciones;

  public function __construct()
  {
    $this->users = new UsuariosModel();
    $this->ints = new IntencionesModel();
    $this->anuncios = new DirectorioModel();
    $this->reportes = new ReportesModel();


    $this->opciones = [
      ['cod' => '1', 'sid' => '1️⃣ 🔍', 'opcion' => 'Buscar en directorio', 'intencion' => 'profesional', 'campos' => ['contenido'], 'ejemplo' => '📂 Directorio de Anuncios
Genial 🙌, dime una palabra clave para buscar en nuestro directorio.
👉 Por ejemplo: gasfitero, electricista, clases, alquiler, venta, transporte.

Te mostraré los 5 primeros resultados que coincidan con tu búsqueda, junto con el número de contacto 📱 de cada anuncio.'],
      ['cod' => '2', 'sid' => '2️⃣ 📢', 'opcion' => 'Publicar en directorio', 'intencion' => 'anuncio', 'campos' => ['contenido'], 'ejemplo' => '📝 Publicar Anuncio
Perfecto 🙌, envíame el texto de tu anuncio en un solo párrafo.
👉 Recuerda incluir información clara como:

Qué ofreces o buscas
Tu número de contacto 📱
Algún detalle importante (ej: ubicación, disponibilidad, precio si aplica)

Cuando lo envíes, lo revisaré y lo publicaré para que otros puedan verlo 🔎.'],
      ['cod' => '5', 'sid' => '5️⃣ 🌤️', 'opcion' => 'Consultar el clima', 'intencion' => 'clima', 'campos' => ['contenido'], 'ejemplo' => ''],
      ['cod' => '6', 'sid' => '6️⃣ 📌', 'opcion' => 'Suscribirse a noticias y eventos', 'intencion' => 'suscribirse', 'campos' => ['contenido'], 'ejemplo' => ''],
      ['cod' => '7', 'sid' => '7️⃣ 🚫', 'opcion' => 'Desuscribirme', 'intencion' => 'desuscribirse', 'campos' => ['contenido'], 'ejemplo' => ''],
    ];
  }
  
  public function prueba()
  {
    $this->enviarWhatsApp('51951939876', $this->menuPrincipal('Hola'));
  }

  public function webhook($json)
  {
    $num = $json['from'];
    $texto = trim($json['body']);

    if (!$this->users->where('numero', $num)->first()) {
      $this->users->save(['numero' => $num]);
      $this->enviarWhatsApp($num, $this->menuPrincipal('¡👋 ¡Bienvenido! Soy tu asistente por WhatsApp.\nPuedo ayudarte a:'));
      return;
    }
    $pend = $this->ints->obtenerPendiente($num);
    $newInt = $this->detectarIntencion($texto);
    // log_message('info', "texto: " . $texto);
    $essaludo = preg_match('/\b(hola|adios|chao)\b/i', $texto);

    if (($pend && $newInt && $newInt != $pend['intencion']) || ($pend && $essaludo)) {
      $this->ints->cancelarPendiente($num);
      $pend = null;
    }

    if ($pend) {
      $pend = $this->ints->agregarDatos($pend, $this->extraerDatos($pend['intencion'], $texto));
      if ($this->tieneTodo($pend['intencion'], $pend['datos'])) {
        $resp = $this->procesar($pend, $num);
        $this->ints->marcarCompleto($pend['id']);
        $this->enviar($num, $resp);
      } else {
        $this->enviar($num, $this->menuFaltantes($pend['intencion'], $pend['datos']));
      }
    } elseif ($newInt) {
      $datos = $this->extraerDatos($newInt, $texto);
      if ($this->tieneTodo($newInt, $datos)) {
        $resp = $this->procesar(['intencion' => $newInt, 'datos' => $datos], $num);
        $this->enviar($num, $resp);
      } else {
        $this->ints->crear($num, $newInt, $datos);
        $this->enviar($num, $this->menuFaltantes($newInt, $datos));
      }
    } else {
      if (preg_match('/\b(hola|adios|chao)\b/i', $texto)) {
        $this->enviarWhatsApp($num, $this->menuPrincipal("✨ ¡Hola! Gracias por comunicarte\nEstoy aquí para ayudarte con lo que necesites.\n"));
      } else {
        // $this->enviarWhatsApp($num, $this->menuPrincipal("Lo siento, no entendí. "));
        $resp = $this->buscar($texto);
        $this->enviarWhatsApp($num, $resp);
      }
    }
  }

  protected function enviarWhatsApp($num, $mensaje)
  {
    $url = 'http://64.23.254.99:3000/send-message'; // Cambia TU_IP por la IP de tu servidor Node

    $data = [
      'to' => $num,
      'message' => $mensaje
    ];

    // log_message('info', "WhatsApp send3: " . json_encode($data));

    $options =  [
      'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
      ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
      //return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo conectar a la API']);
    }

  }

  public function enviar($telefono, $mensaje)
  {
    $this->enviarWhatsApp($telefono, $mensaje);
  }

  protected function menuPrincipal($texto)
  {
    $options = $texto . "\n";
    foreach ($this->opciones as $index => $item) {
      $options .= $item['sid'] . " " . $item['opcion'] . "\n";
    }
    return $options;
  }

  protected function detectarIntencion($t)
  {
    $m = strtolower($t);
    log_message('info', "inte: " . $m);
    foreach ($this->opciones as $opcion) {
      log_message('info', "mi texto: " . $opcion['opcion'] . " - " . stripos($m, $opcion['opcion']));
      if (trim($m) == $opcion['cod']) return $opcion['intencion'];
      if (stripos($m, $opcion['intencion']) !== false) return $opcion['intencion'];
    }
    return null;
  }

  protected function extraerDatos($int, $t)
  {
    $d = [];
    if ($int == 'profesional' && strlen(str_ireplace("Busco Profesional", "", $t)) > 5) $d['contenido'] = $t;
    if ($int == 'directorio' && strlen(str_ireplace("Consultar Directorio", "", $t)) > 5) $d['contenido'] = $t;
    if ($int == 'anuncio' && strlen($t) > 40) $d['contenido'] = $t;
    if ($int == 'clima') $d['contenido'] = '.';
    if ($int == 'suscribirse') $d['contenido'] = '.';
    if ($int == 'desuscribirse') $d['contenido'] = '.';

    return $d;
  }

  protected function tieneTodo($int, $d)
  {
    $r = [];
    foreach ($this->opciones as $item) {
      $r[$item['intencion']] = $item['campos'];
    }

    foreach ($r[$int] as $f) {
      if (empty($d[$f])) return false;
    }
    return true;
  }

  protected function menuFaltantes($int, $d)
  {
    $r = [];
    $ejemplo = '';
    foreach ($this->opciones as $item) {
      $r[$item['intencion']] = $item['campos'];
      if ($int == $item['intencion']) $ejemplo = $item['ejemplo'];
    }

    $r = array_diff($r[$int], $d ? array_keys($d) : []);
    if (count($r)) return $ejemplo;
    return "Falta datos: " . implode(', ', $r);
  }

  public function enviarAGPT(string $apiKey, array $mensajes): ?string
  {
    $url = "https://api.openai.com/v1/chat/completions";

    $data = [
      'model' => 'gpt-3.5-turbo',
      'messages' => $mensajes,
      'temperature' => 0.7
    ];

    $headers = [
      'Content-Type: application/json',
      'Authorization: ' . 'Bearer ' . $apiKey
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
      log_message('error', 'Error en cURL: ' . curl_error($ch));
      curl_close($ch);
      return null;
    }

    curl_close($ch);

    $json = json_decode($result, true);

    return $json['choices'][0]['message']['content'] ?? null;
  }

  function getcate($text)
  {
    $conversacion = [
      ['role' => 'system', 'content' => 'Eres un asistente que clasifica mini anuncios en categorías y pone un titulo pequeño sugerente. Tu tarea es devolver solo la categoría la más adecuada para el anuncio dado, Si no encaja, clasifícalo como "Otro".
Devuelve en formato JSON el titulo sugerente y un valor numerico de la categoria.
Lista de categorías:
1. Inmuebles
2. Vehículos
3. Tecnología
4. Empleo
5. Servicios
6. Compra y Venta
7. Varios']
    ];

    $prompt = $text;


    // Agregar mensaje actual del usuario
    $conversacion[] = ['role' => 'user', 'content' => $prompt];


    $apiKey = getenv('OPENAI_API_KEY'); // ponlo en .env
    $response = $this->enviarAGPT($apiKey, $conversacion);


    // file_put_contents("migpt.txt", $response);
    return json_decode($response);
  }

  public function buscar($text)
  {
    $keyword = $text; // palabra que consulta el usuario
    // Buscar coincidencias en título o descripción
    $resultados = $this->anuncios
      ->like('titulo', $keyword)
      ->orLike('contenido', $keyword)
      ->orderBy('id', 'DESC')
      ->limit(5)
      ->find();

    if (empty($resultados)) {
      return "No encontré anuncios para *{$keyword}* 😔";
    }

    // Construir mensaje en texto para WhatsApp
    $mensaje = "🔎 Resultados para *{$keyword}*:\n\n";

    foreach ($resultados as $i => $row) {
      $num = $i + 1;
      $mensaje .= "{$num}. *{$row['titulo']}*\n";
      $mensaje .= "📞 {$row['numero']}\n";
      $mensaje .= "{$row['contenido']}\n\n";
    }

    $mensaje .= "👉 Estos son los primeros " . count($resultados) . " anuncios encontrados.";

    return $mensaje;
  }

  protected function procesar($p, $num)
  {
    $int = $p['intencion'];
    $d = $p['datos'];

    if ($int == 'profesional') {
      $resp = $this->buscar($d['contenido']);
      return 'Encontrados: ' . $resp;
    }
    if ($int == 'directorio') {
      $resp = $this->buscar($d['contenido']);
      return 'Encontrados: ' . $resp;
    }
    if ($int == 'anuncio') {
      $cates = $this->getcate($d['contenido']);

      $this->anuncios->insert(['numero' => $num, 'titulo' => $cates->titulo_sugerente, 'contenido' => $d['contenido'], 'tipo' => 'temporal', 'idCate' => $cates->categoria, 'fechareg' => date('Y-m-d H:i:s'), 'fechapub' => date('Y-m-d H:i:s'), 'fechafin' => date('Y-m-d H:i:s', time() + 24 * 3 * 60 * 60)]);
      $id = $this->anuncios->getInsertID();
      return "📢 ¡Tu anuncio ha sido publicado con éxito!
🔗 Puedes verlo en la web: https://WAVISOS.com/item-" . $id . "
💬 Además, también puede ser consultado directamente en este chat de WhatsApp";
    }
    if ($int == 'clima') {
      return $this->getclima() . "\n\n" . $this->maniana();
    }
    if ($int == 'suscribirse') {
      $this->users->update(['numero' => $num], ['suscrito' => '1']);
      return '🎉 ¡Te has suscrito con éxito!
A partir de ahora recibirás 👉 noticias, eventos y avisos de interés directamente aquí en WhatsApp 📲.
Si en algún momento deseas dejar de recibirlos, solo selecciona la opción 🚫 Desuscribirme en el menú.';
    }
    if ($int == 'desuscribirse') {
      $this->users->update(['numero' => $num], ['suscrito' => '0']);
      return '🚫 Te has dado de baja de las suscripciones.
❌ Ya no recibirás noticias ni alertas de eventos en tu WhatsApp.

Si en el futuro deseas volver a recibir información, solo escribe:
📰 Suscribirme';
    }

    return "Listo.";
  }


  public function maniana($city = "Puno")
  {
    $apiKey = "743c44144e9233175b5499fcbdaec77f";
    $url = "https://api.openweathermap.org/data/2.5/forecast?q={$city}&appid={$apiKey}&units=metric&lang=es";

    $client = \Config\Services::curlrequest();

    try {
      $response = $client->get($url);
      $data = json_decode($response->getBody(), true);

      if (!isset($data['list'])) {
        return "No se pudo obtener el pronóstico para {$city}.";
      }

      $tomorrowDate = date("Y-m-d", strtotime("+1 day"));

      $temps = [];
      $descriptions = [];

      foreach ($data['list'] as $item) {
        $dateTime = $item['dt_txt'];
        $dateOnly = substr($dateTime, 0, 10);

        if ($dateOnly === $tomorrowDate) {
          $temps[] = $item['main']['temp'];
          $descriptions[] = $item['weather'][0]['description'];
        }
      }

      if (empty($temps)) {
        return "No hay datos disponibles para {$city} el {$tomorrowDate}.";
      }

      $min = min($temps);
      $max = max($temps);
      // Tomar la descripción más común
      $desc = array_count_values($descriptions);
      arsort($desc);
      $mainDesc = array_key_first($desc);

      $mensaje = "📅 *Pronóstico para mañana ({$tomorrowDate})*:\n";
      $mensaje .= "🌡️ Mín: {$min}°C | Máx: {$max}°C\n";
      $mensaje .= "📝 Condición general: {$mainDesc}";

      return $mensaje;
    } catch (\Exception $e) {
      return "Error: " . $e->getMessage();
    }
  }


  public function getclima($city = "Puno")
  {
    $apiKey = "743c44144e9233175b5499fcbdaec77f";
    $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric&lang=es";

    // Cliente HTTP de CodeIgniter
    $client = \Config\Services::curlrequest();

    try {
      $response = $client->get($url);
      $data = json_decode($response->getBody(), true);

      if (isset($data['main'])) {
        return "🌤️ *Clima en Puno* 
🌡️ Temp. actual: " . $data['main']['temp'] . " °C
🤔 Sensación: " . $data['main']['feels_like'] . " °C
💧Humedad: " . $data['main']['humidity'] . "%
☁️ Condición: " . $data['weather'][0]['description'];
      } else {
        return [
          "error" => "No se pudo obtener el clima para {$city}"
        ];
      }
    } catch (\Exception $e) {
      return [
        "error" => $e->getMessage()
      ];
    }
  }
}
