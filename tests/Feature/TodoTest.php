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
        $this->user = User::factory()->create(); // Tạo user và gán vào biến user
    }

    /** @test kiểm tra non user có thể xem todo */
    public function guest_user_cant_see_todo()
    {
        // Gửi request với tư cách người dùng chưa đăng nhập
        $response = $this->get('/todos');

        $response->assertStatus(302); // Trả về thành công
        $response->assertRedirect('/login'); // Chuyển hướng đến trang đăng nhập
    }
    /** @test kiểm tra user không thể xem todo của người khác */
    public function user_cant_see_other_user_todo()
    {
        // Tạo một todo cho một user khác
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Test Todo',
            'description' => 'This is a test todo.',
            'is_completed' => false,
            'deadline' => now()->addDays(7),
            'priority' => 'medium',
        ]);

        // Gửi request với tư cách user đã đăng nhập
        $response = $this->actingAs($this->user)->get('/todos/' . $todo->id);

        $response->assertStatus(403); // Trả về lỗi 403 (Forbidden)
    }

    /** @test kiểm tra user có thể xem danh sách todo */
    public function user_can_see_todo()
    {
        // Gửi request với tư cách user đã đăng nhập
        $response = $this->actingAs($this->user)->get('/todos');

        $response->assertStatus(200); // Trả về thành công
        $response->assertViewHas('todos'); // View có biến todos
    }

    /** @test kiểm tra user có thể tạo todo */
    public function user_can_create_todo()
    {
        // Gửi request với tư cách user đã đăng nhập
        $response = $this->actingAs($this->user)->post('/todos', [
            'title' => 'Test Todo',
            'description' => 'This is a test todo.',
            'is_completed' => false,
            'deadline' => now()->addDays(7),
            'priority' => 'medium',
        ]);

        $response->assertStatus(302); // Trả về thành công
        $this->assertDatabaseHas('todos', [
            'title' => 'Test Todo',
            'description' => 'This is a test todo.',
            'is_completed' => false,
            'user_id' => $this->user->id,
        ]);
    }
    /** @test user để trống ô tạo todo  */
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
        $response->assertSessionHasErrors(['title','priority']); //  title và priority là bắt buộc
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
        $response->assertRedirect('/todos'); // Hoặc tên route tương ứng

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

/** @test */
public function user_can_update_todo()
{
    // Create a Todo owned by the authenticated user
    $todo = Todo::factory()->create([
        'user_id' => $this->user->id,
        'title' => 'Test Todo',
        'description' => 'This is a test todo.',
        'deadline' => now()->addDays(7)->format('Y-m-d'),
        'priority' => 'medium',
        'is_completed' => false,
    ]);

    // Send PUT request to update Todo
    $updatedDeadline = now()->addDays(14);
    $response = $this->actingAs($this->user)->put("/todos/{$todo->id}", [
        'title' => 'Test Todo got updated',
        'description' => 'This is a test todo2.',
        'deadline' => $updatedDeadline->format('Y-m-d'),
        'priority' => 'high',
    ]);

    // Assert response
    $response->assertStatus(302);
    $response->assertRedirect(route('todos.index'));

    // Assert database state
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
