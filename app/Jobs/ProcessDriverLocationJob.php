<?php

namespace App\Jobs;

use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDriverLocationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public float $lat;
    public float $lng;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, float $lat, float $lng)
    {
        $this->userId = $userId;
        $this->lat = $lat;
        $this->lng = $lng;

        $this->onQueue('geolocation');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Driver::updateOrCreate(
            ['id_user' => $this->userId],
            [
                'latitude' => $this->lat,
                'longitude' => $this->lng,
                'is_online' => true,
                'last_location_update' => now()
            ]
        );
    }
}
