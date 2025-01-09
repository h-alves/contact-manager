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

        if (!$this->hasElevenCharacters($value)) {
            $fail("O campo {$attribute} deve ter 11 caracteres");
        }

        if ($this->isSequence($value)) {
            $fail("O campo {$attribute} deve ser válido");
        }

        if (!$this->hasCorrectDigits($value)) {
            $fail("O campo {$attribute} deve ser válido");
        }
    }

    private function hasElevenCharacters(string $value): bool
    {
        return strlen($value) == 11;
    }

    private function isSequence(string $value): bool
    {
        return preg_match('/^\d1{10}$/', $value);
    }

    private function calculateFirstDigit($value): int
    {
        $sum1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum1 += (int) $value[$i] * ($i + 1);
        }
        $sum1 %= 11;
        return ($sum1 < 10) ? $sum1 : 0;
    }

    private function calculateSecondDigit($value): int
    {
        $sum2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum2 += (int) $value[$i] * $i;
        }
        $sum2 %= 11;
        return ($sum2 < 10) ? $sum2 : 0;
    }

    private function hasCorrectDigits($value): bool
    {
        $firstDigit = $this->calculateFirstDigit($value);
        $secondDigit = $this->calculateSecondDigit($value);

        return $firstDigit == $value[9] || $secondDigit == $value[10];
    }
}
