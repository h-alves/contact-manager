<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleGeocodingService
{
    public function getCoordinates(string $address): ?array
    {
        $apiKey = config('services.google_maps.api_key');
        $url = "https://maps.googleapis.com/maps/api/geocode/json";

        $response = Http::get($url, [
            'address' => $address,
            'key' => $apiKey,
        ]);

        if ($response->ok() && $response['status'] === 'OK') {
            $location = $response['results'][0]['geometry']['location'];
            return [
                'latitude' => $location['lat'],
                'longitude' => $location['lng'],
            ];
        }

        return null;
    }
}
