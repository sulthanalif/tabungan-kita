<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Title;

new #[Title('Dashboard')] class extends Component {
    public function saying(): string
    {
        $hour = now()->format('G');

        if ($hour < 5) {
            return 'Selamat malam';
        } elseif ($hour < 12) {
            return 'Selamat pagi';
        } elseif ($hour < 15) {
            return 'Selamat siang';
        } elseif ($hour < 18) {
            return 'Selamat sore';
        }

        return 'Selamat malam';
    }

    public function with(): array
    {
        return [
            'saying' => $this->saying(),
        ];
    }
}; ?>

<div>
    <div class="flex-1 max-md:pt-6 self-stretch">
        <flux:heading size="xl" level="1">{{ $saying }}, {{ auth()->user()->name }}</flux:heading>

        <flux:subheading size="lg" class="mb-6">{{ __('You are logged in!') }}</flux:subheading>

        <flux:separator variant="subtle" />
    </div>
</div>
