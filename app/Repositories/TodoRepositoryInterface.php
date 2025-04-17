<?php

namespace App\Repositories;

use App\Models\Todo;

interface TodoRepositoryInterface
{
    public function getAllTodosByUser(int $userId);

    public function findTodoById(int $id): ?Todo;

    public function createTodo(array $data): Todo;

    public function updateTodo(Todo $todo, array $data): bool;

    public function deleteTodo(Todo $todo): bool;
}
