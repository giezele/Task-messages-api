<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Events\MessageWasViewed;
use App\Models\Message;
use App\Models\Task;
use App\Models\User;
use App\Transformers\MessageTransformer;
use App\Transformers\TaskTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use JWTAuth;


class TaskController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var TaskTransformer
     */
    private $taskTransformer;

    function __construct(Manager $fractal, TaskTransformer $taskTransformer)
    {
        $this->fractal = $fractal;
        $this->taskTransformer = $taskTransformer;
        $this->middleware('jwt.auth' , ['only' => ['store', 'index', 'show', 'update', 'destroy', 'changeTaskStatus']]); 
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Task $task)
    {
        // $this->authorize('view', $task);
        $user = JWTAuth::parseToken()->authenticate();
       
        $tasks = Task::where('user_id', $user->id)->orWhere('assignee_id', $user->id)->paginate(5);
       
        $response = fractal()
            ->collection($tasks)
            ->transformWith(new TaskTransformer)
            ->includeUser()
            ->includeAssignee()
            ->includeMessages()
            ->paginateWith(new IlluminatePaginatorAdapter($tasks))
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
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:4096',
            'type' => 'in:basic,advanced,expert',
            'status' => 'in:todo,closed,hold',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'type'=> $request->type,
            'status'=> $request->status,

        ];
        $task = Task::create($data);
        $task->user()->associate(User::find(auth()->user()->id));
        $task->assignee()->associate(User::find($request->input('assignee_id')));
        $task->save();
        
        $response = fractal()
            ->item($task)
            ->transformWith(new TaskTransformer)
            ->includeUser()
            ->includeAssignee()
            ->toArray();

        return response()->json($response, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show($task)
    {
        $task = Task::findOrFail($task);
        $this->authorize('view', $task);

        
        $response = fractal()
            ->item($task)
            ->transformWith(new TaskTransformer)
            ->includeUser()
            ->includeAssignee()
            ->includeMessages()
            ->toArray();
        
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $task)
    {
        $task = Task::find($task);

        $this->authorize('update', $task);

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:4096',
            'type' => 'in:basic,advanced,expert',
            'status' => 'in:todo,closed,hold',
        ]);

        $task->assignee()->associate(User::find($request->input('assignee_id')));
        $task->save();

        $task->update($request->all());
        $response = fractal()
            ->item($task)
            ->transformWith(new TaskTransformer)
            ->includeUser()
            ->includeAssignee()
            ->toArray();
    
        return response()->json($response, 200); 

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ], 200);

    }

    public function changeTaskStatus(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $validator = Validator::make($request->only(['status']), [
            'status' => 'in:closed' 
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $task->status = $request->get('status', $task->status);
        $task->save();
     
        $response = fractal()	
            ->item($task)
            ->transformWith(new TaskTransformer)
            ->includeUser()
            ->includeAssignee()
            ->toArray();
     
        return response()->json($response, 200);
    }

    public function addMessage(Request $request, Task $task){
       
        $task = Task::findOrFail($request->get('task_id'));
        $this->authorize('view', $task);

        $this->validate($request, [
            'subject' => 'required|string|max:255',
            'message' => 'string|max:4096',
        ]);

        $message = new Message([
            'subject' => $request->get('subject'),
            'message' => $request->get('message')
        ]);

        $message->user()->associate($request->user());
        $task->messages()->save($message);
        
        $response = fractal()
            ->item($message)
            ->transformWith(new MessageTransformer)
            ->includeMsgUser()
            // ->includeAssignee()
            ->toArray();

        event(new MessageCreated($message));

        return response()->json($response, 201);
    }


    public function getMessagesOfTask(Task $task){
        // dd($task->user->id);
        $this->authorize('view', $task);

        $messages = Message::where('task_id', $task->id)->paginate(3);
       
        $response = fractal()
            ->collection($messages)
            ->transformWith(new MessageTransformer)
            ->paginateWith(new IlluminatePaginatorAdapter($messages))
            ->toArray();

        return response()->json($response, 200);
        
    }

    public function getMessage(Task $task, Message $message){
        
        $this->authorize('view', $task);

        if($task->id !== $message->task_id){
            return response()->json(['msg' => 'forbidden'], 403);
        }
        $message = Message::findOrFail($message->id);

        event(new MessageWasViewed($message));
       
        $response = fractal()
            ->item($message)
            ->transformWith(new MessageTransformer)
             ->toArray();

        return response()->json($response, 200);
    }

    public function getMessageLogOfTask(Task $task){

        $this->authorize('view', $task);
        
        $log = DB::select( DB::raw("SELECT * FROM message_log WHERE task_id = :taskId"), 
            array('taskId' => $task->id)
        );
        
        return response()->json($log, 200);
    }

}
