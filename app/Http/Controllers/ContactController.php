<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Address;
use App\Models\Contact;
use App\Rules\CpfValidation;
use App\Services\GoogleGeocodingService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Contact::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'cpf' => ['required', new CpfValidation()],
            'phone' => 'required',
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

        if (!$address) {
            return response()->json(['error' => 'Não foi possível registrar o endereço fornecido.'], 400);
        }

        $contact = request()->user()->contacts()->create([
            'name' => $fields['name'],
            'cpf' => $fields['cpf'],
            'phone' => $fields['phone'],
            'address_id' => $address->id,
        ]);

        if (!$contact) {
            return response()->json(['error' => 'Não foi possível registrar o contato fornecido.'], 400);
        }

        return response()->json([
            'contact' => $contact,
            'address' => $address,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
