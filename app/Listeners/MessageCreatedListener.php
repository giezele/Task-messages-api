<?php

namespace App\Listeners;

use App\Events\MessageCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use JWTAuth;

class MessageCreatedListener
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
     * @param  MessageCreated  $event
     * @return void
     */
    public function handle(MessageCreated $event)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $current_timestamp = Carbon::now()->toDateTimeString();

        $messageinfo = $event->message;

        $messageCreated = DB::table('message_log')->insert(
            [   'message_id' => $event->message->id,
                'task_id' => $messageinfo->task_id,
                'log_type' => 'created',
                'subject' => $messageinfo->subject,
                'message_owner' => $messageinfo->user_id, 
                // 'seen_by' => $user->id,
                // 'viewed_at' => $current_timestamp,
                'created_at' => $current_timestamp, 
                'updated_at' => $messageinfo->updated_at, 
            ]
        );
        return $messageCreated;
    }
}
