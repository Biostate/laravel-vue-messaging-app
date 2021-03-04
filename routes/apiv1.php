<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->append(['friendship_requests_count']);
});
Route::middleware('auth:sanctum')->get('/user/check', function (Request $request) {
    return true;
});


Route::middleware('auth:sanctum')->get('/user/contacts', function (Request $request) {
    $user = $request->user();
    return \App\Models\UserContact::where('user_id', $user->id)->with('contact')->get()->pluck('contact');
});

Route::middleware('auth:sanctum')->get('/user/conversations', function (Request $request) {
    $user = $request->user();
    $conversations =  \App\Models\Conversation::whereHas('participants', function(Illuminate\Database\Eloquent\Builder $q) use (&$user){
        $q->where('user_id', $user->id);
    })->with(['participants' => function($q) use (&$user){
        $q->where('user_id', '!=',$user->id);
    }, 'participants.user'])->orderBy('updated_at', 'desc')->get();
    foreach ($conversations as $conversation )
            $conversation->append('latest_message');
    return $conversations;
});

Route::middleware('auth:sanctum')->get('/user/conversations/{id}/messages', function (Request $request, $id) {
    $user = $request->user();
    return \App\Models\Message::where('conversation_id', $id)->whereHas('conversation.participants', function(Illuminate\Database\Eloquent\Builder $q) use (&$user){
        $q->where('user_id', $user->id);
    })->orderBy('created_at', 'desc')->get();
});

Route::middleware('auth:sanctum')->post('/user/conversations/{id}/messages', function (Request $request, $id) {
    $user = $request->user();
    $conversation = \App\Models\Conversation::where('id', $id)->whereHas('participants', function(Illuminate\Database\Eloquent\Builder $q) use(&$user){$q->where('user_id', $user->id);})->first();
    if($conversation) {
        $conversation->touch();
        $message = $conversation->messages()->create([
            'message' => $request->message,
            'user_id' => $user->id
        ]);
        App\Events\NewMessage::dispatch($message->append('to'));
        return $message;
    }else
        abort(401);
});

Route::middleware('auth:sanctum')->resource('/user/friendship_requests','FriendshipRequestController')->only(['index', 'store', 'update']);

Route::post('/login', '\App\Http\Controllers\AuthController@login');

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return response()->json([
        "result"=> true,
    ]);
});
