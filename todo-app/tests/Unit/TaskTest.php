<?php

use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a task with title only', function () {
    $task = Task::create(['title' => 'Buy groceries']);

    expect($task->title)->toBe('Buy groceries')
        ->and($task->description)->toBeNull()
        ->and((bool) $task->completed)->toBeFalse()
        ->and($task->category_id)->toBeNull();
});

it('creates task with description as null by default', function () {
    $task = Task::create(['title' => 'Some task']);

    expect($task->description)->toBeNull();
});

it('can update a task title and description', function () {
    $task = Task::create(['title' => 'Original title']);

    $task->update([
        'title' => 'Updated title',
        'description' => 'A helpful description',
    ]);

    expect($task->fresh()->title)->toBe('Updated title')
        ->and($task->fresh()->description)->toBe('A helpful description');
});

it('can toggle a task as completed', function () {
    $task = Task::create(['title' => 'Pending task']);

    $task->update(['completed' => true]);

    expect($task->fresh()->completed)->toBeTrue();
});

it('can delete a task', function () {
    $task = Task::create(['title' => 'Task to delete']);
    $id = $task->id;

    $task->delete();

    expect(Task::find($id))->toBeNull();
});

it('can retrieve a task with its category', function () {
    $category = Category::create(['name' => 'Work', 'color' => '#3B82F6']);
    $task = Task::create(['title' => 'Write report', 'category_id' => $category->id]);

    $loaded = Task::with('category')->find($task->id);

    expect($loaded->category)->not->toBeNull()
        ->and($loaded->category->name)->toBe('Work');
});

it('filters only pending tasks using scope', function () {
    Task::create(['title' => 'Pending', 'completed' => false]);
    Task::create(['title' => 'Done', 'completed' => true]);

    $pending = Task::pending()->get();

    expect($pending)->toHaveCount(1)
        ->and($pending->first()->title)->toBe('Pending');
});

it('filters only completed tasks using scope', function () {
    Task::create(['title' => 'Pending', 'completed' => false]);
    Task::create(['title' => 'Done', 'completed' => true]);

    $completed = Task::completed()->get();

    expect($completed)->toHaveCount(1)
        ->and($completed->first()->title)->toBe('Done');
});