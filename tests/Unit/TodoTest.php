<?php

namespace Tests\Unit\Services;

use App\Models\Todo;
use App\Services\TodoService;
use App\Repositories\TodoRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Carbon\Carbon;
use Mockery;

class TodoServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_todos_for_current_user()
    {
        Auth::shouldReceive('id')->once()->andReturn(1);

        $mockTodos = collect([
            new Todo(['title' => 'Task 1']),
            new Todo(['title' => 'Task 2']),
        ]);

        $repo = Mockery::mock(TodoRepositoryInterface::class);
        $repo->shouldReceive('getAllTodosByUser')->once()->with(1)->andReturn($mockTodos);

        $service = new TodoService($repo);
        $result = $service->getTodosForCurrentUser();

        $this->assertCount(2, $result);
    }

    /** @test */
    public function it_can_create_todo_for_current_user()
    {
        Auth::shouldReceive('id')->once()->andReturn(2);

        $todo = new Todo(['title' => 'New Task']);
        $repo = Mockery::mock(TodoRepositoryInterface::class);
        $repo->shouldReceive('createTodo')->once()->andReturn($todo);

        $service = new TodoService($repo);
        $result = $service->createTodoForCurrentUser([
            'title' => 'New Task',
            'deadline' => now()->toDateString(),
        ]);

        $this->assertEquals('New Task', $result->title);
    }

    /** @test */
    public function it_can_update_a_todo()
    {
        $todo = new Todo(['title' => 'Old']);
        $repo = Mockery::mock(TodoRepositoryInterface::class);
        $repo->shouldReceive('findTodoById')->once()->with(1)->andReturn($todo);
        $repo->shouldReceive('updateTodo')->once()->with($todo, Mockery::type('array'))->andReturnTrue();

        $service = new TodoService($repo);
        $result = $service->updateTodo(1, ['title' => 'Updated', 'deadline' => now()->toDateString()]);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_delete_a_todo()
    {
        $todo = new Todo();
        $repo = Mockery::mock(TodoRepositoryInterface::class);
        $repo->shouldReceive('findTodoById')->once()->with(5)->andReturn($todo);
        $repo->shouldReceive('deleteTodo')->once()->with($todo)->andReturnTrue();

        $service = new TodoService($repo);
        $result = $service->deleteTodoById(5);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_update_todo_status()
    {
        $todo = Mockery::mock(Todo::class);
        $todo->shouldReceive('update')->once()->with(['is_completed' => true]);

        $repo = Mockery::mock(TodoRepositoryInterface::class);
        $repo->shouldReceive('findById')->once()->with(3)->andReturn($todo);

        $service = new TodoService($repo);
        $result = $service->updateTodoStatus(3, true);

        $this->assertTrue($result);
    }



    /** @test */
    public function it_can_search_todo_by_user()
    {
        Auth::shouldReceive('id')->once()->andReturn(1);

        $mockTodos = collect([new Todo(['title' => 'Search task'])]);

        $repo = Mockery::mock(TodoRepositoryInterface::class);
        $repo->shouldReceive('searchTodosByUser')->once()->with(1, 'Search')->andReturn($mockTodos);

        $service = new TodoService($repo);
        $result = $service->searchTodosForCurrentUser('Search');

        $this->assertCount(1, $result);
    }
}
