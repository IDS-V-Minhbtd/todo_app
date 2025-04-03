<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class CheckTodoOwner
{
    /**
     * Xử lý request.
     */
    public function handle(Request $request, Closure $next)
    {
        $todo = $request->route('todo'); // Lấy Todo từ route

        if ($todo && $todo->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        return $next($request);
    }
}
