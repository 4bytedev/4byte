<?php

namespace Packages\Course\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Packages\Course\Models\Course;

class CoursePublishedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Course $course;

    /**
     * Create a new event instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('content-published-channel'),
        ];
    }
}
