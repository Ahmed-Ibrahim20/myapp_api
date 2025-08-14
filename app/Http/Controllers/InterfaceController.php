<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\InterfaceService;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class InterfaceController extends Controller
{
    protected InterfaceService $service;

    public function __construct(InterfaceService $service)
    {
        $this->service = $service;
    }

    /**
     * تحقق اختياري من التوكن: لو موجود نرجع اليوزر (لا نرفض الطلب لو مش موجود).
     */
    private function getAuthUserFromRequest(Request $request)
    {
        $authHeader = $request->header('Authorization', '');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        $token = trim(substr($authHeader, 7));
        $tokenModel = PersonalAccessToken::findToken($token);
        return $tokenModel ? $tokenModel->tokenable : null;
    }

    /**
     * GET /api/interface/categories
     * عرض كل الأقسام
     */
    public function categories(Request $request)
    {
        $categories = $this->service->getAllCategories();
        $user = $this->getAuthUserFromRequest($request);

        return response()->json([
            'status' => true,
            'message' => 'قائمة الأقسام',
            'data' => $categories,
            'auth_user' => $user, // سيكون null لو مفيش توكن
        ]);
    }

    /**
     * GET /api/interface/categories/{id}
     * تفاصيل قسم واحد
     */
    public function showCategory($id, Request $request)
    {
        $category = $this->service->getCategoryById($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'القسم غير موجود'
            ], 404);
        }

        $user = $this->getAuthUserFromRequest($request);

        return response()->json([
            'status' => true,
            'message' => 'تفاصيل القسم',
            'data' => $category,
            'auth_user' => $user,
        ]);
    }

    /**
     * GET /api/interface/categories/{id}/products
     * عرض منتجات القسم (paginated)
     * Query params: perPage (default 10), search (optional)
     */
    public function categoryProducts($id, Request $request)
    {
        $perPage = (int) $request->query('perPage', 10);
        $search = $request->query('search', null);

        $result = $this->service->getProductsByCategory($id, $perPage, $search);

        if (!$result['category']) {
            return response()->json([
                'status' => false,
                'message' => 'القسم غير موجود'
            ], 404);
        }

        $user = $this->getAuthUserFromRequest($request);

        return response()->json([
            'status' => true,
            'message' => 'منتجات القسم',
            'data' => [
                'category' => $result['category'],
                'products' => $result['products'] // paginator (with transformed items)
            ],
            'auth_user' => $user,
        ]);
    }
       /**
     * GET /api/interface/products
     * عرض كل المنتجات (paginated) مع خيارات: perPage, search, category_id (فلترة)
     */
    public function products(Request $request)
    {
        $perPage = (int) $request->query('perPage', 10);
        $search = $request->query('search', null);
        $categoryId = $request->query('category_id', null);

        $paginator = $this->service->getAllProducts($perPage, $search, $categoryId);

        $user = $this->getAuthUserFromRequest($request);

        return response()->json([
            'status' => true,
            'message' => 'قائمة المنتجات',
            'data' => $paginator,
            'auth_user' => $user,
        ]);
    }

    /**
     * GET /api/interface/products/{id}
     * تفاصيل منتج واحد بالـ id
     */
    public function showProduct($id, Request $request)
    {
        $product = $this->service->getProductById($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        $user = $this->getAuthUserFromRequest($request);

        return response()->json([
            'status' => true,
            'message' => 'تفاصيل المنتج',
            'data' => $product,
            'auth_user' => $user,
        ]);
    }

}
