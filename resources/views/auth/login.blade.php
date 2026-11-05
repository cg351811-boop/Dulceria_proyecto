<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    {{-- Mensaje: Sesión cerrada desde otro dispositivo --}}
    @if(request('sesion_cerrada') == '1' || session('sesion_cerrada'))
        <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-800 text-sm flex items-start gap-3 shadow-sm" style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 0.75rem;">
            <span style="font-size: 1.5rem; line-height: 1;">⚠️</span>
            <div>,ju
                <strong style="display: block; font-weight: 700; margin-bottom: 0.25rem;">Sesión finalizada por seguridad</strong>
                Se cerró la sesión porque se inició sesión en otro dispositivo con tu cuenta.<br>
                Por tu seguridad, esta sesión ha sido finalizada automáticamente.
            </div>
        </div>
    @endif

    {{-- Mensaje: Sesión cerrada por inactividad (5 minutos) --}}
    @if(request('inactividad') == '1' || session('inactividad'))
        <div class="mb-4 p-4 rounded-lg bg-yellow-100 border border-yellow-300 text-yellow-900 text-sm flex items-start gap-3 shadow-sm" style="background-color: #fef9c3; border: 1px solid #fde047; color: #713f12; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 0.75rem;">
            <span style="font-size: 1.5rem; line-height: 1;">⏱️</span>
            <div>
                <strong style="display: block; font-weight: 700; margin-bottom: 0.25rem;">Sesión expirada por inactividad</strong>
                Tu sesión ha sido cerrada automáticamente tras 5 minutos de inactividad por motivos de seguridad. Por favor, inicia sesión nuevamente.
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
