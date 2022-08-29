<x-filament::widget class="filament-filament-info-widget">
    <x-filament::card class="relative">
        
        <div class="relative flex flex-col items-center justify-center h-12 space-y-2">
            <div class="space-y-1">
                <a
                    href="https://www.tawk.to/"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <img class="h-6 px-2 py-0.5 bg-white rounded-lg" src="img/logos/tawkto-noicon.png" />
                </a>
            </div>

            <div class="flex space-x-2 text-sm rtl:space-x-reverse">
                <a
                    href="https://dashboard.tawk.to/"
                    target="_blank"
                    rel="noopener noreferrer"
                    @class([
                        'text-gray-600 hover:text-primary-500 focus:outline-none focus:underline',
                        'dark:text-gray-300 dark:hover:text-primary-500' => config('filament.dark_mode'),
                    ])
                >
                    Dashboard
                </a>

                <span>
                    &bull;
                </span>

                <a
                    href="https://uniteagency.tawk.help"
                    target="_blank"
                    rel="noopener noreferrer"
                    @class([
                        'text-gray-600 hover:text-primary-500 focus:outline-none focus:underline',
                        'dark:text-gray-300 dark:hover:text-primary-500' => config('filament.dark_mode'),
                    ])
                >
                    Knowledge Base
                </a>
            </div>

        </div>
            
        <img class="absolute left-0 h-12 -bottom-1" src="img/logos/tawkto-icon.png"/>
            
    </x-filament::card>
</x-filament::widget>
