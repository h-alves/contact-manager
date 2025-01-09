<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Contact;
use App\Rules\CpfValidation;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private $addressController;

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
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'cpf' => ['required', 'unique:contacts,cpf', new CpfValidation()],
            'phone' => 'required',
            'cep' => 'required',
            'uf' => 'required',
            'cidade' => 'required',
            'bairro' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'complemento' => 'nullable',
        ]);

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
    public function update(Request $request, Contact $contact)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'cpf' => ['required', 'unique:contacts,cpf', new CpfValidation()],
            'phone' => 'required',
            'cep' => 'required',
            'uf' => 'required',
            'cidade' => 'required',
            'bairro' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'complemento' => 'nullable',
        ]);

        $addressResponse = $this->addressController->update($request, $contact->address());
        $address = json_decode($addressResponse->getContent());

        $contact->update([
            'name' => $fields['name'],
            'cpf' => $fields['cpf'],
            'phone' => $fields['phone'],
        ]);

        return response()->json([
            'contact' => $contact,
            'address' => $address,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $this->addressController->destroy($contact->address());

        $contact->delete();
        return response()->json(['message' => 'Contato excluído com sucesso.'], 200);
    }
}
