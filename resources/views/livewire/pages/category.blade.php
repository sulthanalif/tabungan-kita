<?php

use Flux\Flux;
use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Masmerise\Toaster\Toaster;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;

new #[Title('Kategori')] class extends Component {
    use WithPagination;

    public string $search = '';

    //var
    public ?int $id = null;
    public string $code = '';
    public string $name = '';
    public string $description = '';

    public function resetVar(): void
    {
        $this->reset('id', 'code', 'name', 'description');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'code' => ['required', 'string', 'max:255', Rule::unique(Category::class)->ignore($this->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::beginTransaction();
            if ($this->id) {
                Category::find($this->id)->update($validated);
                DB::commit();

                Toaster::success('Kategori berhasil diubah');
            } else {
                Category::create($validated);
                DB::commit();

                Toaster::success('Kategori berhasil ditambahkan');
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            Toaster::error('Terjadi kesalahan');
        }

        $this->resetVar();
        Flux::modal('showModal')->close();
    }

    public function datas(): LengthAwarePaginator
    {
        return Category::query()
            ->where('name', 'like', "%{$this->search}%")
            ->orWhere('code', 'like', "%{$this->search}%")
            ->latest()
            ->paginate(10);
    }

    public function with(): array
    {
        return [
            'categories' => $this->datas(),
        ];
    }
}; ?>

<div>
    <div class="flex-1 max-md:pt-6 self-stretch">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1">Kategori</flux:heading>

                <flux:subheading size="lg" class="mb-6">{{ __('Kategori untuk tabungan') }}</flux:subheading>
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
            <table class="table-auto w-full no-border">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-2 text-left">Kode</th>
                        <th class="px-6 py-2">Nama</th>
                        <th class="px-6 py-2">Deskripsi</th>
                        <th class="px-6 py-2">Dibuat Pada</th>
                        <th class="px-6 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="border-t px-6 py-4">
                                <div class="text-sm leading-5">
                                    {{ $category->code }}
                                </div>
                            </td>
                            <td class="border-t px-6 py-4">
                                <div class="text-sm leading-5">
                                    {{ $category->name }}
                                </div>
                            </td>
                            <td class="border-t px-6 py-4">
                                <div class="text-sm leading-5">
                                    {{ $category->description }}
                                </div>
                            </td>
                            <td class="border-t px-6 py-4">
                                <div class="text-sm leading-5">
                                    {{ $category->created_at->format('d-m-Y H:i') }}
                                </div>
                            </td>
                            <td class="border-t px-6 py-4">
                                <div class="text-sm leading-5">
                                    <flux:button variant="danger" icon="trash" wire:click="delete({{ $category->id }})"></flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="border-t px-6 py-4 text-center" colspan="4">
                                <div class="text-sm leading-5">
                                    Tidak ada kategori
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="flex justify-center">
                {{ $categories->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <flux:modal name="showModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $id ? 'Edit' : 'Tambah' }} Kategori</flux:heading>
            </div>

            <flux:input label="Kode" wire:model="code" placeholder="Masukan Kode" />

            <flux:input label="Nama" wire:model="name" placeholder="Masukan Nama" />

            <flux:textarea label="Deskripsi" wire:model="description" placeholder="Masukan deskripsi..." />

            <div class="flex">
                <flux:spacer />

                <flux:button wire:click="save" variant="primary">Save</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
