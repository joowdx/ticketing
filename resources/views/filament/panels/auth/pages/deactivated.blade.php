<x-filament-panels::page.simple>
    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
        Your account has been deactivated by an administrator at
    </p>

    <p class="text-center text-sm font-mono text-gray-500 dark:text-gray-400">
        {{ $user->deactivated_at->toDateTimeString() }}
    </p>

</x-filament-panels::page.simple>
