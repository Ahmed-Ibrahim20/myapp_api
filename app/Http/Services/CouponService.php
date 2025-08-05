<?php

namespace App\Http\Services;

use App\Models\Coupon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CouponService
{
    protected Coupon $model;

    public function __construct(Coupon $model)
    {
        $this->model = $model;
    }

    public function indexCoupon($search = null, $perPage = 10)
    {
        return $this->model->when($search, function ($query) use ($search) {
            $query->where('code', 'like', "%{$search}%");
        })->paginate($perPage);
    }

    public function storeCoupon(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'code',
                'discount_value',
                'discount_type',
                'usage_limit',
                'start_date',
                'end_date',
                'description',
                'user_add_id'
            ]);
            $data['user_add_id'] = Auth::id();
            $coupon = $this->model->create($data);
            return [
                'status' => true,
                'message' => 'تم إنشاء الكوبون بنجاح',
                'data' => $coupon
            ];
        } catch (\Exception $e) {
            Log::error('Coupon creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء الكوبون'
            ];
        }
    }

    public function editCoupon($id)
    {
        return $this->model->find($id);
    }

    public function updateCoupon(array $requestData, $id)
    {
        try {
            $coupon = $this->model->find($id);
            if (!$coupon) {
                return [
                    'status' => false,
                    'message' => 'الكوبون غير موجود'
                ];
            }
            $data = Arr::only($requestData, [
                'code',
                'discount_value',
                'discount_type',
                'usage_limit',
                'start_date',
                'end_date',
                'description'
            ]);
            $coupon->update($data);
            return [
                'status' => true,
                'message' => 'تم تحديث الكوبون بنجاح',
                'data' => $coupon
            ];
        } catch (\Exception $e) {
            Log::error('Coupon update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }

    public function destroyCoupon($id)
    {
        try {
            $coupon = $this->model->find($id);
            if (!$coupon) {
                return [
                    'status' => false,
                    'message' => 'الكوبون غير موجود'
                ];
            }
            $coupon->delete();
            return [
                'status' => true,
                'message' => 'تم حذف الكوبون بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Coupon deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف الكوبون'
            ];
        }
    }
}
