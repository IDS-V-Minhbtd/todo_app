<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoRequest;
use App\Services\TodoService;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    protected $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->middleware('auth');
        $this->middleware('checkTodoOwner')->only(['show', 'edit', 'update', 'destroy']);
        $this->todoService = $todoService;
    }

    public function index(Request $request)
    {
        $todos = $this->todoService->getTodosForCurrentUser($request->search);
        return view('todos.index', compact('todos'));
    }

    public function create()
    {
        return view('todos.create');
    }

    public function store(TodoRequest $request)
    {
        $this->todoService->createTodoForCurrentUser($request->validated());
        return redirect()->route('todos.index')->with('success', 'Công việc đã được thêm!');
    }

    public function show($id)
    {
        $todo = $this->todoService->getTodoById($id);
        return view('todos.show', compact('todo'));
    }

    public function edit($id)
    {
        $todo = $this->todoService->getTodoById($id);
        return view('todos.edit', compact('todo'));
    }

    public function update(TodoRequest $request, $id)
    {
        $this->todoService->updateTodo($id, $request->validated());
        return redirect()->route('todos.index')->with('success', 'Công việc đã được cập nhật!');
    }

    public function destroy($id)
    {
        $this->todoService->deleteTodoById($id);
        return redirect()->route('todos.index')->with('success', 'Công việc đã được xóa!');
    }

    public function updateStatus(Request $request, $id)
    {
        $isUpdated = $this->todoService->updateTodoStatus($id, $request->input('is_completed', false));

        if (!$isUpdated) {
            return redirect()->route('todos.index')->with('error', 'Todo not found.');
        }

        return redirect()->route('todos.index')->with('success', 'Todo status updated successfully!');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $todos = $this->todoService->searchTodosForCurrentUser($search);

        return view('todos.index', compact('todos'));
    }
}
