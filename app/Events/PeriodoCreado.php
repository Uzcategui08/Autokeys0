<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pnomina;

class PeriodoCreado
{
    use Dispatchable, SerializesModels;

    public $periodo;

    public function __construct(Pnomina $periodo)
    {
        $this->periodo = $periodo;
    }
}