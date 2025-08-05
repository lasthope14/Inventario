<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            <i class="fas fa-user mr-2"></i>{{ __('Informaci車n del Perfil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Actualiza tu informaci車n personal.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Correo Electr車nico')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100" :value="old('email', $user->email)" required autocomplete="username" readonly />
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>{{ __('Tu correo electr車nico no se puede cambiar.') }}
            </p>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-blue-500 hover:bg-blue-600">
                <i class="fas fa-save mr-2"></i>{{ __('Guardar') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 dark:text-green-400">
                    <i class="fas fa-check mr-1"></i>{{ __('Guardado.') }}
                </p>
            @endif
        </div>
    </form>
</section>