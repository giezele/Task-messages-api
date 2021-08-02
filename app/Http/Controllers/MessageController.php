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
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
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
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
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
}
