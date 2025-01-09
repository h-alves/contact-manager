<?php

namespace App\Http\Requests;

use App\Rules\CpfValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'cpf' => ['required', 'unique:contacts,cpf', new CpfValidation()],
            'phone' => 'required',
            'postal_code' => 'required',
            'state' => 'required',
            'city' => 'required',
            'neighborhood' => 'required',
            'street' => 'required',
            'number' => 'required',
            'complement' => 'nullable',
        ];
    }
}
