<?php

namespace App\Events;

use App\Models\Timeline;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimelinePaused implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Timeline $timeline;

    public function __construct(Timeline $timeline)
    {
        $this->timeline = $timeline;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('timelines');
    }

    public function broadcastAs(): string
    {
        return 'timeline.paused';
    }
}
