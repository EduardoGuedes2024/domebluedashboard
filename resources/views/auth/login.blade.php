<x-guest-layout>

    <div class="flex flex-col items-center mb-6">
        <img src="{{ asset('imagens/favicondomeblueazul.png') }}" class="h-14 mb-2" alt="Logo">
        <h1 class="text-2xl font-black text-slate-800">DomeBlue</h1>
        <p class="text-sm text-slate-500">Acesse sua conta</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Código de Acesso -->
        <div>
            <x-input-label for="codigo_acesso" value="Usuário" />

            <x-text-input
                id="codigo_acesso"
                class="block mt-1 w-full"
                type="text"
                name="codigo_acesso"
                :value="old('codigo_acesso')"
                required
                autofocus
                autocomplete="username"
            />

            <x-input-error :messages="$errors->get('codigo_acesso')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Senha" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600">Lembrar de mim</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                Entrar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
