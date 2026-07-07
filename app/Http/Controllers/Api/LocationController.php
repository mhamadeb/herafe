<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    // عرض جميع مواقع المستخدم
    public function index(Request $request)
    {
        $locations = $request->user()->locations;
        
        return response()->json([
            'locations' => $locations
        ]);
    }

    // إضافة موقع جديد
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $location = $request->user()->locations()->create($request->all());

        return response()->json([
            'message' => 'تم إضافة الموقع بنجاح',
            'location' => $location
        ], 201);
    }

    // تحديث موقع
    public function update(Request $request, $id)
    {
        $location = Location::find($id);
        
        if (!$location) {
            return response()->json(['message' => 'الموقع غير موجود'], 404);
        }

        if ($location->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح به'], 403);
        }

        $validator = Validator::make($request->all(), [
            'city' => 'sometimes|string',
            'address' => 'sometimes|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $location->update($request->all());

        return response()->json([
            'message' => 'تم تحديث الموقع بنجاح',
            'location' => $location
        ]);
    }

    // حذف موقع
    public function destroy(Request $request, $id)
    {
        $location = Location::find($id);
        
        if (!$location) {
            return response()->json(['message' => 'الموقع غير موجود'], 404);
        }

        if ($location->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح به'], 403);
        }

        $location->delete();

        return response()->json([
            'message' => 'تم حذف الموقع بنجاح'
        ]);
    }
}