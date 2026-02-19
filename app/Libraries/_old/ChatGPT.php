<?php

namespace App\Libraries;

use GuzzleHttp\Client;

class ChatGPT
{
  protected $apiKey;
  protected $client;

  public function __construct()
  {
    $this->apiKey = getenv('OPENAI_API_KEY');
    $this->client = new Client([
      'base_uri' => 'https://api.openai.com/v1/',
      'timeout'  => 20.0
    ]);
  }

  public function preguntar($mensaje)
  {
    try {
      $response = $this->client->post('chat/completions', [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->apiKey,
          'Content-Type'  => 'application/json',
        ],
        'json' => [
          'model' => 'gpt-3.5-turbo',
          'messages' => [
            ['role' => 'system', 'content' => 'Responde como asistente municipal para una ciudad pequeña.'],
            ['role' => 'user', 'content' => $mensaje],
          ]
        ]
      ]);

      $json = json_decode($response->getBody(), true);
      return $json['choices'][0]['message']['content'] ?? 'Lo siento, no tengo una respuesta.';
    } catch (\Exception $e) {
      return 'Error al conectarse con ChatGPT: ' . $e->getMessage();
    }
  }

  public function conversar($historial)
  {
    try {
      $response = $this->client->post('chat/completions', [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->apiKey,
          'Content-Type'  => 'application/json',
        ],
        'json' => [
          'model' => 'gpt-3.5-turbo',
          'messages' => $historial
        ]
      ]);

      $json = json_decode($response->getBody(), true);
      return $json['choices'][0]['message']['content'] ?? 'No tengo respuesta.';
    } catch (\Exception $e) {
      return 'Error: ' . $e->getMessage();
    }
  }
}
