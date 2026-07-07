<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfessionalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // تسجيل مستخدم جديد
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'type' => ['required', Rule::in(['professional', 'customer'])],
            // حقول إضافية للحرفي
            'biography' => 'required_if:type,professional|string|nullable',
            'years_of_experience' => 'required_if:type,professional|integer|min:0|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'type' => $request->type,
            'joined_at' => now(),
        ]);

        // إذا كان المستخدم حرفي، أضف التفاصيل الإضافية
        if ($request->type === 'professional' && $request->biography) {
            ProfessionalDetail::create([
                'user_id' => $user->id,
                'biography' => $request->biography,
                'years_of_experience' => $request->years_of_experience ?? 0,
                'is_available' => true,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم التسجيل بنجاح',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // تسجيل الدخول
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ], 401);
        }

        // حذف التوكنات القديمة (اختياري)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    // جلب بيانات المستخدم الحالي
    public function me(Request $request)
    {
        $user = $request->user()->load(['professionalDetail', 'locations', 'services']);
        
        return response()->json($user);
    }

    // تحديث بيانات المستخدم
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('full_name')) {
            $user->full_name = $request->full_name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $imagePath;
        }

        $user->save();

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'user' => $user
        ]);
    }

    // تحديث بيانات الحرفي
    public function updateProfessionalDetails(Request $request)
    {
        $user = $request->user();

        if (!$user->isProfessional()) {
            return response()->json(['message' => 'غير مصرح به'], 403);
        }

        $validator = Validator::make($request->all(), [
            'biography' => 'sometimes|string',
            'years_of_experience' => 'sometimes|integer|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $details = $user->professionalDetail;
        
        if ($request->has('biography')) {
            $details->biography = $request->biography;
        }
        
        if ($request->has('years_of_experience')) {
            $details->years_of_experience = $request->years_of_experience;
        }
        
        if ($request->has('is_available')) {
            $details->is_available = $request->is_available;
        }
        
        $details->save();

        return response()->json([
            'message' => 'تم تحديث بيانات الحرفي بنجاح',
            'professional_details' => $details
        ]);
    }
}