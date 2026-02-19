<?php

namespace App\Libraries;

class WhatsAppAPI
{
    private $accessToken;
    private $phoneNumberId;
    private $apiUrl;

    public function __construct()
    {
        $this->accessToken = getenv('WA_ACCESS_TOKEN');
        $this->phoneNumberId = getenv('WA_PHONE_ID');
        $this->apiUrl = "https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages";
    }

    public function enviarMensajeTexto($telefono, $mensaje)
    {
        $data = [
            "messaging_product" => "whatsapp",
            "to" => $telefono,
            "type" => "text",
            "text" => [
                "body" => $mensaje
            ]
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->accessToken}",
                "Content-Type: application/json"
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['status' => $status, 'response' => json_decode($response, true)];
    }
}
