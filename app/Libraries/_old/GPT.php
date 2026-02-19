<?php
namespace App\Libraries;

class GPT
{
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiKey = getenv('OPENAI_API_KEY'); // Asegúrate de definirlo en tu .env
        $this->model = 'gpt-3.5-turbo'; // O 'gpt-3.5-turbo' si prefieres
    }

    public function responder(string $prompt): string
    {
        $response = $this->requestChat([
            ['role' => 'user', 'content' => $prompt]
        ]);
        file_put_contents('respuestassss.txt', date("Y-M-d H:i:s").' - '.$response." - ".$prompt."\r\n", FILE_APPEND);
        return $response ?? 'Lo siento, no pude procesar la solicitud.';
    }

    public function responderLibre(string $texto): string
    {
        return $this->responder("Responde amablemente al siguiente mensaje: \"$texto\"");
    }

    public function generarPreguntaFaltante(string $intencion, array $faltantes): string
    {
        $campos = implode(', ', $faltantes);
        return "Por favor, indícame los siguientes datos necesarios para consultar tu {$intencion}: {$campos}.";
    }

    protected function requestChat(array $messages): ?string
    {
        $data = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', "Error al llamar a OpenAI: $error");
            return null;
        }

        $decoded = json_decode($result, true);
        return $decoded['choices'][0]['message']['content'] ?? null;
    }
}
