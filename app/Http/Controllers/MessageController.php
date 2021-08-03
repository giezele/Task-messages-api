<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\MessageTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Fractal\Fractal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Task;
use JWTAuth;
use DB;

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
        $user = JWTAuth::parseToken()->authenticate();

        // $this->authorize('create', Task::class);

        $this->validate($request, [
            'subject' => 'required|string|max:255',
            'message' => 'string|max:4096',
        ]);

        $data = [
            'subject' => $request->subject,
            'message'=> $request->message,
            
        ];

        $message = Message::create($data);
        $message->user()->associate(Auth::id());
        $message->task()->associate(Task::find($request->input('task_id')));
        $message->save();
        
        $response = fractal()
            ->item($message)
            ->transformWith(new MessageTransformer)
            // ->includeUser()
            // ->includeAssignee()
            ->toArray();

        return response()->json($response, 201);

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
        
        return response()->json([
            'success' => true,
            'message' => 'message deleted successfully'
        ], 200);
    }

    public function addMessage(Request $request, Message $message){
        $user = JWTAuth::parseToken()->authenticate();

        $this->validate($request, [
            'subject' => 'required|string|max:255',
            'message' => 'string|max:4096',
        ]);

        $data = [
            'subject' => $request->subject,
            'message'=> $request->message,
            
        ];

        $message = new Message;
        $message->subject = $request->get('subject');
        $message->message = $request->get('message');
        $message->user()->associate($request->user());
        $task = Task::find($request->get('task_id'));
        $task->messages()->save($message);
        
        $response = fractal()
            ->item($message)
            ->transformWith(new MessageTransformer)
            // ->includeUser()
            // ->includeAssignee()
            ->toArray();

        return response()->json($response, 201);

    }

    public function getMessages($task){

        // dd($task->user->id);

        // $user = JWTAuth::parseToken()->authenticate();
        // if (!$user){
        //     return response()->json(['msg' => 'forbidden'], 403);
        // } else if ($user !== $task->user->id){

        //     return response()->json(['msg' => 'you do not own it'], 403);
        // }

        $messages = Message::where('task_id', $task)->get();
        // $messages = Task::with('messages')->find($task);
        // $messages = Task::with('messages')->find($task);
       
        $response = fractal()
            ->collection($messages)
            ->transformWith(new MessageTransformer)
            // ->paginateWith(new IlluminatePaginatorAdapter($messages))
            ->toArray();

        return response()->json($response, 200);
    }
}
