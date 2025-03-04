<x-layouts.app.sidebar title="{{ $title ?? '' }}">
    <flux:main>
        {{ $slot }}
        <x-toaster-hub />
    </flux:main>
</x-layouts.app.sidebar>
