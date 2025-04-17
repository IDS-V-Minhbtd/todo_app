<?php

namespace App\Services;

use App\Repositories\TodoRepositoryInterface; // Correct casing
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TodoService
{
    protected $todoRepository;

    public function __construct(TodoRepositoryInterface $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    public function getTodosForCurrentUser($search = null)
    {
        $userId = Auth::id();
        $todos = $this->todoRepository->getAllTodosByUser($userId);

        if ($search) {
            $todos = $todos->filter(function ($todo) use ($search) {
                return stripos($todo->title, $search) !== false;
            });
        }

        return $todos;
    }

    public function createTodoForCurrentUser(array $data)
    {
        $data['user_id'] = Auth::id();
        $data['deadline'] = Carbon::parse($data['deadline']);
        return $this->todoRepository->createTodo($data);
    }

    public function updateTodo($todoId, array $data)
    {
        $todo = $this->todoRepository->findTodoById($todoId);

        if (!$todo) {
            throw new \Exception('Todo not found');
        }

        if (isset($data['deadline'])) {
            $data['deadline'] = Carbon::parse($data['deadline']);
        }

        return $this->todoRepository->updateTodo($todo, $data);
    }

    public function deleteTodoById($todoId)
    {
        $todo = $this->todoRepository->findTodoById($todoId);
        return $this->todoRepository->deleteTodo($todo);
    }

    public function updateTodoStatus($id, $isCompleted)
    {
        $todo = $this->todoRepository->findById($id);

        if (!$todo) {
            return false;
        }

        $todo->update(['is_completed' => $isCompleted]);

        return true;
    }

    public function getTodoById($id)
    {
        return $this->todoRepository->findById($id);
    }

    public function searchTodosForCurrentUser($search)
    {
        $userId = Auth::id();
        return $this->todoRepository->searchTodosByUser($userId, $search);
    }
}
