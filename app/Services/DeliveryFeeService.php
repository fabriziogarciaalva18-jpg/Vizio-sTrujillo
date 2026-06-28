<?php

namespace App\Services;

class DeliveryFeeService
{
    protected $storeLat;
    protected $storeLng;
    protected $baseFee;
    protected $perKm;
    protected $freeDistance;
    protected $maxDistance;

    public function __construct()
    {
        $config = config('delivery');
        $this->storeLat = $config['store']['lat'];
        $this->storeLng = $config['store']['lng'];
        $this->baseFee = $config['fee']['base'];
        $this->perKm = $config['fee']['per_km'];
        $this->freeDistance = $config['fee']['free_distance'];
        $this->maxDistance = $config['fee']['max_distance'];
    }

    /**
     * Calcular el costo de envío basado en la distancia
     */
    public function calculate($distance)
    {
        if ($distance > $this->maxDistance) {
            return [
                'error' => "La distancia ({$distance} km) excede el máximo permitido ({$this->maxDistance} km).",
                'fee' => null,
                'distance' => $distance,
                'valid' => false,
            ];
        }

        $distanceToCharge = max(0, $distance - $this->freeDistance);
        $fee = $this->baseFee + ($distanceToCharge * $this->perKm);

        return [
            'valid' => true,
            'distance' => $distance,
            'fee' => round($fee, 2),
            'distance_km' => $distance,
            'base_fee' => $this->baseFee,
            'per_km' => $this->perKm,
            'free_distance' => $this->freeDistance,
        ];
    }
}
