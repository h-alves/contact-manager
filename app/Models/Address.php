<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;

    protected $fillable = ['postal_code', 'state', 'city', 'neighborhood', 'street', 'number', 'complement', 'latitude', 'longitude'];

    public function contacts() {
        return $this->hasMany(Contact::class);
    }
}
