<?php

namespace App\Listeners;

use App\Events\MessageUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;


class MessageUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageUpdated  $event
     * @return void
     */
    public function handle(MessageUpdated $event)
    {
        $messageinfo = $event->message;

        $messageUpdated = DB::table('message_log')->insert(
            [   'message_id' => $event->message->id,
                'task_id' => $messageinfo->task_id,
                'log_type' => 'updated',
                'subject' => $messageinfo->subject,
                'message_owner' => $messageinfo->user_id, 
                'created_at' => $messageinfo->created_at, 
                'updated_at' => $messageinfo->updated_at, 
            ]
        );
        return $messageUpdated;
    }
}
