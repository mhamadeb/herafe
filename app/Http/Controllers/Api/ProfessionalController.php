<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    // عرض جميع الحرفيين
    public function index(Request $request)
    {
        $query = User::where('type', 'professional')
                    ->with(['professionalDetail', 'services', 'ratings']);

        // فلترة حسب الخدمة
        if ($request->has('service_id')) {
            $query->whereHas('services', function($q) use ($request) {
                $q->where('service_id', $request->service_id);
            });
        }

        // فلترة حسب التوفر
        if ($request->has('is_available')) {
            $query->whereHas('professionalDetail', function($q) use ($request) {
                $q->where('is_available', $request->is_available);
            });
        }

        // فلترة حسب المدينة
        if ($request->has('city')) {
            $query->whereHas('locations', function($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        $professionals = $query->paginate(15);

        return response()->json([
            'professionals' => $professionals
        ]);
    }

    // عرض حرفي محدد
    public function show($id)
    {
        $professional = User::where('type', 'professional')
                        ->with(['professionalDetail', 'services', 'locations', 'ratings', 'ordersAsCustomer'])
                        ->find($id);

        if (!$professional) {
            return response()->json(['message' => 'الحرفي غير موجود'], 404);
        }

        // إضافة متوسط التقييم
        $professional->average_rating = $professional->average_rating;
        $professional->total_ratings = $professional->ratings_count;

        return response()->json([
            'professional' => $professional
        ]);
    }

    // الحصول على تقييمات حرفي
    public function getRatings($id)
    {
        $professional = User::find($id);
        
        if (!$professional || !$professional->isProfessional()) {
            return response()->json(['message' => 'الحرفي غير موجود'], 404);
        }

        $ratings = $professional->ordersAsProfessional()
                    ->whereNotNull('order_rating')
                    ->with('customer')
                    ->select('id', 'order_rating', 'customer_id', 'created_at')
                    ->get();

        return response()->json([
            'ratings' => $ratings,
            'average' => $professional->average_rating,
            'total' => $ratings->count()
        ]);
    }

    // الحرفيون الأكثر تقييماً
    public function topRated()
    {
        $professionals = User::where('type', 'professional')
                        ->with(['professionalDetail', 'services'])
                        ->get()
                        ->map(function($professional) {
                            $professional->average_rating = $professional->average_rating;
                            return $professional;
                        })
                        ->sortByDesc('average_rating')
                        ->take(10)
                        ->values();

        return response()->json([
            'top_professionals' => $professionals
        ]);
    }
}