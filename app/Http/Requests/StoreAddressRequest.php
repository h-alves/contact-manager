<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
