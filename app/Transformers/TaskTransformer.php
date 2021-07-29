<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Task;
use Illuminate\Support\Facades\App;

class TaskTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'user'
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Task $task)
    {
        return [
            'id' => $task->id,
            'name' => $task->name,
            'description'=> $task->description,
            'type'=> $task->type,
            'status'=> $task->status,
            // 'user_id' => $task->user->id,
            // 'assignee_id' => $task->assignee->id
        ];
    }

    public function includeUser(Task $task)
    {
        if (!$task->user) {
            return null;
        }
        return $this->item($task->user, App::make(UserTransformer::class));
    }
}
