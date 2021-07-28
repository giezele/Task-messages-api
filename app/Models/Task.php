<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = "tasks";

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'user_id',
        // 'owner'
    ];

    /**
     * The user that belong to the task.
     * 1 user has many tasks
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
