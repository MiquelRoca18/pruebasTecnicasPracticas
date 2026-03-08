<?php

use App\Models\Task;
use App\Models\Category;
use Livewire\Volt\Component;

new class extends Component
{
    public string $newTitle = '';
    public ?int $newCategoryId = null;

    public string $filter = 'all';
    public string $search = '';
    public bool $showSearch = false;

    public function rules(): array
    {
        return [
            'newTitle' => 'required|string|min:1|max:255',
            'newCategoryId' => 'nullable|exists:categories,id',
        ];
    }

    public function with(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'tasks' => $this->loadTasks(),
        ];
    }

    private function loadTasks()
    {
        $query = Task::with('category')->orderBy('order');

        $query->when($this->filter === 'pending', fn($q) => $q->where('completed', false));
        $query->when($this->filter === 'completed', fn($q) => $q->where('completed', true));
        $query->when($this->search !== '', fn($q) => $q->where('title', 'like', "%{$this->search}%"));

        return $query->get();
    }

    public function createTask(): void
    {
        $this->validate();

        Task::create([
            'title' => $this->newTitle,
            'description' => null,
            'category_id' => $this->newCategoryId,
            'order' => Task::max('order') + 1,
        ]);

        $this->reset('newTitle', 'newCategoryId');
    }

    public function toggleComplete(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $task->update(['completed' => !$task->completed]);
    }
};
?>

<div class="bg-white rounded-2xl shadow-lg p-6 space-y-4">

    {{-- Formulario crear tarea --}}
    <div x-data="{ open: false }">
        <button
            @click="open = !open"
            class="w-full flex items-center gap-3 px-4 py-3 border border-gray-200 rounded-xl text-gray-400 hover:border-blue-400 hover:text-blue-500 transition text-sm font-medium"
        >
            📋 Añadir una Tarea
        </button>

        <div x-show="open" x-transition @click.outside="open = false"
            class="mt-2 bg-white rounded-xl border border-gray-100 p-4 space-y-3 shadow-md">

            <div>
                <input
                    type="text"
                    wire:model="newTitle"
                    placeholder="Título de la tarea..."
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                />
                @error('newTitle')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <select
                wire:model="newCategoryId"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
                <option value="">Sin categoría</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <div class="flex justify-end gap-2">
                <button @click="open = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">
                    Cancelar
                </button>
                <button
                    wire:click="createTask"
                    @click="$wire.createTask().then(() => open = false)"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                    Añadir
                </button>
            </div>
        </div>
    </div>

    {{-- Filtros + Búsqueda --}}
    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
        <div class="flex gap-5 text-sm font-medium">
            @foreach (['all' => 'Todas', 'pending' => 'Pendiente', 'completed' => 'Completadas'] as $key => $label)
                <button
                    wire:click="$set('filter', '{{ $key }}')"
                    class="{{ $filter === $key ? 'text-blue-500 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }} pb-1 transition"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div x-data="{ open: @entangle('showSearch') }">
            <button @click="open = !open" class="text-gray-400 hover:text-blue-500 transition">
                🔍
            </button>
            <div x-show="open" x-transition class="mt-2">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Buscar tareas..."
                    class="px-3 py-1 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 w-48"
                />
            </div>
        </div>
    </div>

    {{-- Lista de tareas --}}
    <ul class="space-y-1 min-h-[200px]">
        @forelse ($tasks as $task)
            <li class="flex items-center gap-3 py-3 border-b border-gray-100">
                <input
                    type="checkbox"
                    wire:click="toggleComplete({{ $task->id }})"
                    {{ $task->completed ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-300 text-blue-500 cursor-pointer"
                />
                <span class="{{ $task->completed ? 'line-through text-gray-400' : 'text-gray-800' }} text-sm font-medium">
                    {{ $task->title }}
                </span>
                @if ($task->category)
                    <span class="px-2 py-0.5 rounded-full text-xs text-white font-semibold"
                        style="background-color: {{ $task->category->color }}">
                        {{ $task->category->name }}
                    </span>
                @endif
            </li>
        @empty
            <li class="text-center py-10 text-gray-400 text-sm">No hay tareas.</li>
        @endforelse
    </ul>

</div>