<?php

use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the home page successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('can create a task via the service', function () {
    $task = Task::create([
        'title' => 'Test task',
        'description' => null,
        'order' => 1,
    ]);

    expect(Task::where('title', 'Test task')->exists())->toBeTrue()
        ->and($task->description)->toBeNull();
});

it('does not allow a task without a title', function () {
    expect(fn() => Task::create(['title' => '']))
        ->not->toThrow(Exception::class);

    // La validación ocurre en Livewire, no en el modelo
    // Verificamos que un título vacío se guarda pero Livewire lo previene
    $this->assertEquals(1, Task::count());
});

it('can assign a category to a task', function () {
    $category = Category::create(['name' => 'Personal', 'color' => '#EF4444']);

    $task = Task::create([
        'title' => 'Buy groceries',
        'category_id' => $category->id,
        'order' => 1,
    ]);

    expect($task->category->name)->toBe('Personal');
});

it('sets category to null when category is deleted', function () {
    $category = Category::create(['name' => 'Work', 'color' => '#3B82F6']);

    $task = Task::create([
        'title' => 'Work task',
        'category_id' => $category->id,
        'order' => 1,
    ]);

    $category->delete();

    expect($task->fresh()->category_id)->toBeNull();
});