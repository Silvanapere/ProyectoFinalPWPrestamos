<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TelefonoDominicano implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digitos = preg_replace('/\D/', '', (string) $value);
        if (! preg_match('/^\+?[0-9 ()-]+$/', (string) $value)
            || ! preg_match('/^(?:1)?(?:809|829|849)[0-9]{7}$/', $digitos)) {
            $fail('El campo :attribute debe ser un teléfono dominicano válido: 809, 829 o 849 y siete dígitos; se permite +1, espacios, paréntesis y guiones.');
        }
    }
}
