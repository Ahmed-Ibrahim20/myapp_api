<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Http\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $coupons = $this->couponService->indexCoupon($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'قائمة الكوبونات',
            'data' => $coupons
        ]);
    }

    public function store(CouponRequest $request)
    {
        $result = $this->couponService->storeCoupon($request->validated());
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function show($id)
    {
        $coupon = $this->couponService->editCoupon($id);
        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'الكوبون غير موجود'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تفاصيل الكوبون',
            'data' => $coupon
        ]);
    }

    public function update(CouponRequest $request, $id)
    {
        $result = $this->couponService->updateCoupon($request->validated(), $id);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->couponService->destroyCoupon($id);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
