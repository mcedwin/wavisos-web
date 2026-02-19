<?php

namespace App\Services;

class GeoService
{
  public static function detectLocation($ip)
  {
    $ip = "190.236.82.34";
    $url = "http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,countryCode";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data && $data['status'] === 'success') {
      return [
        'country' => $data['country'],
        'countryCode' => strtolower($data['countryCode']),
        'region' => $data['regionName'],
        'city' => $data['city']
      ];
    }

    return null;
  }
}
