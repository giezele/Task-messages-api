<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\TaskTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Fractal\Fractal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Rule;
use App\Models\User;


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
        // $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Task $task)
    {
        // $this->authorize('view', $task);

        $paginator = Task::paginate(10);
        // $tasks = Task::all();
        $tasks = Task::with('user')->orderBy('id')->get();
        $tasks = $paginator->getCollection();

        $response = fractal()
            ->collection($tasks)
            ->transformWith(new TaskTransformer)
            ->includeUser()
            ->includeAssignee()
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
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
        $user = auth()->user();

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
            'user_id' => auth()->user()->id,
            // 'assignee_id' => $request->except('assignee_id'),
        ];
        $task = Task::create($data);
        
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
        // $this->authorize('view', $task);
        $task = Task::find($task);
        
        $response = fractal()
            ->item($task)
            ->transformWith(new TaskTransformer)
            ->includeUser()
            ->includeAssignee()
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
        // $this->authorize('update', $task);

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:4096',
            'type' => 'in:basic,advanced,expert',
            'status' => 'in:todo,closed,hold',
        ]);

        $task = Task::find($task);
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
    public function destroy($task)
    {
        // $this->authorize('delete', $task);
        
        return (Task::destroy($task)== 1) ? 
                response()->json(['success' => 'deleted successfully'], 200) : 
                response()->json(['error' => 'deleting from database was not successful'], 500)  ;
    }

    public function addTask(Request $request, Task $task){
        $this->validate($request,[
            'name' => 'required|string|max:255',
            'description' => 'string|max:4096',
            'type' => 'in:basic,advanced,expert',
            'status' => 'in:todo,closed,hold',
        ]);
     
        $task = Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'type'=> $request->type,
            'status'=> $request->status,
            'user_id' => auth()->user()->id,
            // 'assignee_id' => $request->assignee_id
        ]);
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

    public function changeTaskStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validator = Validator::make(Input::only(['status']), [
            'type' => 'in:todo,closed,hold' 
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
            ->toArray();
     
        return response()->json($response, 200);
    }

    public function authUserTasks(Task $task)
    {
        $tasks = Task::where('user_id', auth()->user()->id);
            // ->orWhere('assignee_id', auth()->user()->id);
            // ->filter($filter);

        $response = fractal()	
            ->collection($tasks)
            ->transformWith(new TaskTransformer)
            ->includeUser()
            ->toArray();
             
        return response()->json($response, 200);
    }

}
