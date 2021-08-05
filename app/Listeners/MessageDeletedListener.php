<?php

namespace App\Listeners;

use App\Events\MessageDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class MessageDeletedListener
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
     * @param  MessageDeleted  $event
     * @return void
     */
    public function handle(MessageDeleted $event)
    {
        $current_timestamp = Carbon::now()->toDateTimeString();

        $messageinfo = $event->message;

        $messageDeleted = DB::table('message_log')->insert(
            [   'message_id' => $event->message->id,
                'task_id' => $messageinfo->task_id,
                'log_type' => 'deleted',
                'subject' => $messageinfo->subject,
                'message_owner' => $messageinfo->user_id, 
                'created_at' => $messageinfo->created_at, 
                'updated_at' => $messageinfo->updated_at, 
                'deleted_at' => $current_timestamp
            ]
        );
        return $messageDeleted;
    }
}
