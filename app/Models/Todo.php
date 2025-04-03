<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_completed',
        'deadline',
        'priority',
    ];

    /**
     * Liên kết Todo với User (Mỗi Todo thuộc về một User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
