<?php

use Flux\Flux;
use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Masmerise\Toaster\Toaster;
use Illuminate\Validation\Rule;
use Masmerise\Toaster\Toastable;
use Illuminate\Pagination\LengthAwarePaginator;

new #[Title('Kategori')] class extends Component {
    use WithPagination, Toastable;

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

    public function confirmDelete($id): void
    {
        $category = Category::find($id);

        $this->id = $category->id;

        Flux::modal('confirmDelete')->show();
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

                $this->success('Kategori berhasil diubah');
            } else {
                Category::create($validated);
                DB::commit();

                $this->success('Kategori berhasil ditambahkan');
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->error('Terjadi kesalahan');
        }

        $this->resetVar();
        Flux::modal('showModal')->close();
    }

    public function delete(): void
    {
        try {
            DB::beginTransaction();
            Category::find($this->id)->delete();
            DB::commit();
            $this->resetVar();
            $this->success('Kategori berhasil dihapus');
            Flux::modal('confirmDelete')->close();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->resetVar();
            $this->error('Terjadi kesalahan');
            Flux::modal('confirmDelete')->close();
        }
    }

    public function datas(): LengthAwarePaginator
    {
        return Category::query()
            ->where('name', 'like', "%{$this->search}%")
            ->orWhere('code', 'like', "%{$this->search}%")
            ->latest()
            ->paginate(5);
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
            <table class="table-auto w-full border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-2 text-left whitespace-nowrap">Kode</th>
                        <th class="px-6 py-2 text-left whitespace-nowrap">Nama</th>
                        <th class="px-6 py-2 text-left min-w-[200px]">Deskripsi</th>
                        <th class="px-6 py-2 text-left whitespace-nowrap">Dibuat Pada</th>
                        <th class="px-6 py-2 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse ($categories as $category)
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ $category->code }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ $category->name }}
                            </td>
                            <td class="px-6 py-4 text-sm break-words">
                                {{ $category->description }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ $category->created_at->format('d-m-Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:button variant="danger" icon="trash"
                                    wire:click="confirmDelete({{ $category->id }})">
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-4 text-center" colspan="5">
                                Tidak ada kategori
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $categories->links() }}
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
