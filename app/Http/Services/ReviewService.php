<?php

namespace App\Http\Services;

use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    public function indexReview($search = null, $perPage = 10)
    {
        return Review::with(['product', 'customer'])
            ->when($search, function ($query) use ($search) {
                $query->where('comment', 'like', "%{$search}%");
            })->paginate($perPage);
    }

    public function editReview($id)
    {
        return Review::with(['product', 'customer'])->find($id);
    }

    public function storeReview(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'product_id',
                'customer_id',
                'rating',
                'comment',
            ]);
            $review = Review::create($data);
            return [
                'status' => true,
                'message' => 'تم إضافة التقييم بنجاح',
                'data' => $review
            ];
        } catch (\Exception $e) {
            Log::error('Review creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إضافة التقييم'
            ];
        }
    }

    public function updateReview(array $requestData, $id)
    {
        try {
            $review = Review::find($id);
            if (!$review) {
                return [
                    'status' => false,
                    'message' => 'التقييم غير موجود'
                ];
            }
            $data = Arr::only($requestData, [
                'product_id',
                'customer_id',
                'rating',
                'comment',
            ]);
            $review->update($data);
            return [
                'status' => true,
                'message' => 'تم تحديث التقييم بنجاح',
                'data' => $review
            ];
        } catch (\Exception $e) {
            Log::error('Review update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }

    public function destroyReview($id)
    {
        try {
            $review = Review::find($id);
            if (!$review) {
                return [
                    'status' => false,
                    'message' => 'التقييم غير موجود'
                ];
            }
            $review->delete();
            return [
                'status' => true,
                'message' => 'تم حذف التقييم'
            ];
        } catch (\Exception $e) {
            Log::error('Review deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف التقييم'
            ];
        }
    }
}
