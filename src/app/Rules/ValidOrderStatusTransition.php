<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidOrderStatusTransition implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }

    // app/Rules/ValidOrderStatusTransition.php

    public function passes($attribute, $value)
    {
        $currentStatus = $this->order->status;

        return match ($value) {
            'aprovado' => $currentStatus === 'solicitado',
            'cancelado' => $currentStatus === 'solicitado' || $currentStatus === 'aprovado',
            default => false
        };
    }

    public function message()
    {
        return 'Transição de status inválida.';
    }
}
