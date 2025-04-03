@extends('adminlte::page')

@section('title', 'Chi tiết công việc')

@section('content_header')
    <h1>Chi tiết công việc</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('todos.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
        <div class="card-body">
            <h3>Tiêu đề: {{ $todo->title }}</h3>
            <p><strong>Mô tả:</strong> {{ $todo->description }}</p>
            <p><strong>Hạn chót:</strong> {{ $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->format('d/m/Y') : 'Không có' }}</p>
            <p><strong>Ưu tiên:</strong> 
                <span class="badge bg-{{ $todo->priority == 'high' ? 'danger' : ($todo->priority == 'medium' ? 'warning' : 'success') }}">
                    {{ ucfirst($todo->priority) }}
                </span>
            </p>
            <p><strong>Trạng thái:</strong> 
                <span class="badge bg-{{ $todo->is_completed ? 'success' : 'secondary' }}">
                    {{ $todo->is_completed ? 'Hoàn thành' : 'Chưa xong' }}
                </span>
            </p>
            <form action="{{ route('todos.update-status', $todo->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="is_completed" value="{{ $todo->is_completed ? 0 : 1 }}">                <button type="submit" class="btn btn-{{ $todo->is_completed ? 'secondary' : 'success' }}">                    {{ $todo->is_completed ? 'Đánh dấu chưa xong' : 'Đánh dấu hoàn thành' }}                </button>            </form>        </div>    </div>@stop