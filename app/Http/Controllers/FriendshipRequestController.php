<?php

namespace App\Http\Controllers;

use App\Events\FriendshipRequestAccepted;
use App\Models\Conversation;
use App\Models\Participant;
use App\Models\UserContact;
use Illuminate\Http\Request;
use App\Models\FriendshipRequest;
use App\Models\User;
use App\Events\NewFriendshipRequest;
class FriendshipRequestController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'result' => true,
            'data' => auth()->user()->frendship_requests()->with('from_user')->get()
        ]);
    }

    public function store(Request $request)
    {
        $user = User::where('email', $request->email)->whereNotIn('id', [auth()->user()->id])->select('id')->firstOrFail();
        if(auth()->user()->contacts()->where('contact_id', $user->id)->exists())
            return response()->json([
                'result' => false,
                'message' => "You are already in contact with that user."
            ]);
        $fr = FriendshipRequest::firstOrCreate([
            'from' => auth()->user()->id,
            'to' => $user->id,
            'deleted_at' => null
        ]);
        if(!$fr->wasRecentlyCreated) {
            return response()->json([
                'result' => false,
                'message' => "You sent already."
            ]);
        }
        NewFriendshipRequest::dispatch($fr->load('from_user'));
        return response()->json([
            'result' => true,
            'message' => "Your request sent."
        ]);
    }

    protected function update(Request $request, $id)
    {
        $fr = auth()->user()->frendship_requests()->where('id', $id)->firstOrFail();
        if($request->is_user_accepted){
            UserContact::insert([
                [
                    'user_id' => $fr->from,
                    'contact_id' => $fr->to,
                    'created_at' => now(),
                    'updated_at' => now()
                ],[
                    'user_id' => $fr->to,
                    'contact_id' => $fr->from,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
            $conversation = Conversation::create();
            $conversation->participants()->createMany([
                [
                    'user_id' => $fr->from
                ],[
                    'user_id' => $fr->to
                ]
            ]);
            $conversation->load(['participants.user']);
            $conversation->append('latest_message');
            FriendshipRequestAccepted::dispatch($conversation, $fr->from);
            FriendshipRequestAccepted::dispatch($conversation, $fr->to);
        }
        $fr->delete();
        return $fr;
    }
}
