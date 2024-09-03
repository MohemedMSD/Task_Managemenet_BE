<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    //
    // get all notification
    public function index(Request $request){

        $notifications = Notification::where('user_id', Auth()->user()->id)
        ->orderBy('created_at', 'desc')->get();

        return NotificationResource::collection($notifications);

    }

    public function delete($id){

        $notification = Notification::find($id);
        $notification->delete();

        return response()->json(200);
    }

}
