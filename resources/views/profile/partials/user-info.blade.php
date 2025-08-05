<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            <i class="fas fa-info-circle mr-2"></i>{{ __('Información Adicional') }}
        </h2>
    </header>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="created_at" :value="__('Fecha de Registro')" />
            <div class="mt-1 p-3 bg-gray-100 dark:bg-gray-800 rounded-md">
                <i class="far fa-calendar-alt mr-2"></i>{{ $user->created_at->format('d/m/Y') }}
            </div>
        </div>

        <div>
            <x-input-label for="last_login" :value="__('Último Inicio de Sesión')" />
            <div class="mt-1 p-3 bg-gray-100 dark:bg-gray-800 rounded-md">
                <i class="far fa-clock mr-2"></i>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : 'N/A' }}
            </div>
        </div>
    </div>
</section>