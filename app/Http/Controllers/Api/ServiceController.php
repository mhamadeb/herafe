<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // عرض جميع الخدمات
    public function index()
    {
        $services = Service::with('professionals')->get();
        
        return response()->json([
            'services' => $services
        ]);
    }

    // عرض خدمة محددة
    public function show($id)
    {
        $service = Service::with(['professionals' => function($query) {
            $query->whereHas('professionalDetail', function($q) {
                $q->where('is_available', true);
            });
        }])->find($id);

        if (!$service) {
            return response()->json(['message' => 'الخدمة غير موجودة'], 404);
        }

        return response()->json([
            'service' => $service
        ]);
    }

    // إضافة خدمة جديدة (للمدير فقط)
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|unique:services',
            'type' => 'required|string',
            'color' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $serviceData = $request->only(['name', 'type', 'color']);
        
        if ($request->hasFile('image')) {
            $serviceData['image'] = $request->file('image')->store('services', 'public');
        }

        $service = Service::create($serviceData);

        return response()->json([
            'message' => 'تم إضافة الخدمة بنجاح',
            'service' => $service
        ], 201);
    }

    // الحصول على خدمات حرفي معين
    public function getProfessionalServices($professionalId)
    {
        $professional = User::find($professionalId);
        
        if (!$professional || !$professional->isProfessional()) {
            return response()->json(['message' => 'الحرفي غير موجود'], 404);
        }

        $services = $professional->services;

        return response()->json([
            'services' => $services
        ]);
    }

    public function attachServiceToProfessional(Request $request, $professionalId)
{
    $validator = validator($request->all(), [
        'service_id' => 'required|exists:services,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $professional = User::find($professionalId);

    if (!$professional || !$professional->isProfessional()) {
        return response()->json(['message' => 'الحرفي غير موجود'], 404);
    }

    // ربط الخدمة
    $professional->services()->syncWithoutDetaching([
        $request->service_id
    ]);

    return response()->json([
        'message' => 'تم ربط الخدمة بالحرفي بنجاح'
    ]);
}
public function detachServiceFromProfessional(Request $request, $professionalId)
{
    $validator = validator($request->all(), [
        'service_id' => 'required|exists:services,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $professional = User::find($professionalId);

    if (!$professional || !$professional->isProfessional()) {
        return response()->json(['message' => 'الحرفي غير موجود'], 404);
    }

    $professional->services()->detach($request->service_id);

    return response()->json([
        'message' => 'تم حذف الخدمة من الحرفي بنجاح'
    ]);
}
}