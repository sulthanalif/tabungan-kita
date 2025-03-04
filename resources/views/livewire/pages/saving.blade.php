<?php

use App\Models\Saving;
use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Masmerise\Toaster\Toastable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

new #[Title('Tabungan')] class extends Component {
    use WithPagination, Toastable;

    public string $search = '';

    //var
    public ?int $id = null;
    public string $date = '';
    public string $category_id = '';
    public string $amount = '';
    public string $description = '';

    public function resetVar(): void
    {
        $this->reset('id', 'date', 'category_id', 'amount', 'description');
    }

    public function confirmDelete($id): void
    {
        $saving = Saving::find($id);

        $this->id = $saving->id;

        Flux::modal('confirmDelete')->show();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'date' => ['required', 'date'],
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        // dd($validated);

        try {
            DB::beginTransaction();
            Saving::create([
                'user_id' => auth()->id(),
                'category_id' => $validated['category_id'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'date' => $validated['date'],
            ]);
            DB::commit();
            $this->resetVar();
            $this->success('Tabungan berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::debug("message: {$th->getMessage()}");
            $this->error($th->getMessage());
        }
    }

    public function datas(): LengthAwarePaginator
    {
        return Saving::query()
            ->with('user', 'category')
            ->whereHas('user', function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(5);
    }

    public function with(): array
    {
        return [
            'savings' => $this->datas(),
            'categories' => Category::all(),
        ];
    }
}; ?>

<div>
    <div class="flex-1 max-md:pt-6 self-stretch">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1">Tabungan</flux:heading>

                <flux:subheading size="lg" class="mb-6">{{ __('Ini adalah tabungan kita') }}</flux:subheading>
            </div>
            <div>
                <flux:modal.trigger name="showModal">
                    <flux:button wire:click="resetVar">Tambah</flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        <flux:separator variant="subtle" />
    </div>
    <div class="mt-5 w-full">
        <div class="overflow-x-auto rounded-lg">
            <table class="table-auto w-full border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-2 text-left whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-2 text-left whitespace-nowrap">Kategori</th>
                        <th class="px-6 py-2 text-left min-w-[200px]">Deskripsi</th>
                        <th class="px-6 py-2 text-left whitespace-nowrap">Jumlah</th>
                        <th class="px-6 py-2 text-left whitespace-nowrap">Dibuat Oleh</th>
                        <th class="px-6 py-2 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse ($savings as $saving)
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($saving->date)->locale('id_ID')->isoFormat('D MMMM YYYY') }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ $saving->category->name }}
                            </td>
                            <td class="px-6 py-4 text-sm break-words">
                                {{ $saving->description }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                Rp.{{ number_format($saving->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ $saving->user->name }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:button variant="danger" icon="trash"
                                    wire:click="confirmDelete({{ $saving->id }})">
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-4 text-center" colspan="6">
                                Tidak ada tabungan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $savings->links() }}
            </div>
        </div>
    </div>

    <flux:modal name="showModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $id ? 'Edit' : 'Tambah' }} Tabungan</flux:heading>
            </div>

            <flux:input type="date" wire:model="date" label="Tanggal" />

            <flux:select label="Kategori" wire:model="category_id" placeholder="Pilih Kategori...">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </flux:select>

            <flux:textarea label="Deskripsi" wire:model="description" placeholder="Masukan deskripsi..." />

            <flux:input.group>
                <flux:input.group.prefix>Rp</flux:input.group.prefix>

                <flux:input type='number' wire:model="amount" placeholder="Maukan Jumlah Uang" />
            </flux:input.group>

            <div class="flex">
                <flux:spacer />

                <flux:button wire:click="save" variant="primary">Save</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="confirmDelete" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus data?</flux:heading>

            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
