<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MobileNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MobileNotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     */
    public function index(Request $request)
    {
        try {
            $pelanggan = $request->user();

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            Log::info('Fetching notifications for: ' . $pelanggan->id_pelanggan);

            $notifications = MobileNotification::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            Log::info('Found ' . $notifications->count() . ' notifications');

            $mappedData = [];
            foreach ($notifications as $notif) {
                try {
                    $mappedData[] = [
                        'id' => $notif->id,
                        'title' => $notif->title ?? '',
                        'body' => $notif->body ?? '',
                        'type' => $notif->type ?? 'info',
                        'data' => is_array($notif->data) ? $notif->data : null,
                        'is_read' => (bool) $notif->is_read,
                        'created_at' => $notif->created_at ? $notif->created_at->format('Y-m-d H:i:s') : null,
                        'time_ago' => $notif->created_at ? $notif->created_at->diffForHumans() : null,
                    ];
                } catch (\Exception $mapError) {
                    Log::error('Error mapping notification ID ' . $notif->id . ': ' . $mapError->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'data' => $mappedData,
            ]);
        } catch (\Exception $e) {
            Log::error('MobileNotificationController@index error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function unreadCount(Request $request)
    {
        try {
            $pelanggan = $request->user();

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            $count = MobileNotification::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $count,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $pelanggan = $request->user();

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            $notification = MobileNotification::where('id', $id)
                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan',
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca',
            ]);
        } catch (\Exception $e) {
            Log::error('MobileNotificationController@markAsRead error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $pelanggan = $request->user();

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            MobileNotification::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sudah dibaca',
            ]);
        } catch (\Exception $e) {
            Log::error('MobileNotificationController@markAllAsRead error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete all notifications for authenticated user
     */
    public function deleteAll(Request $request)
    {
        try {
            $pelanggan = $request->user();

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            MobileNotification::where('id_pelanggan', $pelanggan->id_pelanggan)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('MobileNotificationController@deleteAll error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
