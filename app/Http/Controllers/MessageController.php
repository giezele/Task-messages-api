<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Events\MessageUpdated;
use App\Events\MessageDeleted;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Transformers\MessageTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Models\User;
use App\Models\Task;
use JWTAuth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var MessageTransformer
     */
    private $MessageTransformer;

    function __construct(Manager $fractal, MessageTransformer $messageTransformer)
    {
        $this->fractal = $fractal;
        $this->messageTransformer = $messageTransformer;
        $this->middleware('jwt.auth' , ['only' => ['store', 'index', 'show', 'update', 'destroy']]); 
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $task)
    {
        $user = JWTAuth::parseToken()->authenticate();

        // $task = Task::find($request);
           
        $messages = Message::where('task_id', $task)->get();
       
        $response = fractal()
            ->collection($messages)
            ->transformWith(new MessageTransformer)
            ->paginateWith(new IlluminatePaginatorAdapter($messages))
            ->toArray();

    return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Task $task)
    {
        //

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show($message, Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $message)
    {
        $message = Message::findOrFail($message);

        $this->authorize('update', $message);

        $this->validate($request, [
            'subject' => 'required|string|max:255',
            'message' => 'string|max:4096',
        ]);

        $message->update($request->all());

        event(new MessageUpdated($message));

        $response = fractal()
            ->item($message)
            ->transformWith(new MessageTransformer)
            ->toArray();
    
        return response()->json($response, 200); 

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);
        $message->delete();
        
        event(new MessageDeleted($message));

        return response()->json([
            'success' => true,
            'message' => 'message deleted successfully'
        ], 200);
    }

}
