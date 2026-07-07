<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // عرض جميع إشعارات المستخدم
    public function index(Request $request)
    {
        $notifications = $request->user()
                            ->notifications()
                            ->with('order')
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $request->user()->notifications()->where('is_read', false)->count()
        ]);
    }

    // عرض إشعار محدد
    public function show(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
                            ->with('order')
                            ->find($id);

        if (!$notification) {
            return response()->json(['message' => 'الإشعار غير موجود'], 404);
        }

        return response()->json([
            'notification' => $notification
        ]);
    }

    // تحديد إشعار كمقروء
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
                            ->find($id);

        if (!$notification) {
            return response()->json(['message' => 'الإشعار غير موجود'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'message' => 'تم تحديد الإشعار كمقروء'
        ]);
    }

    // تحديد جميع الإشعارات كمقروءة
    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()->update(['is_read' => true]);

        return response()->json([
            'message' => 'تم تحديد جميع الإشعارات كمقروءة'
        ]);
    }

    // حذف إشعار
    public function destroy(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
                            ->find($id);

        if (!$notification) {
            return response()->json(['message' => 'الإشعار غير موجود'], 404);
        }

        $notification->delete();

        return response()->json([
            'message' => 'تم حذف الإشعار بنجاح'
        ]);
    }
}