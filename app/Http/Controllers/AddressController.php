<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\GoogleGeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Address::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validated();

        $fullAddress = $this->generateFullAddress($fields);
        $coordinates = $this->getCoordinatesFromAddress($fullAddress);

        if (!$coordinates) {
            return response()->json(['error' => 'Não foi possível obter as coordenadas para o endereço fornecido.'], 400);
        }

        $address = Address::create([
            'postal_code' => $fields['postal_code'],
            'state' => $fields['state'],
            'city' => $fields['city'],
            'neighborhood' => $fields['neighborhood'],
            'street' => $fields['street'],
            'number' => $fields['number'],
            'complement' => $fields['complement'] ?? null,
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
        ]);

        return response()->json($address, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        $fields = $request->validated();

        $fullAddress = $this->generateFullAddress($fields);
        $coordinates = $this->getCoordinatesFromAddress($fullAddress);

        if (!$coordinates) {
            return response()->json(['error' => 'Não foi possível obter as coordenadas para o endereço fornecido.'], 400);
        }

        $address->update($fields);

        return response()->json($address, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $address->delete();
        return response()->json(['message' => 'Endereço excluído com sucesso.'], 200);
    }

    public function search(Request $request) {
        $request->validate([
            'state' => 'required',
            'city' => 'required',
            'address' => 'nullable',
        ]);

        $state = $request->get('state');
        $city = $request->get('city');
        $address = $request->get('address');

        $apiUrl = "https://viacep.com.br/ws/{$state}/{$city}/{$address}/json/";

        $response = Http::get($apiUrl);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Não foi possível buscar os endereços no momento.',
            ], 500);
        }

        $data = $response->json();

        if (empty($data)) {
            return response()->json([
                'message' => 'Nenhum endereço encontrado.',
            ], 404);
        }

        return response()->json($data, 200);
    }

    private function generateFullAddress(array $fields): string
    {
        $street = $fields['street'] ?? '';
        $number = $fields['number'] ?? '';
        $neighborhood = $fields['neighborhood'] ?? '';
        $city = $fields['city'] ?? '';
        $state = $fields['state'] ?? '';
        $postal_code = $fields['postal_code'] ?? '';

        return trim("{$street}, {$number}, {$neighborhood}, {$city}, {$state}, {$postal_code}");
    }

    private function getCoordinatesFromAddress(string $fullAddress): ?array
    {
        $geocodingService = new GoogleGeocodingService();
        $coordinates = $geocodingService->getCoordinates($fullAddress);

        if (!$coordinates) {
            return null;
        }

        return $coordinates;
    }
}
