<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ReCaptchaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret');

        if (!$secret) {
            $fail('El sistema no está configurado para verificar captcha.');
            return;
        }

        // 🔥 SOLUCIÓN: Añadir 'withoutVerifying()' para evitar el error SSL en desarrollo
        $response = Http::withoutVerifying()->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $data = $response->json();

        if (empty($data['success']) || $data['success'] !== true) {
            $fail('Por favor, completa el captcha correctamente.');
        }
    }
}
