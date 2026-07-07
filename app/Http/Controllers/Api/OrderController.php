<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // عرض جميع الطلبات (حسب صلاحية المستخدم)
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->isProfessional()) {
            // الحرفي يرى طلباته فقط
            $orders = Order::with(['customer', 'service', 'location'])
                        ->where('professional_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        } else {
            // العميل يرى طلباته فقط
            $orders = Order::with(['professional', 'service', 'location'])
                        ->where('customer_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        return response()->json([
            'orders' => $orders
        ]);
    }

    // عرض طلب محدد
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::with(['customer', 'professional', 'service', 'location'])->find($id);

        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        // التحقق من صلاحية الوصول
        if ($order->customer_id !== $user->id && $order->professional_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح بالوصول إلى هذا الطلب'], 403);
        }

        return response()->json([
            'order' => $order
        ]);
    }

    // إنشاء طلب جديد
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isCustomer()) {
            return response()->json(['message' => 'العملاء فقط يمكنهم إنشاء طلبات'], 403);
        }

        $validator = Validator::make($request->all(), [
            'professional_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'location_id' => 'required|exists:locations,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $professional = User::find($request->professional_id);
        
        if (!$professional->isProfessional()) {
            return response()->json(['message' => 'المستخدم المحدد ليس حرفياً'], 422);
        }

        $order = Order::create([
            'customer_id' => $user->id,
            'professional_id' => $request->professional_id,
            'service_id' => $request->service_id,
            'location_id' => $request->location_id,
            'order_date' => now(),
            'status' => 'pending',
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes' => $request->notes,
        ]);

        // إشعار للحرفي
        Notification::create([
            'user_id' => $professional->id,
            'order_id' => $order->id,
            'title' => 'طلب جديد',
            'body' => 'لديك طلب جديد من ' . $user->full_name,
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'تم إنشاء الطلب بنجاح',
            'order' => $order
        ], 201);
    }

    // تحديث حالة الطلب
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        // التحقق من الصلاحية
        if ($order->professional_id !== $user->id && $order->customer_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح به'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,cancelled,delayed,completed,in_progress'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        // إشعار بتحديث الحالة
        $notifyUser = ($user->id === $order->customer_id) ? $order->professional_id : $order->customer_id;
        
        Notification::create([
            'user_id' => $notifyUser,
            'order_id' => $order->id,
            'title' => 'تحديث حالة الطلب',
            'body' => 'تم تغيير حالة الطلب من ' . $oldStatus . ' إلى ' . $request->status,
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'order' => $order
        ]);
    }

    // تقييم الطلب (من قبل العميل فقط)
    public function rateOrder(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        if ($order->customer_id !== $user->id) {
            return response()->json(['message' => 'العميل فقط يمكنه تقييم الطلب'], 403);
        }

        if ($order->status !== 'completed') {
            return response()->json(['message' => 'يمكن تقييم الطلبات المكتملة فقط'], 422);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order->order_rating = $request->rating;
        $order->save();

        return response()->json([
            'message' => 'تم تقييم الطلب بنجاح',
            'rating' => $order->order_rating
        ]);
    }

    // طلباتي كعميل
    public function myOrdersAsCustomer(Request $request)
    {
        $user = $request->user();
        
        $orders = Order::with(['professional', 'service', 'location'])
                    ->where('customer_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return response()->json($orders);
    }

    // طلباتي كحرفي
    public function myOrdersAsProfessional(Request $request)
    {
        $user = $request->user();
        
        $orders = Order::with(['customer', 'service', 'location'])
                    ->where('professional_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return response()->json($orders);
    }

    // إلغاء الطلب
    public function cancelOrder(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        if ($order->customer_id !== $user->id && $order->professional_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح به'], 403);
        }

        if (!in_array($order->status, ['pending', 'in_progress'])) {
            return response()->json(['message' => 'لا يمكن إلغاء هذا الطلب في حالته الحالية'], 422);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'message' => 'تم إلغاء الطلب بنجاح',
            'order' => $order
        ]);
    }
}