<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    public function geocode($address, $strict = true)
    {
        $url = 'https://nominatim.openstreetmap.org/search';
        $response = Http::withHeaders([
            'User-Agent' => 'VizioPasteleria/1.0 (contacto@vizio.pe)'
        ])->get($url, [
            'q' => $address . ', Trujillo, La Libertad, Peru',
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 1,
            'countrycodes' => 'pe',
        ]);

        if ($response->failed() || $response->json() == null) {
            Log::error('Nominatim request failed', ['address' => $address]);
            return null;
        }

        $data = $response->json()[0] ?? null;
        if (!$data) {
            Log::warning('No data from Nominatim', ['address' => $address]);
            return null;
        }

        $addressDetails = $data['address'] ?? [];

        $region = $addressDetails['state'] ??
                  $addressDetails['region'] ??
                  $addressDetails['county'] ??
                  $addressDetails['province'] ??
                  '';

        $city = $addressDetails['city'] ??
                $addressDetails['town'] ??
                $addressDetails['village'] ??
                $addressDetails['municipality'] ??
                '';

        $fullAddress = $data['display_name'] ?? '';

        $regionLower = strtolower(trim($region));
        $cityLower = strtolower(trim($city));
        $fullLower = strtolower($fullAddress);

        $keywords = ['la libertad', 'libertad', 'trujillo', 'victor larco', 'huanchaco', 'moche', 'salaverry', 'laredo', 'poroto', 'simbal', 'pueblo nuevo'];

        $isValid = false;
        foreach ($keywords as $keyword) {
            if (str_contains($fullLower, $keyword) || str_contains($regionLower, $keyword) || str_contains($cityLower, $keyword)) {
                $isValid = true;
                break;
            }
        }

        Log::info('Geocode result', [
            'address' => $address,
            'region' => $region,
            'city' => $city,
            'isValid' => $isValid,
            'display_name' => $fullAddress
        ]);

        if ($strict && !$isValid) {
            return [
                'error' => 'La dirección debe estar en la región La Libertad o en Trujillo.',
                'valid' => false,
                'warning' => false
            ];
        }

        return [
            'valid' => true,
            'lat' => (float) $data['lat'],
            'lng' => (float) $data['lon'],
            'display_name' => $data['display_name'],
            'region' => $region,
            'city' => $city,
            'warning' => !$isValid ? 'La dirección no se ha podido verificar como parte de La Libertad, pero se aceptará para continuar.' : null
        ];
    }


    /**
     * Calcular distancia en ruta usando OSRM (Open Source Routing Machine)
     */
    public function calculateDistance($originLat, $originLng, $destLat, $destLng)
    {
        $url = "https://router.project-osrm.org/route/v1/driving/{$originLng},{$originLat};{$destLng},{$destLat}?overview=false";

        $response = Http::get($url);

        if ($response->failed()) {
            return $this->haversineDistance($originLat, $originLng, $destLat, $destLng);
        }

        $data = $response->json();
        if (empty($data['routes'])) {
            return $this->haversineDistance($originLat, $originLng, $destLat, $destLng);
        }

        $distanceInMeters = $data['routes'][0]['distance'] ?? 0;
        return round($distanceInMeters / 1000, 2);
    }

    /**
     * Distancia en línea recta (Haversine) - fallback
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }
}
