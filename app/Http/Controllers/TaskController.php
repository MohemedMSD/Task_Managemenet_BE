<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\Notification;
use App\Models\User;
use App\Http\Resources\TaskResource;
use App\Http\Resources\NotificationResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(){
        $this->middleware('role:manager')->only(['store', 'update', 'softDelete', 'restore', 'trashedProducts', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all task for management

        $data = Task::orderBy('created_at', 'desc')->get();

        return TaskResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validation = Validator::make($request->all(), [
            'title' => ['required', 'min:10'],
            'description' => ['required', 'min:20'],
            'finished_at' => ['required', 'date', 'after_or_equal:today'],
            'statu_id' => ['required', 'exists:status,id'],
            'user_id' => ['required', 'exists:users,id']
        ]);
        
        if ($validation->fails()) {
            
            return response()->json($validation->messages(), 422);

        }

        $task = Task::create($request->all());

        // make notification to assigned user
        $notification = Notification::create([
            'user_id' => $request->user_id,
            'title' => 'Assign a task to you !',
            'content' => 'You have been assigned a task : ' . $request->title,
            'from' => Auth()->user()->email
        ]);

        return new TaskResource($task);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $task = Task::find($id);
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $validation = Validator::make($request->all(), [
            'title' => ['required', 'min:10'],
            'description' => ['required', 'min:20'],
            'finished_at' => ['required', 'date', 'after_or_equal:today'],
            'statu_id' => ['required', 'exists:status,id'],
            'user_id' => ['required', 'exists:users,id']
        ]);

        if ($validation->fails()) {
            
            return response()->json($validation->messages(), 422);

        }

        $task = Task::find($id);

        // if exists user
        if (User::find($task->user_id)) {

            // check if manager change assigned user for send or not the notification
            if ($task->user_id != $request->user_id) {

                // send notification for old user 
                $notification = Notification::create([
                    'user_id' => $task->user_id,
                    'title' => 'Remove task from you !',
                    'content' => 'The task have been removed from you : ' . $request->title,
                    'from' => Auth()->user()->email
                ]);
    
                // send notification for new user 
                $notification = Notification::create([
                    'user_id' => $request->user_id,
                    'title' => 'Assign a task to you !',
                    'content' => 'You have been assigned a task : ' . $request->title,
                    'from' => Auth()->user()->email
                ]);
    
            }
        } else {

            // send notification for new user 
            $notification = Notification::create([
                'user_id' => $request->user_id,
                'title' => 'Update task!',
                'content' => 'The task has been updated : ' . $request->title,
                'from' => Auth()->user()->email
            ]);

        }

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'finished_at' => $request->finished_at,
            'statu_id' => $request->statu_id,
            'user_id' => $request->user_id
        ]);

        return new TaskResource($task);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $task = Task::find($id);

        if (User::find($task->user_id)) {
            // // send notification for old user
            $notification = Notification::create([
                'user_id' => $task->user_id,
                'title' => 'Your Task has been deleted !',
                'content' => 'The task has been deleted : ' . $task->title,
                'from' => Auth()->user()->email
            ]);
        }

        $task->delete();
        return response()->json(200);
    }

    public function updateStatus(Request $request, $id){
        
        $validation = Validator::make($request->all(), [
            'statu_id' => ['required', 'exists:status,id']
        ]);

        if ($validation->fails()) {
            
            return response()->json($validation->messages(), 422);

        }

        $task = Task::find($id);

        // send notification for user 
        $notification = Notification::create([
            'user_id' => $user = User::whereHas('role', function ($query){
                            $query->where('name', 'manager');
                        })->first()->id,
            'title' => 'Update task!',
            'content' => 'The task has been updated : ' . $task->title . '. New Status : ' . TaskStatus::find($request->statu_id)->name,
            'from' => Auth()->user()->email
        ]);

        $task->update([
            'statu_id' => $request->statu_id
        ]);

        return new TaskResource($task);

    }

    public function userTasks(){
        // get User Tasks
        $tasks = Task::where('user_id', Auth()->user()->id)
        ->get();

        return TaskResource::collection($tasks);
    }

    public function status(){
        // get status of task
        $status = TaskStatus::all();

        return response()->json($status);

    }
}
