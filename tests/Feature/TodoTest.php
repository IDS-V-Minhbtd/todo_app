<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void 
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test kiểm tra guest (chưa đăng nhập) không thể xem danh sách todo */
    public function guest_user_cant_see_todo()
    {
        $response = $this->get('/todos');

        $response->assertStatus(302); // Redirect về trang đăng nhập nếu chưa login
        $response->assertRedirect('/login');
    }

    /** @test kiểm tra user không thể xem todo của người khác */
    public function user_cant_see_other_user_todo()
    {
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Test Todo',
            'description' => 'This is a test todo.',
            'is_completed' => false,
            'deadline' => now()->addDays(7),
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user)->get('/todos/' . $todo->id);
        $response->assertStatus(403); // Forbidden nếu đã cài đặt policy
    }

    /** @test kiểm tra user có thể xem danh sách todo */
    public function user_can_see_todo()
    {
        $response = $this->actingAs($this->user)->get('/todos');

        $response->assertStatus(200);
        $response->assertViewHas('todos');
    }

    /** @test kiểm tra user có thể tạo todo */
    public function user_can_create_todo()
    {
        $response = $this->actingAs($this->user)->post('/todos', [
            'title' => 'Test Todo',
            'description' => 'This is a test todo.',
            'is_completed' => false,
            'deadline' => now()->addDays(7),
            'priority' => 'medium',
        ]);

        $response->assertStatus(302); // Redirect sau khi tạo
        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'description' => 'This is a test todo.',
            'is_completed' => false,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test kiểm tra validation khi tạo todo */
    public function user_create_todo_with_empty_fields()
    {
        $response = $this->actingAs($this->user)->post('/todos', [
            'title' => '',
            'description' => '',
            'is_completed' => false,
            'deadline' => '',
            'priority' => '',
        ]);

        $response->assertStatus(302); 
        $response->assertSessionHasErrors(['title', 'priority']);
    }

    /** @test kiểm tra user có thể xóa todo */
    public function user_can_delete_todo()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Todo',
            'description' => 'This is a test todo.',
            'is_completed' => false,
            'deadline' => now()->addDays(7),
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user)->delete('/todos/' . $todo->id);

        $response->assertStatus(302);
        $response->assertRedirect('/todos');

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    /** @test kiểm tra user có thể cập nhật todo */
    public function user_can_update_todo()
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Todo',
            'description' => 'This is a test todo.',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'priority' => 'medium',
            'is_completed' => false,
        ]);

        $updatedDeadline = now()->addDays(14);

        $response = $this->actingAs($this->user)->put("/todos/{$todo->id}", [
            'title' => 'Test Todo got updated',
            'description' => 'This is a test todo2.',
            'deadline' => $updatedDeadline->format('Y-m-d'),
            'priority' => 'high',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('todos.index'));

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'user_id' => $this->user->id,
            'title' => 'Test Todo got updated',
            'description' => 'This is a test todo2.',
            'deadline' => $updatedDeadline->format('Y-m-d'),
            'priority' => 'high',
            'is_completed' => 0,
        ]);
    }

}
