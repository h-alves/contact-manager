<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\GoogleGeocodingService;
use Illuminate\Http\Request;

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
        $fields = $request->validate([
            'cep' => 'required',
            'uf' => 'required',
            'cidade' => 'required',
            'bairro' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'complemento' => 'nullable',
        ]);

        $fullAddress = "{$fields['rua']}, {$fields['numero']}, {$fields['bairro']}, {$fields['cidade']}, {$fields['uf']}, {$fields['cep']}";

        $geocodingService = new GoogleGeocodingService();

        $coordinates = $geocodingService->getCoordinates($fullAddress);

        if (!$coordinates) {
            return response()->json(['error' => 'Não foi possível obter as coordenadas para o endereço fornecido.'], 400);
        }

        $address = Address::create([
            'cep' => $fields['cep'],
            'uf' => $fields['uf'],
            'cidade' => $fields['cidade'],
            'bairro' => $fields['bairro'],
            'rua' => $fields['rua'],
            'numero' => $fields['numero'],
            'complemento' => $fields['complemento'] ?? null,
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
        $fields = $request->validate([
            'cep' => 'required',
            'uf' => 'required',
            'cidade' => 'required',
            'bairro' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'complemento' => 'nullable',
        ]);

        $fullAddress = "{$fields['rua']}, {$fields['numero']}, {$fields['bairro']}, {$fields['cidade']}, {$fields['uf']}, {$fields['cep']}";

        $geocodingService = new GoogleGeocodingService();

        $coordinates = $geocodingService->getCoordinates($fullAddress);

        if (!$coordinates) {
            return response()->json(['error' => 'Não foi possível obter as coordenadas para o endereço fornecido.'], 400);
        }

        $address->update([
            'cep' => $fields['cep'],
            'uf' => $fields['uf'],
            'cidade' => $fields['cidade'],
            'bairro' => $fields['bairro'],
            'rua' => $fields['rua'],
            'numero' => $fields['numero'],
            'complemento' => $fields['complemento'] ?? null,
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
        ]);

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
}
