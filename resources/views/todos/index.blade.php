@extends('adminlte::page')

@section('title', 'Danh sách công việc')

@section('content_header')
    <h1>Danh sách công việc</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('todos.create') }}" class="btn btn-primary">Thêm công việc</a>
            <form method="GET" action="{{ route('todos.index') }}">
                <input type="text" name="search" placeholder="Search todos..." value="{{ request('search') }}">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Hạn chót</th>
                        <th>Ưu tiên</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todos as $todo)
                        <tr>
                            <td>{{ $todo->title }}</td>
                            <td>{{ $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->format('d/m/Y') : 'Không có' }}</td>
                            <td>
                                <span class="badge bg-{{ $todo->priority == 'high' ? 'danger' : ($todo->priority == 'medium' ? 'warning' : 'success') }}">
                                    {{ ucfirst($todo->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $todo->is_completed ? 'success' : 'secondary' }}">
                                    {{ $todo->is_completed ? 'Hoàn thành' : 'Chưa xong' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('todos.show', $todo->id) }}" class="btn btn-info btn-sm">Xem</a>
                                <a href="{{ route('todos.edit', $todo->id) }}" class="btn btn-warning btn-sm">Sửa</a>

                                <form action="{{ route('todos.update-status', $todo->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_completed" value="{{ $todo->is_completed ? 0 : 1 }}">
                                    <button type="submit" class="btn btn-{{ $todo->is_completed ? 'secondary' : 'success' }} btn-sm">
                                        {{ $todo->is_completed ? 'Đánh dấu chưa xong' : 'Đánh dấu hoàn thành' }}
                                    </button>
                                </form>

                                <form action="{{ route('todos.destroy', $todo->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Không có công việc nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
