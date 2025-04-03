@extends('adminlte::page')

@section('title', 'Thêm công việc')

@section('content_header')
    <h1>Thêm công việc mới</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('todos.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hạn chót</label>
                    <input type="date" name="deadline" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Ưu tiên</label>
                    <select name="priority" class="form-control">
                        <option value="low">Thấp</option>
                        <option value="medium" selected>Trung bình</option>
                        <option value="high">Cao</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Thêm</button>
                <a href="{{ route('todos.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@stop
