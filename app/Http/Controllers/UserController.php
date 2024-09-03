<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    //

    public function __construct(){
        $this->middleware('role:manager')->only(['delete', 'index']);
    }

    public function index(){
        $users = User::get(['id', 'name', 'email']);
        return \response()->json($users);
    }

    public function homeInformations(){

        $myTasks = Task::where('user_id', Auth()->user()->id)
        ->count();

        $users = User::where('id', '!=', Auth()->user()->id)->count();
        $Tasks = Task::all()->count();

        $data = ['myTasks' => $myTasks];

        if(Auth()->user()->role->name === 'manager'){

            $data = [
                'myTasks' => $myTasks,
                'tasks' => $Tasks,
                'users' => $users
            ];

        }

        return \response()->json($data);

    }

    public function show($id){
        $user = User::find($id);
        return new UserResource($user);
    }

    public function delete($id){

        $user = User::find($id);
        $user->notifications()->delete();
        $user->forceDelete();
        return response()->json(200);

    }
}
