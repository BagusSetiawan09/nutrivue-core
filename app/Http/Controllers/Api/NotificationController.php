<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppNotification;

class NotificationController
{
    /**
     * Mengambil semua notifikasi milik pengguna yang sedang login
     */
    public function index(Request $request)
    {
        try {
            $notifications = AppNotification::where('user_id', $request->user()->id)
                                ->orderBy('created_at', 'desc')
                                ->get();

            // Hitung jumlah yang belum dibaca untuk lencana (badge) di ikon lonceng
            $unreadCount = $notifications->where('is_read', false)->count();

            return response()->json([
                'status' => 'success',
                'unread_count' => $unreadCount,
                'data' => $notifications
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menarik data notifikasi'], 500);
        }
    }

    /**
     * Menandai semua notifikasi menjadi sudah dibaca
     */
    public function markAllAsRead(Request $request)
    {
        try {
            AppNotification::where('user_id', $request->user()->id)
                           ->where('is_read', false)
                           ->update(['is_read' => true]);

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }
}