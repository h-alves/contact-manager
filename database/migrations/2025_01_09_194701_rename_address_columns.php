<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('cep', 'postal_code');
            $table->renameColumn('uf', 'state');
            $table->renameColumn('cidade', 'city');
            $table->renameColumn('bairro', 'neighborhood');
            $table->renameColumn('rua', 'street');
            $table->renameColumn('numero', 'number');
            $table->renameColumn('complemento', 'complement');
        });
    }

    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('postal_code', 'cep');
            $table->renameColumn('state', 'uf');
            $table->renameColumn('city', 'cidade');
            $table->renameColumn('neighborhood', 'bairro');
            $table->renameColumn('street', 'rua');
            $table->renameColumn('number', 'numero');
            $table->renameColumn('complement', 'complemento');
        });
    }
};
