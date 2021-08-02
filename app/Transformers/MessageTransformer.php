<?php

namespace App\Transformers;

use App\Models\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
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
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Message $message)
    {
        return [
            'id' => $message->id,
            'subject' => $message->subject,
            'message'=> $message->message,
            'user_id' => $message->user_id,
            'task_id' => $message->task_id,
            'created_at' => $message->created_at->diffForHumans()
        ];
    }
}
