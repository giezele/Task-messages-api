<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;
use Illuminate\Support\Facades\App;
use League\Fractal\ParamBag;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\CustomClasses\CollectionPaginate;

class UserTransformer extends TransformerAbstract
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
        'tasks',
        'assignedTasks',
        'messages'
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' =>$user->id,
            'name' => $user->name,
            'email' => $user->email
        ];
    }


    public function includeTasks(User $user, ParamBag $paramBag)
    {
        $tasks = $user->tasks()->get();

        return $this->collection($tasks, App::make(TaskTransformer::class));
    }

    public function includeAssignedTasks(User $user, ParamBag $paramBag)
    {
        $assignedTasks = $user->assigned_tasks()->get();//get grazina collection

        return $this->collection($assignedTasks, App::make(TaskTransformer::class));
    }

    public function includeTaskMessages(User $user){
        $messages = $user->messages();
 
        return $this->collection($messages, new MessageTransformer);
    }
}
