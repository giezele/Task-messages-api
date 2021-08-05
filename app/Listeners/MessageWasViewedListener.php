<?php

namespace App\Listeners;

use App\Events\MessageWasViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use JWTAuth;

class MessageWasViewedListener
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
     * @param  MessageWasViewed  $event
     * @return void
     */
    public function handle(MessageWasViewed $event)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $current_timestamp = Carbon::now()->toDateTimeString();

        $messageinfo = $event->message;

        $messageViewed = DB::table('message_log')->insert(
            [   'message_id' => $event->message->id,
                'task_id' => $messageinfo->task_id,
                'log_type' => 'viewed',
                'subject' => $messageinfo->subject, 
                'message_owner' => $messageinfo->user_id, 
                'seen_by' => $user->id,
                'viewed_at' => $current_timestamp,
                'created_at' => $messageinfo->created_at, 
                'updated_at' => $messageinfo->updated_at,
            ]
        );
        return $messageViewed;
    }
}
