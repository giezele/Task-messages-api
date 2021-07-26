<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $task = Task::all();
        $task = Task::with('user')->orderBy('id')->get();

        return $task;
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
