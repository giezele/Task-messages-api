<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\TaskTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Database\Eloquent\Builder;

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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $tasks = Task::all();
        // $tasks = Task::with('user')->orderBy('id')->get();
        $tasksPaginator = Task::paginate(10);

        $tasks = new Collection($tasksPaginator->items(), $this->taskTransformer);
        $tasks->setPaginator(new IlluminatePaginatorAdapter($tasksPaginator));

        $this->fractal->parseIncludes($request->get('include', '')); // parse includes
        $tasks = $this->fractal->createData($tasks); // Transform data

        return $tasks->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $this->validate($request, [
            'name' => 'required|string|max:255',
            // 'description' => 'required|text|max:4096',
            // 'type' => Rule::in(['basic', 'advanced', 'expert']),
            // 'status' => Rule::in(['todo', 'closed', 'hold']),

        ]);

        $task = Task::create($request->all());
        
        return $task; //regular

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Task::find($id), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $this->authorize('update', $task);

        $task = Task::find($id);

        $task->update($request->all());
        return $task;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $this->authorize('delete', $task);

        return (Task::destroy($id)== 1) ? 
                response()->json(['success' => 'success'], 200) : 
                response()->json(['error' => 'deleting from database was not successful'], 500)  ;
    }
}
