<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceMarked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $sessionId;
    public array $payload;

    public function __construct(int $sessionId, array $payload)
    {
        $this->sessionId = $sessionId;
        $this->payload = $payload;
    }

    /**
     * Canal public "session.{id}" — tout navigateur ouvert sur le dashboard
     * du professeur qui suit cette séance reçoit l'événement en direct.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('session.' . $this->sessionId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'attendance.marked';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}