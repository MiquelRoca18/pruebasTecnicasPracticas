<?php

use App\Models\Category;
use Livewire\Volt\Component;

new class extends Component
{
    public string $newName = '';
    public string $newColor = '#3B82F6';
    public bool $showAll = false;

    public function rules(): array
    {
        return [
            'newName' => 'required|string|min:1|max:50|unique:categories,name',
            'newColor' => 'required|string',
        ];
    }

    public function with(): array
    {
        $query = Category::orderBy('name');

        return [
            'categories' => $this->showAll ? $query->get() : $query->limit(4)->get(),
            'totalCount' => Category::count(),
        ];
    }

    public function createCategory(): void
    {
        $this->validate();

        Category::create([
            'name' => $this->newName,
            'color' => $this->newColor,
        ]);

        $this->reset('newName', 'newColor');
        $this->dispatch('toast', message: 'Categoría creada.', type: 'success');
    }

    public function deleteCategory(int $id): void
    {
        Category::findOrFail($id)->delete();
        $this->dispatch('category-deleted');
        $this->dispatch('toast', message: 'Categoría eliminada.', type: 'error');
    }
};
?>

<div class="space-y-4">

    {{-- Añadir categoría --}}
    <div class="bg-white rounded-2xl shadow-lg p-6" x-data="{ open: false }">
        <button
            @click="open = !open"
            class="w-full flex items-center gap-3 px-4 py-3 border border-gray-200 rounded-xl text-gray-400 hover:border-blue-400 hover:text-blue-500 transition text-sm font-medium"
        >
            📋 Añadir una Categoría
        </button>

        <div x-show="open" x-transition @click.outside="open = false" class="mt-3 space-y-3">
            <div>
                <input
                    type="text"
                    wire:model="newName"
                    placeholder="Nombre de la categoría..."
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    @keydown.enter="$wire.createCategory().then(() => open = false)"
                />
                @error('newName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <label class="text-sm text-gray-500">Color</label>
                <input type="color" wire:model="newColor"
                    class="w-10 h-8 rounded cursor-pointer border border-gray-200" />
            </div>

            <div class="flex justify-end gap-2">
                <button @click="open = false"
                    class="px-3 py-1.5 text-sm text-gray-500">Cancelar</button>
                <button
                    @click="$wire.createCategory().then(() => open = false)"
                    class="px-4 py-1.5 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                    Añadir
                </button>
            </div>
        </div>
    </div>

    {{-- Lista de categorías --}}
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="font-bold text-gray-700 mb-4">Lista de categorías</h3>

        <div class="flex flex-wrap gap-2">
            @forelse ($categories as $category)
                <div wire:key="cat-{{ $category->id }}" class="flex items-center gap-1">
                    <span
                        class="px-3 py-1 rounded-full text-white text-xs font-bold"
                        style="background-color: {{ $category->color }}"
                    >
                        {{ $category->name }}
                    </span>
                    <button
                        wire:click="deleteCategory({{ $category->id }})"
                        class="text-gray-300 hover:text-red-400 transition text-xs"
                    >✕</button>
                </div>
            @empty
                <p class="text-sm text-gray-400">No hay categorías aún.</p>
            @endforelse
        </div>

        @if ($totalCount > 4)
            <button
                wire:click="$toggle('showAll')"
                class="mt-3 text-sm text-gray-400 border border-dashed border-gray-300 rounded-lg px-4 py-1 hover:border-blue-400 hover:text-blue-400 transition"
            >
                {{ $showAll ? 'ver menos' : 'ver más' }}
            </button>
        @endif
    </div>

</div>