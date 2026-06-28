<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    /**
     * Buscar dirección y obtener coordenadas usando Nominatim (OpenStreetMap)
     */
    public function geocode($address)
    {
        $url = 'https://nominatim.openstreetmap.org/search';
        $response = Http::withHeaders([
            'User-Agent' => 'VizioPasteleria/1.0 (contacto@vizio.pe)' // Obligatorio para Nominatim
        ])->get($url, [
            'q' => $address . ', Trujillo, La Libertad, Peru',
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 1,
            'countrycodes' => 'pe',
        ]);

        if ($response->failed() || $response->json() == null) {
            return null;
        }

        $data = $response->json()[0] ?? null;
        if (!$data) return null;

        // Verificar que esté en La Libertad
        $addressDetails = $data['address'] ?? [];
        $region = $addressDetails['state'] ?? $addressDetails['region'] ?? '';
        $city = $addressDetails['city'] ?? $addressDetails['town'] ?? $addressDetails['village'] ?? '';

        if (!str_contains(strtolower($region), 'la libertad') && !str_contains(strtolower($city), 'trujillo')) {
            return [
                'error' => 'La dirección debe estar en la región La Libertad o en Trujillo.',
                'valid' => false
            ];
        }

        return [
            'valid' => true,
            'lat' => (float) $data['lat'],
            'lng' => (float) $data['lon'],
            'display_name' => $data['display_name'],
            'region' => $region,
            'city' => $city,
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
            // Fallback a distancia en línea recta (Haversine)
            return $this->haversineDistance($originLat, $originLng, $destLat, $destLng);
        }

        $data = $response->json();
        if (empty($data['routes'])) {
            return $this->haversineDistance($originLat, $originLng, $destLat, $destLng);
        }

        // Distancia en metros, convertir a kilómetros
        $distanceInMeters = $data['routes'][0]['distance'] ?? 0;
        return round($distanceInMeters / 1000, 2);
    }

    /**
     * Distancia en línea recta (Haversine) - fallback
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }
}
