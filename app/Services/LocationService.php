<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    /**
     * Coordenadas de Trujillo (fallback)
     */
    protected $fallbackLat = -8.1120;
    protected $fallbackLng = -79.0288;

    public function geocode($address)
    {
        try {
            $url = 'https://nominatim.openstreetmap.org/search';
            $response = Http::timeout(5)->withHeaders([
                'User-Agent' => 'VizioPasteleria/1.0 (contacto@vizio.pe)'
            ])->get($url, [
                'q' => $address . ', Trujillo, La Libertad, Peru',
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1,
                'countrycodes' => 'pe',
            ]);

            if ($response->failed() || $response->json() == null) {
                Log::warning('Nominatim request failed, using fallback', ['address' => $address]);
                return $this->fallbackResult($address, 'No se pudo verificar la dirección exacta, se usará ubicación aproximada.');
            }

            $data = $response->json()[0] ?? null;
            if (!$data) {
                return $this->fallbackResult($address, 'No se encontró la dirección, se usará ubicación aproximada.');
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

            if (!$isValid) {
                return $this->fallbackResult($address, 'La dirección no está dentro de La Libertad. Verifica que sea correcta.');
            }

            return [
                'valid' => true,
                'lat' => (float) $data['lat'],
                'lng' => (float) $data['lon'],
                'display_name' => $data['display_name'],
                'region' => $region,
                'city' => $city,
                'warning' => null
            ];

        } catch (\Exception $e) {
            Log::error('Exception en geocode: ' . $e->getMessage());
            return $this->fallbackResult($address, 'Error al verificar la dirección, se usará ubicación aproximada.');
        }
    }

    /**
     * Resultado de fallback (coordenadas de Trujillo)
     */
    private function fallbackResult($address, $message)
    {
        return [
            'valid' => true,
            'lat' => $this->fallbackLat,
            'lng' => $this->fallbackLng,
            'display_name' => $address . ' (Trujillo, La Libertad)',
            'region' => 'La Libertad',
            'city' => 'Trujillo',
            'warning' => $message
        ];
    }

    /**
     * Calcular distancia con OSRM (fallback a Haversine)
     */
    public function calculateDistance($originLat, $originLng, $destLat, $destLng)
    {
        $url = "https://router.project-osrm.org/route/v1/driving/{$originLng},{$originLat};{$destLng},{$destLat}?overview=false";

        try {
            $response = Http::timeout(5)->get($url);
            if ($response->failed() || empty($response->json()['routes'])) {
                return $this->haversineDistance($originLat, $originLng, $destLat, $destLng);
            }
            $distanceInMeters = $response->json()['routes'][0]['distance'] ?? 0;
            return round($distanceInMeters / 1000, 2);
        } catch (\Exception $e) {
            return $this->haversineDistance($originLat, $originLng, $destLat, $destLng);
        }
    }

    /**
     * Haversine fallback
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
