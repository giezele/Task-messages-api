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
        // 'assignee_id'
    ];

    /**
     * a task must belong to creator []
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the users that have been assigned to task
     * @return [type] [description]
     */
    public function assignee() {
    	return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * A task has one or many subscriptions
     * @return [type] [description]
     */
    public function subscriptions() {
        
        return $this->hasMany(TaskSubscription::class);
    }
}
