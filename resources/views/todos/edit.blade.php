@extends('adminlte::page')

@section('title', 'Sửa công việc')

@section('content_header')
    <h1>Sửa công việc</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('todos.update', $todo->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="{{ $todo->title }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control">{{ $todo->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hạn chót</label>
                    <input type="date" name="deadline" class="form-control" value="{{ $todo->deadline }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Ưu tiên</label>
                    <select name="priority" class="form-control">
                        <option value="low" {{ $todo->priority == 'low' ? 'selected' : '' }}>Thấp</option>
                        <option value="medium" {{ $todo->priority == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="high" {{ $todo->priority == 'high' ? 'selected' : '' }}>Cao</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Cập nhật</button>
                <a href="{{ route('todos.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@stop
