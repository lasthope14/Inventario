@props(['title', 'value', 'color', 'icon'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
    <div class="p-5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ $value }}
                </div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ $title }}
                </div>
            </div>
            <div class="{{ $color }} text-white rounded-full p-3 transition-transform duration-300 transform hover:scale-110">
                <i class="fas {{ $icon }} fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="{{ $color }} h-1"></div>
</div>