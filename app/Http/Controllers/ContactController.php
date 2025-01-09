<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;

class ContactController extends Controller
{
    private AddressController $addressController;

    public function __construct() {
        $this->addressController = new AddressController();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::with('address')->get();
        return response()->json($contacts, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $fields = $request->validated();

        $addressResponse = $this->addressController->store($request);
        $address = json_decode($addressResponse->getContent());

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
        $contact->load('address');
        return response()->json($contact, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $fields = $request->validated();

        if ($request->hasAny(['cep', 'uf', 'cidade', 'bairro', 'rua', 'numero', 'complemento'])) {
            $addressResponse = $this->addressController->update($request, $contact->address);
            $address = json_decode($addressResponse->getContent());
        }

        $contact->update($fields);

        return response()->json([
            'contact' => $contact,
            'address' => $address ?? $contact->address,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $this->addressController->destroy($contact->address);

        $contact->delete();
        return response()->json(['message' => 'Contato excluído com sucesso.'], 200);
    }
}
