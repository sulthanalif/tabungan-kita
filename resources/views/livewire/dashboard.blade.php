<?php

use App\Models\Saving;
use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;

new #[Title('Dashboard')] class extends Component {
    public function saying(): string
    {
        $hour = now()->format('G');

        return match (true) {
            $hour < 5 => 'Selamat malam',
            $hour < 12 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }

    public function saldo(): string
    {
        $savings = Saving::where('category_id', Category::where('name', 'Pemasukan')->first()->id)->sum('amount');
        $expenses = Saving::where('category_id', Category::where('name', 'Pengeluaran')->first()->id)->sum('amount');

        return 'Rp. ' . number_format($savings - $expenses, 0, ',', '.');
    }

    public function pemasukan(): string
    {
        return 'Rp. ' . number_format(
            Saving::where('category_id', Category::where('name', 'Pemasukan')->first()->id)->sum('amount'),
            0,
            ',',
            '.'
        );
    }

    public function pengeluaran(): string
    {
        return 'Rp. ' . number_format(
            Saving::where('category_id', Category::where('name', 'Pengeluaran')->first()->id)->sum('amount'),
            0,
            ',',
            '.'
        );
    }

    public function with(): array
    {
        return [
            'saying' => $this->saying(),
            'saldo' => $this->saldo(),
            'pemasukan' => $this->pemasukan(),
            'pengeluaran' => $this->pengeluaran(),
        ];
    }
}; ?>

<div>
    <div class="flex-1 max-md:pt-6 self-stretch">
        <flux:heading size="xl" level="1">{{ $saying }}, {{ auth()->user()->name }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('You are logged in!') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex gap-6 md:flex-row flex-col mt-6">
        @foreach ([
            ['title' => 'Tabungan', 'value' => $saldo],
            ['title' => 'Pemasukan', 'value' => $pemasukan],
            ['title' => 'Pengeluaran', 'value' => $pengeluaran],
        ] as $card)
            <div class="flex flex-col flex-1 rounded-lg bg-gray-800 dark:bg-gray-700 p-6 shadow-lg dark:shadow-md">
                <flux:heading size="lg" class="text-white">{{ $card['title'] }}</flux:heading>
                <div class="mt-auto text-xl font-bold text-gray-200 dark:text-gray-100">
                    {{ $card['value'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>
