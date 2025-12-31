<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

/**
 * Shared password validation rules.
 *
 * Provides consistent password validation across registration and reset flows.
 */
trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * Uses Laravel's default password strength rules plus confirmation.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }
}
