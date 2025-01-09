<?php

namespace Tests\Feature;

use App\Services\GoogleGeocodingService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleGeocodingServiceTest extends TestCase
{
    public function test_get_coordinates_returns_coordinates_when_response_is_ok()
    {
        $address = '463, Rua Pauster - Batel, Curitiba Paraná';

        $googleGeocodingService = new GoogleGeocodingService();
        $coordinates = $googleGeocodingService->getCoordinates($address);

        $this->assertIsArray($coordinates);
        $this->assertArrayHasKey('latitude', $coordinates);
        $this->assertArrayHasKey('longitude', $coordinates);
        $this->assertEquals(-25.4437172, $coordinates['latitude']);
        $this->assertEquals(-49.2789859, $coordinates['longitude']);
    }

    public function test_get_coordinates_returns_coordinates_when_response_is_not_ok()
    {
        $address = 'Endereço Inválido';

        $googleGeocodingService = new GoogleGeocodingService();
        $coordinates = $googleGeocodingService->getCoordinates($address);
        
        $this->assertNull($coordinates);
    }
}
