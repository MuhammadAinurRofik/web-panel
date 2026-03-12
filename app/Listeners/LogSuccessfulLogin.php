<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
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
    // public function handle(object $event): void
    // {
    //     //
    // }

    public function handle(object $event): void
    {
        $user = $event->user;

        ActivityLog::create([
            'user_id'     => $user->user_id, // Gunakan user_id sesuai PK tabel Anda
            'activity'    => 'Login',
            'description' => "User {$user->name} berhasil login ke dashboard",
        ]);
    }
}
