<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;

    protected $fillable = ['cep', 'uf', 'cidade', 'bairro', 'rua', 'numero', 'complemento', 'latitude', 'longitude'];

    public function contact() {
        return $this->hasMany(Contact::class);
    }
}
