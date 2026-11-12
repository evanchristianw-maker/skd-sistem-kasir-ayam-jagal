<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at?->toIso8601String(),
                'time' => $notification->created_at?->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'message' => 'Notifikasi berhasil ditandai sudah dibaca.',
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        if (!$notification->read_at) {
            return response()->json([
                'message' => 'Notifikasi harus dibaca sebelum dihapus.',
            ], 422);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notifikasi berhasil dihapus.',
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}
