<?php

namespace App\Http\Controllers\Api;

use App\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Carbon\Carbon;

/**
 * @group Task management
 * 
 * APIs for managing tasks
 */
class TasksController extends Controller
{
    /**
     * Display a listing of the tasks.
     * @authenticated
     * 
     * @responseFile responses/tasks.get.json
     * @responseFile 401 responses/401.json
     * @responseFile 404 responses/404.json
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TaskResource::collection(auth()->user()->tasks()->with('creator')->latest()->paginate(2));
    }

    /**
     * Store a newly created resource in storage.
     * @authenticated
     * @bodyParam title string required Title of the task. Example: my first task
     * @bodyParam description text Description of the task.
     * @bodyParam due string Due date of the task. Example: next friday
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $input = $request->all();

        if ($request->has('due')) {
            $input['due'] = Carbon::parse($request->due)->toDateTimeString();
        }

        $task = auth()->user()->tasks()->create($input);

        return new TaskResource($task->load('creator'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return new TaskResource($task->load('creator'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $input = $request->all();

        if ($request->has('due')) {
            $input['due'] = Carbon::parse($request->due)->toDateTimeString();
        }

        $task->update($input);

        return new TaskResource($task->load('creator'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response(['message' => 'Task deleted']);
    }
}
