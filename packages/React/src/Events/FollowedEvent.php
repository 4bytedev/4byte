<?php

namespace Packages\React\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Packages\React\Models\Follow;

class FollowedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var array<string, mixed>
     */
    public array $follow;

    /**
     * Create a new event instance.
     */
    public function __construct(Follow $follow)
    {
        $this->follow = $follow->only(['follower_id', 'followable_id', 'followable_type']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('followed-channel'),
        ];
    }
}
