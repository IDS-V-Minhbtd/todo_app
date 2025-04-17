<?php

namespace App\Repositories;

use App\Models\Todo;
use App\Repositories\TodoRepositoryInterface;

class TodoRepository implements TodoRepositoryInterface
{
    public function getAllTodosByUser(int $userId)
    {
        return Todo::where('user_id', $userId)->orderBy('priority', 'desc')->get();
    }

    public function findTodoById(int $id): ?Todo
    {
        return Todo::find($id);
    }

    public function createTodo(array $data): Todo
    {
        return Todo::create($data);
    }

    public function updateTodo(Todo $todo, array $data): bool
    {
        return $todo->update($data); // Update the database record
    }

    public function deleteTodo(Todo $todo): bool
    {
        return $todo->delete();
    }

    public function findById($id)
    {
        return Todo::find($id);
    }

    public function searchTodosByUser(int $userId, string $search)
    {
        return Todo::where('user_id', $userId)
            ->where('title', 'like', '%' . $search . '%')
            ->orderBy('priority', 'desc')
            ->get();
    }
}
