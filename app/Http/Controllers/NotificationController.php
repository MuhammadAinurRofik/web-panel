<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mengubah status notifikasi menjadi 'read'.
     */
    public function markAsRead($id)
    {
        // Cari notifikasi berdasarkan ID dan pastikan itu milik user yang sedang login
        $notification = Notification::where('notif_id', $id)
                                    ->where('user_id', Auth::user()->user_id)
                                    ->firstOrFail();

        // Update status
        $notification->update(['status' => 'read']);

        return back()->with('success', 'Pesan telah ditutup.');
    }
}
