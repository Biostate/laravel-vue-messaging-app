<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewFriendshipRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    protected $friendship_request;
    public $from_user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($friendship_request)
    {
        $this->friendship_request = $friendship_request;
        $this->from_user = $friendship_request->from_user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('user-'.$this->friendship_request->to);
    }

    public static function broadcastAs()
    {
        return 'NewFriendshipRequest';
    }
}
