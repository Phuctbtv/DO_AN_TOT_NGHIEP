<?php

namespace App\Events;

use App\Models\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int    $trip_id;
    public string $status;
    public string $trip_code;
    public ?string $completed_at;

    public function __construct(Trip $trip)
    {
        $this->trip_id      = $trip->id;
        $this->status       = $trip->status;
        $this->trip_code    = $trip->trip_code;
        $this->completed_at = $trip->completed_at?->toISOString();
    }

    /**
     * Kênh broadcast (Private Channel theo trip_id)
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('trips.' . $this->trip_id),
        ];
    }

    /**
     * Tên event gửi về client
     */
    public function broadcastAs(): string
    {
        return 'TripStatusUpdated';
    }
}
