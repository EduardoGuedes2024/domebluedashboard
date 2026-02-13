<?php

namespace App\Http\Requests\Auth;

use App\Models\Operador;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_acesso' => ['required', 'string'],
            'password'      => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $codigo = (string) $this->input('codigo_acesso');
        $senhaDigitada = (string) $this->input('password');

        /** @var Operador|null $user */
        $user = Operador::where('codigo_acesso', $codigo)->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            $this->throwInvalid();
        }

        // Se já tem hash (novo padrão)
        if (!empty($user->senha_hash)) {
            if (!Hash::check($senhaDigitada, $user->senha_hash)) {
                RateLimiter::hit($this->throttleKey());
                $this->throwInvalid();
            }
        } else {
            // Senha legada (como está hoje)
            $senhaBanco = (string) ($user->senha ?? '');

            if ($senhaDigitada !== $senhaBanco) {
                RateLimiter::hit($this->throttleKey());
                $this->throwInvalid();
            }

            // Migra automaticamente pra hash
            $user->senha_hash = Hash::make($senhaDigitada);
            $user->save();
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    protected function throwInvalid(): void
    {
        throw ValidationException::withMessages([
            'codigo_acesso' => trans('auth.failed'),
        ]);
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'codigo_acesso' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        // trava por codigo_acesso + ip
        return Str::transliterate(Str::lower($this->string('codigo_acesso')).'|'.$this->ip());
    }
}
