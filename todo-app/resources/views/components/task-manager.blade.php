<?php

use App\Models\Task;
use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public string $newTitle = '';
    public ?int $newCategoryId = null;

    public string $filter = 'all';
    public string $search = '';
    public bool $showSearch = false;

    // Edit
    public ?int $editingTaskId = null;
    public string $editTitle = '';
    public string $editDescription = '';
    public ?int $editCategoryId = null;

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

    public function startEditing(int $taskId): void
    {
        $task = Task::findOrFail($taskId);
        $this->editingTaskId = $taskId;
        $this->editTitle = $task->title;
        $this->editDescription = $task->description ?? '';
        $this->editCategoryId = $task->category_id;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editTitle' => 'required|string|min:1|max:255',
            'editDescription' => 'nullable|string',
            'editCategoryId' => 'nullable|exists:categories,id',
        ]);

        Task::findOrFail($this->editingTaskId)->update([
            'title' => $this->editTitle,
            'description' => $this->editDescription ?: null,
            'category_id' => $this->editCategoryId,
        ]);

        $this->reset('editingTaskId', 'editTitle', 'editDescription', 'editCategoryId');
    }

    public function cancelEdit(): void
    {
        $this->reset('editingTaskId', 'editTitle', 'editDescription', 'editCategoryId');
    }

    public function deleteTask(int $taskId): void
    {
        Task::findOrFail($taskId)->delete();
    }
    #[On('category-deleted')]
    public function refreshTasks(): void
    {
        // El with() se re-ejecuta automáticamente, no hace falta código aquí
    }
};
?>
<div>
    {{-- Panel principal --}}
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
                <li class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <input
                            type="checkbox"
                            wire:click="toggleComplete({{ $task->id }})"
                            {{ $task->completed ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-blue-500 cursor-pointer flex-shrink-0"
                        />
                        <span class="{{ $task->completed ? 'line-through text-gray-400' : 'text-gray-800' }} text-sm font-medium truncate">
                            {{ $task->title }}
                        </span>
                        @if ($task->category)
                            <span class="px-2 py-0.5 rounded-full text-xs text-white font-semibold flex-shrink-0"
                                style="background-color: {{ $task->category->color }}">
                                {{ $task->category->name }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                        <button
                            @click="$dispatch('show-task-detail', {
                                title: '{{ addslashes($task->title) }}',
                                description: '{{ addslashes($task->description ?? '') }}',
                                completed: {{ $task->completed ? 'true' : 'false' }},
                                category: '{{ addslashes($task->category?->name ?? '') }}'
                            })"
                            class="text-gray-400 hover:text-blue-500 transition"
                        >
                            👁️
                        </button>

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false"
                                class="text-gray-400 hover:text-gray-600 transition text-lg leading-none">
                                ···
                            </button>
                            <div x-show="open" x-transition
                                class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-20">
                                <button
                                    wire:click="startEditing({{ $task->id }})"
                                    @click="open = false; $dispatch('open-edit-modal')"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    ✏️ Editar
                                </button>
                                <button
                                    @click="open = false; $dispatch('confirm-delete', { id: {{ $task->id }} })"
                                    class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50">
                                    🗑️ Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="text-center py-10 text-gray-400 text-sm">No hay tareas.</li>
            @endforelse
        </ul>
    </div>

    {{-- Modal Detalle --}}
    <div
        x-data="{ open: false, task: null }"
        x-on:show-task-detail.window="task = $event.detail; open = true"
        x-on:keydown.escape.window="open = false"
        x-cloak x-data="{ open: false, task: null }"
    >
        <div x-show="open" x-transition.opacity
            class="fixed inset-0 bg-black/50 z-30 flex items-center justify-center p-4">
            <div @click.outside="open = false"
                class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md space-y-3">
                <h3 class="font-bold text-gray-800 text-lg" x-text="task?.title"></h3>
                <p class="text-sm text-gray-500" x-text="task?.description || 'Sin descripción.'"></p>
                <div class="flex gap-2">
                    <span x-show="task?.category"
                        class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium"
                        x-text="task?.category"></span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                        :class="task?.completed ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                        x-text="task?.completed ? 'Completada' : 'Pendiente'"></span>
                </div>
                <div class="flex justify-end pt-2">
                    <button @click="open = false"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Editar --}}
    <div
        x-data="{ open: false }"
        x-on:open-edit-modal.window="open = true"
        x-on:keydown.escape.window="open = false"
        x-cloak x-data="{ open: false, task: null }"
    >
        <div x-show="open" x-transition.opacity
            class="fixed inset-0 bg-black/50 z-30 flex items-center justify-center p-4">
            <div @click.outside="open = false; $wire.cancelEdit()"
                class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md space-y-4">
                <h3 class="font-bold text-gray-800 text-lg">Editar Tarea</h3>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Título</label>
                    <input type="text" wire:model="editTitle"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"/>
                    @error('editTitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Descripción</label>
                    <textarea wire:model="editDescription" rows="3"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Categoría</label>
                    <select wire:model="editCategoryId"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Sin categoría</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button @click="open = false; $wire.cancelEdit()"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">
                        Cancelar
                    </button>
                    <button
                        @click="$wire.saveEdit().then(() => open = false)"
                        class="px-5 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Confirmar Eliminar --}}
    <div
        x-data="{ open: false, taskId: null }"
        x-on:confirm-delete.window="open = true; taskId = $event.detail.id"
        x-on:keydown.escape.window="open = false"
        x-cloak x-data="{ open: false, task: null }"
    >
        <div x-show="open" x-transition.opacity
            class="fixed inset-0 bg-black/50 z-30 flex items-center justify-center p-4">
            <div @click.outside="open = false"
                class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center space-y-4">
                <div class="text-4xl">🗑️</div>
                <h3 class="font-bold text-gray-800">¿Eliminar esta tarea?</h3>
                <p class="text-sm text-gray-500">Esta acción no se puede deshacer.</p>
                <div class="flex gap-3 justify-center pt-2">
                    <button @click="open = false"
                        class="px-4 py-2 border border-gray-200 text-sm text-gray-600 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button
                        @click="$wire.deleteTask(taskId); open = false"
                        class="px-5 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>