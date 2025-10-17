<?php

namespace App\Services;

class GeoService
{
    public function getAddressByCep(string $cep): ?array
    {
        $cep = preg_replace('/\D/', '', $cep);
        $response = @file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || isset($data['erro'])) {
            return null;
        }

        return $data;
    }

    public function getCoordinates(string $address): ?array
    {
        $encoded = urlencode($address);
        $url = "https://geocode.maps.co/search?q={$encoded}";
        $response = @file_get_contents($url);
        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || empty($data[0])) {
            return null;
        }

        return [
            'lat' => $data[0]['lat'],
            'lng' => $data[0]['lon'],
        ];
    }

    public function distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
