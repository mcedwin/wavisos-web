<?php

namespace App\Controllers;

use App\Libraries\WhatsAppService;

class WhatsApp extends BaseController
{
  protected $wa;
  public function __construct()
  {
    $this->wa = new WhatsAppService();
  }
  public function enviar()
  {

    // Prueba con número de destino y texto
    $telefono = '51965889389'; // sin "+" ni espacios
    $mensaje = 'Hola desde CodeIgniter 👋';

    $resultado = $this->wa->enviar($telefono, $mensaje);

    return $this->response->setJSON($resultado);
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
  public function webhook()
  {
    $j = $this->request->getJSON(true);
    
    if (isset($j['entry'][0]['changes'][0]['value']['statuses'][0])) {
      return $this->response->setStatusCode(200);
    }

    file_put_contents("json2.txt", json_encode($j) . "\r\n\r\n\r\n", FILE_APPEND | LOCK_EX);
    $j = $j['entry'][0]['changes'][0]['value']['messages'][0];


    $tipoMensaje = $j['type'];

    $texto = '';
    if ($tipoMensaje === 'text') {
      $texto = $j['text']['body'];
    } elseif ($tipoMensaje === 'button') {
      $texto = $j['button']['text'];
    } elseif ($tipoMensaje === 'interactive') {
      $texto = $j['interactive']['button_reply']['title'] ?? '';
    }

    $tex = trim(mb_strtolower($texto));

    $this->wa->webhook([
      'from' => $j['from'],
      'body' => $tex
    ]);
    return $this->response->setStatusCode(200);
  }
}
