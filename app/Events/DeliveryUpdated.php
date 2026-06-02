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

class DeliveryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int    $trip_id;
    public int    $pending_count;
    public int    $success_count;
    public int    $total_count;
    public int    $delivery_id;      // ID delivery vừa được confirm
    public string $delivery_status;  // 'success' | 'warning'
    public ?string $delivered_at;    // Thời gian giao (format H:i d/m/Y)

    public function __construct(Trip $trip, array $stats, ?\App\Models\Delivery $delivery = null)
    {
        $this->trip_id        = $trip->id;
        $this->pending_count  = $stats['pending_count'];
        $this->success_count  = $stats['success_count'];
        $this->total_count    = $stats['total_count'];
        $this->delivery_id    = $delivery?->id ?? 0;
        $this->delivery_status= $delivery?->status ?? 'success';
        $this->delivered_at   = $delivery?->delivered_at?->format('H:i d/m/Y');
    }

    /**
     * Kênh broadcast (Private Channel theo trip_id)
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('deliveries.' . $this->trip_id),
        ];
    }

    /**
     * Tên event gửi về client
     */
    public function broadcastAs(): string
    {
        return 'DeliveryUpdated';
    }
}
