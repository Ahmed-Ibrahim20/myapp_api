<?php

namespace App\Http\Services;

use App\Models\Offer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OfferService
{
    protected Offer $model;

    public function __construct(Offer $model)
    {
        $this->model = $model;
    }

    public function indexOffer($search = null, $perPage = 10)
    {
        return $this->model->with(['userAdd', 'product'])
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })->paginate($perPage);
    }

    public function storeOffer(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'user_add_id',
                'product_id',
                'title',
                'description',
                'discount_type',
                'value',
                'start_at',
                'end_at'
            ]);
            $data['user_add_id'] = Auth::id();
            $offer = $this->model->create($data);
            return [
                'status' => true,
                'message' => 'تم إنشاء العرض بنجاح',
                'data' => $offer->load(['userAdd', 'product'])
            ];
        } catch (\Exception $e) {
            Log::error('Offer creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء العرض'
            ];
        }
    }

    public function editOffer($id)
    {
        return $this->model->with(['userAdd', 'product'])->find($id);
    }

    public function updateOffer(array $requestData, $id)
    {
        try {
            $offer = $this->model->find($id);
            if (!$offer) {
                return [
                    'status' => false,
                    'message' => 'العرض غير موجود'
                ];
            }
            $data = Arr::only($requestData, [
                'product_id',
                'title',
                'description',
                'discount_type',
                'value',
                'start_at',
                'end_at'
            ]);
            $offer->update($data);
            return [
                'status' => true,
                'message' => 'تم تحديث العرض بنجاح',
                'data' => $offer->load(['userAdd', 'product'])
            ];
        } catch (\Exception $e) {
            Log::error('Offer update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }

    public function destroyOffer($id)
    {
        try {
            $offer = $this->model->find($id);
            if (!$offer) {
                return [
                    'status' => false,
                    'message' => 'العرض غير موجود'
                ];
            }
            $offer->delete();
            return [
                'status' => true,
                'message' => 'تم حذف العرض بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Offer deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف العرض'
            ];
        }
    }
}
