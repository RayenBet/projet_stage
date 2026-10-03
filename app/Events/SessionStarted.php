<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Session;

class SessionStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(Session $session)
    {
        $this->payload = [
            'session_id' => $session->id,
            'subject' => $session->subject->name ?? 'Matière',
            'classe' => $session->classe->name ?? 'Classe',
            'room' => $session->room,
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('professor.' . $this->payload['session_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.started';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}