<?php

namespace App\Http\Requests;

use App\Rules\CpfValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
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
            'name' => 'sometimes|max:255',
            'cpf' => ['sometimes', 'unique:contacts,cpf,' . $this->contact->id, new CpfValidation()],
            'phone' => 'sometimes',
            'postal_code' => 'sometimes',
            'state' => 'sometimes',
            'city' => 'sometimes',
            'neighborhood' => 'sometimes',
            'street' => 'sometimes',
            'number' => 'sometimes',
            'complement' => 'nullable',
        ];
    }
}
