<?php

namespace Tests\Unit;

use App\Rules\CpfValidation;
use Tests\TestCase;

class CpfValidationTest extends TestCase
{
    public function test_cpf_validation()
    {
        $cpfValidator = new CpfValidation();

        $cpf = '38742388066';

        $fail = function ($message) {
            $this->fail($message);
        };

        $cpfValidator->validate('cpf', $cpf, $fail);

        $this->addToAssertionCount(1);
    }

    public function test_cpf_validation_with_empty_cpf()
    {
        $cpfValidator = new CpfValidation();

        $cpf = '';

        $fail = function ($message) {
            $this->assertEquals('O campo cpf deve ter 11 caracteres', $message);
        };

        $cpfValidator->validate('cpf', $cpf, $fail);
    }

    public function test_cpf_validation_with_sequence()
    {
        $cpfValidator = new CpfValidation();

        $cpf = '11111111111';

        $fail = function ($message) {
            $this->assertEquals('O campo cpf deve ser válido', $message);
        };

        $cpfValidator->validate('cpf', $cpf, $fail);
    }

    public function test_cpf_validation_with_incorrect_digits() {
        $cpfValidator = new CpfValidation();

        $cpf = '38742388011'; // Últimos dois dígitos diferentes dos do cpf do primeiro teste

        $fail = function ($message) {
            $this->assertEquals('O campo cpf deve possuir código de identificação válido', $message);
        };

        $cpfValidator->validate('cpf', $cpf, $fail);
    }
}
