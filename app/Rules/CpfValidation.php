<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = preg_replace('/[^0-9]/', '', $value);

        if (strlen($value) != 11) {
            $fail("O campo {$attribute} deve ter 11 caracteres");
        }

        if (preg_match('/(\d)\1{10}/', $value)) {
            $fail("O campo {$attribute} deve ser válido");
        }

        $sum1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum1 += (int) $value[$i] * ($i + 1);
        }
        $sum1 %= 11;
        $sum1 = ($sum1 < 10) ? $sum1 : 0;

        $sum2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum2 += (int)$value[$i] * $i;
        }
        $sum2 %= 11;
        $sum2 = ($sum2 < 10) ? $sum2 : 0;

        if ($sum1 != $value[9] || $sum2 != $value[10]) {
            $fail("O campo {$attribute} deve ser válido");
        }
    }
}
