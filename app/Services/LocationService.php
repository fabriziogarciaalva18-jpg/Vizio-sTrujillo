<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    /**
     * Coordenadas de la tienda (Víctor Larco, Trujillo)
     */
    protected $storeLat = -8.1120;
    protected $storeLng = -79.0288;

    /**
     * Área de cobertura (km)
     */
    protected $maxDistance = 50;

    /**
     * Geocodificar una dirección con múltiples estrategias
     */
    public function geocode($address)
    {
        if (empty($address)) {
            return $this->errorResponse('La dirección está vacía.');
        }

        // Limpiar dirección
        $address = $this->sanitizeAddress($address);

        // Estrategia 1: Búsqueda exacta con ciudad y país
        $result = $this->searchAddress($address);

        if ($result && $result['valid']) {
            return $result;
        }

        // Estrategia 2: Búsqueda solo con ciudad
        $cityOnly = $this->extractCity($address);
        if ($cityOnly && $cityOnly != $address) {
            $result = $this->searchAddress($cityOnly . ', Trujillo, La Libertad, Peru');
            if ($result && $result['valid']) {
                return $result;
            }
        }

        // Estrategia 3: Fallback
        return $this->fallbackResult($address, 'No se pudo geocodificar la dirección exacta. Se usará ubicación aproximada.');
    }

    /**
     * Búsqueda principal en Nominatim
     */
    private function searchAddress($query)
    {
        try {
            // Cachear resultados para evitar llamadas repetidas
            $cacheKey = 'geocode_' . md5($query);
            if (Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                Log::info('Geocode cache hit', ['query' => $query]);
                return $cached;
            }

            $url = 'https://nominatim.openstreetmap.org/search';

            $params = [
                'q' => $query,
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1,
                'countrycodes' => 'pe',
                'bounded' => 1,
                'viewbox' => '-79.5,-7.8,-78.5,-8.5', // Área de La Libertad
            ];

            $response = Http::timeout(8)
                ->retry(2, 200)
                ->withHeaders([
                    'User-Agent' => 'VizioPasteleria/1.0 (contacto@vizio.pe)',
                    'Accept-Language' => 'es',
                ])
                ->get($url, $params);

            if ($response->failed() || empty($response->json())) {
                Log::warning('Nominatim request failed or empty', ['query' => $query]);
                return null;
            }

            $data = $response->json()[0] ?? null;

            if (!$data) {
                Log::info('No results from Nominatim', ['query' => $query]);
                return null;
            }

            $result = $this->parseGeocodeResult($data, $address);

            if ($result && $result['valid']) {
                // Cache por 30 días (las direcciones no cambian)
                Cache::put($cacheKey, $result, 60 * 24 * 30);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Exception en geocode: ' . $e->getMessage(), ['query' => $query]);
            return null;
        }
    }

    /**
     * Parsear resultado de Nominatim
     */
    private function parseGeocodeResult($data, $originalAddress)
    {
        $lat = (float) $data['lat'];
        $lng = (float) $data['lon'];
        $displayName = $data['display_name'] ?? '';
        $addressDetails = $data['address'] ?? [];

        // Extraer componentes
        $region = $this->extractRegion($addressDetails);
        $city = $this->extractCityFromDetails($addressDetails);
        $district = $addressDetails['suburb'] ?? $addressDetails['city_district'] ?? '';
        $postcode = $addressDetails['postcode'] ?? '';
        $road = $addressDetails['road'] ?? '';
        $houseNumber = $addressDetails['house_number'] ?? '';

        // Validar que esté en La Libertad
        $isValid = $this->isValidRegion($region, $city, $district, $displayName);

        // Validar distancia máxima
        $distance = $this->calculateDistance($this->storeLat, $this->storeLng, $lat, $lng);

        Log::info('Geocode parsed', [
            'original' => $originalAddress,
            'region' => $region,
            'city' => $city,
            'district' => $district,
            'isValid' => $isValid,
            'distance' => $distance,
            'display' => $displayName
        ]);

        if (!$isValid) {
            return $this->errorResponse('La dirección no está dentro de La Libertad.');
        }

        if ($distance > $this->maxDistance) {
            return $this->errorResponse("La dirección está a {$distance} km de la tienda. El área de cobertura es de {$this->maxDistance} km.");
        }

        return [
            'valid' => true,
            'lat' => $lat,
            'lng' => $lng,
            'display_name' => $displayName,
            'region' => $region,
            'city' => $city,
            'district' => $district,
            'postcode' => $postcode,
            'road' => $road,
            'house_number' => $houseNumber,
            'distance_km' => $distance,
            'warning' => null,
        ];
    }

    /**
     * Extraer región de los detalles de dirección
     */
    private function extractRegion($details)
    {
        // Orden de prioridad para región/departamento
        $keys = ['state', 'region', 'province', 'county', 'city'];

        foreach ($keys as $key) {
            if (!empty($details[$key])) {
                return $details[$key];
            }
        }

        return '';
    }

    /**
     * Extraer ciudad de los detalles de dirección
     */
    private function extractCityFromDetails($details)
    {
        $keys = ['city', 'town', 'village', 'municipality'];

        foreach ($keys as $key) {
            if (!empty($details[$key])) {
                return $details[$key];
            }
        }

        return '';
    }

    /**
     * Extraer ciudad de una dirección (para búsqueda alternativa)
     */
    private function extractCity($address)
    {
        $parts = explode(',', $address);
        return trim($parts[0] ?? '');
    }

    /**
     * Validar que la dirección esté en La Libertad
     */
    private function isValidRegion($region, $city, $district, $displayName)
    {
        $text = strtolower(trim($region . ' ' . $city . ' ' . $district . ' ' . $displayName));

        $validTerms = [
            'la libertad',
            'libertad',
            'trujillo',
            'victor larco',
            'huanchaco',
            'moche',
            'salaverry',
            'laredo',
            'poroto',
            'simbal',
            'pueblo nuevo',
            'el porvenir',
            'florencia de mora',
            'la esperanza',
            'las delicias',
            'buenos aires',
            'mampuesto',
            'santo domingo',
            'santiago de cao',
            'paijan'
        ];

        foreach ($validTerms as $term) {
            if (str_contains($text, $term)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitizar dirección
     */
    private function sanitizeAddress($address)
    {
        // Eliminar espacios múltiples
        $address = preg_replace('/\s+/', ' ', trim($address));
        // Eliminar caracteres especiales
        $address = preg_replace('/[^\p{L}\p{N}\s,.-]/u', '', $address);
        return $address;
    }

    /**
     * Resultado de error
     */
    private function errorResponse($message)
    {
        return [
            'valid' => false,
            'lat' => null,
            'lng' => null,
            'display_name' => null,
            'region' => null,
            'city' => null,
            'error' => $message,
            'warning' => $message,
        ];
    }

    /**
     * Resultado de fallback (coordenadas de Trujillo)
     */
    private function fallbackResult($address, $message)
    {
        return [
            'valid' => true,
            'lat' => $this->storeLat,
            'lng' => $this->storeLng,
            'display_name' => $address . ' (Trujillo, La Libertad)',
            'region' => 'La Libertad',
            'city' => 'Trujillo',
            'district' => 'Víctor Larco',
            'postcode' => '13009',
            'distance_km' => 0,
            'warning' => $message,
        ];
    }

    /**
     * Calcular distancia con OSRM (con fallback a Haversine)
     */
    public function calculateDistance($originLat, $originLng, $destLat, $destLng)
    {
        $url = "https://router.project-osrm.org/route/v1/driving/{$originLng},{$originLat};{$destLng},{$destLat}?overview=false";

        try {
            $response = Http::timeout(5)
                ->retry(2, 300)
                ->withHeaders(['User-Agent' => 'VizioPasteleria/1.0'])
                ->get($url);

            if ($response->failed() || empty($response->json()['routes'])) {
                return $this->haversineDistance($originLat, $originLng, $destLat, $destLng);
            }

            $distanceInMeters = $response->json()['routes'][0]['distance'] ?? 0;
            return round($distanceInMeters / 1000, 2);
        } catch (\Exception $e) {
            Log::warning('OSRM request failed, using Haversine', [
                'error' => $e->getMessage(),
                'origin' => "{$originLat},{$originLng}",
                'dest' => "{$destLat},{$destLng}"
            ]);
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

    /**
     * Obtener coordenadas de la tienda
     */
    public function getStoreLocation()
    {
        return [
            'lat' => $this->storeLat,
            'lng' => $this->storeLng
        ];
    }
}
