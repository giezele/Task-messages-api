<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;
use Illuminate\Support\Facades\App;
use League\Fractal\ParamBag;

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
        'tasks'
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'name' => $user->name,
            'email' => $user->email
        ];
    }

    // public function includeTasks(User $user, ParamBag $paramBag)
    // {
    //     if (!$user->task) {
    //         return null;
    //     }

    //     // return $this->item($user->tasks, App::make(TaskTransformer::class));//for 1 task
    //     return $this->collection($user->tasks, App::make(TaskTransformer::class));

    // }

    public function includeTasks(User $user, ParamBag $paramBag)
    {
        list($orderCol, $orderBy) = $paramBag->get('order') ?: ['created_at', 'desc'];

        $tasks = $user->tasks()->orderBy($orderCol, $orderBy)->get();

        return $this->collection($tasks, App::make(TaskTransformer::class));
    }
}
