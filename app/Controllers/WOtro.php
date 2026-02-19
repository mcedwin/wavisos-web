<?php

namespace App\Controllers;

use App\Libraries\WOAppService;

class WOtro extends BaseController
{
  protected $wa;
  public function __construct()
  {
    $this->wa = new WOAppService();
  }

  public function prueba()
  {
    /* $this->wa->webhook([
      'from' => '51951939876',
      'body' => 'luz'
    ]);
    return $this->response->setStatusCode(200);*/
    $this->wa->prueba();
  }
  public function sendMessage()
  {
    $number = '51965889389'; // Número destino en formato internacional
    $message = 'Hola desde CodeIgniter 4 🚀';

    $url = 'http://64.23.236.95:3000/send-message'; // Cambia TU_IP por la IP de tu servidor Node

    $data = [
      'number' => $number,
      'message' => $message
    ];

    $options = [
      'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
      ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
      return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo conectar a la API']);
    }

    return $this->response->setJSON(json_decode($result, true));
  }
  public function webhook()
  {
    $json = $this->request->getJSON(true); // true = array asociativo

    log_message('info', '📥 Webhook recibido: ' . json_encode($json));

    // Aquí procesas el mensaje
    if (isset($json['body'])) {
      file_put_contents("llegada.txt", json_encode($json) . "\r\n\r\n", FILE_APPEND | LOCK_EX);
      // if (strtolower($json['body']) === 'hola') {
      //     // responder, guardar en BD, etc.
      // }
      $from = $json['from'] ?? '';
      $mensaje = $json['body'] ?? '';
      log_message('info', "WhatsApp 1: " . $from.$mensaje);  
      // Verificar si es grupo
      if (strpos($from, '@g.us') !== false) {
        // Es grupo → no respondemos
        error_log("📢 Mensaje de grupo ignorado: $from");
        exit();
      }

      //$from = explode("@", $from);
      //$from = $from[0];
      // if($from!='51965889389')exit();
log_message('info', "WhatsApp 2: " . $from.$mensaje);  
      $this->wa->webhook([
        'from' => $from,
        'body' => $mensaje
      ]);
      return $this->response->setStatusCode(200);
    }

    return $this->response->setJSON(['status' => 'ok']);
  }
}
