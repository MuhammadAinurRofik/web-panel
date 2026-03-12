<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Logout;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event->user) {
            ActivityLog::create([
                'user_id'     => $event->user->user_id,
                'activity'    => 'Logout',
                'description' => "User {$event->user->name} telah keluar dari sistem.",
            ]);
        }
    }
}
