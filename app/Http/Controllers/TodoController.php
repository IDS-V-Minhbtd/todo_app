<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
        $this->middleware('checkTodoOwner')->only(['show', 'edit', 'update', 'destroy']); 
    }

    public function index()
    {
        $todos = Todo::where('user_id', Auth::id())->orderBy('priority', 'desc')->get();
        return view('todos.index', compact('todos'));
    }

    public function create()
    {
        return view('todos.create');
    }

    public function store(TodoRequest $request)
    {
        Todo::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => Carbon::parse($request->deadline), // Ensure deadline is a Carbon instance
            'priority' => $request->priority,
        ]);

        return redirect()->route('todos.index')->with('success', 'Công việc đã được thêm!');
    }

    public function show(Todo $todo)
    {
        
        return view('todos.show', compact('todo'));
    }

    public function edit(Todo $todo)
    {
        
        return view('todos.edit', compact('todo'));
    }

    public function update(TodoRequest $request, Todo $todo)
    {
        
        $todo->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => Carbon::parse($request->deadline), 
            'priority' => $request->priority,
        ]);

        return redirect()->route('todos.index')->with('success', 'Công việc đã được cập nhật!');
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();

        return redirect()->route('todos.index')->with('success', 'Công việc đã được xóa!');
    }

    public function updateStatus(Todo $todo, Request $request)
    {
        $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        $todo->is_completed = $request->is_completed;
        $todo->save();

        return redirect()->route('todos.index')->with('success', 'Trạng thái công việc đã được cập nhật!');
    }
}
