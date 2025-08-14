<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class CheckTokenPermission
{
    public function handle(Request $request, Closure $next)
    {
        // تحقق من Authorization Header
        $authHeader = $request->header('Authorization', '');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'status' => false,
                'message' => 'توكن مفقود أو غير صحيح',
            ], 401);
        }

        $token = trim(substr($authHeader, 7));
        if (empty($token)) {
            return response()->json([
                'status' => false,
                'message' => 'توكن غير موجود في الهيدر',
            ], 401);
        }

        // البحث عن التوكن
        $tokenModel = PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return response()->json([
                'status' => false,
                'message' => 'توكن غير صالح أو منتهي',
            ], 401);
        }

        // التحقق من تاريخ الانتهاء
        if (!empty($tokenModel->expires_at) && now()->greaterThan($tokenModel->expires_at)) {
            try {
                $tokenModel->delete();
            } catch (\Throwable $e) {
                Log::warning("فشل حذف التوكن المنتهي: " . $e->getMessage());
            }
            return response()->json([
                'status' => false,
                'message' => 'التوكن منتهي الصلاحية',
            ], 401);
        }

        // جلب المستخدم
        $user = $tokenModel->tokenable;
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'مستخدم التوكن غير موجود',
            ], 401);
        }

        // تحقق من كونه أدمن (role = 1)
        if ((int)$user->role !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'ليس لديك الصلاحية للوصول ',
            ], 403);
        }

        // ربط المستخدم بالتطبيق
        Auth::setUser($user);
        $request->setUserResolver(fn() => $user);
        $request->attributes->set('token_model', $tokenModel);

        return $next($request);
    }
}
