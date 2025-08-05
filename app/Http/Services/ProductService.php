<?php

namespace App\Http\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductService
{
    protected Product $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function indexProduct($search = null, $perPage = 10)
    {
        return $this->model->with(['images', 'variants.values'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })->paginate($perPage);
    }

    public function storeProduct(array $requestData)
    {
        DB::beginTransaction();
        try {
            $data = Arr::only($requestData, [
                'sku',
                'name',
                'slug',
                'description',
                'price',
                'compare_price',
                'cost_price',
                'weight',
                'dimensions',
                'user_add_id',
                'supplier_id',
                'category_id',
                'brand_id'
            ]);
            $data['user_add_id'] = Auth::id();
            // حفظ صورة المنتج الأساسية
            if (isset($requestData['images']) && $requestData['images'] instanceof \Illuminate\Http\UploadedFile) {
                $folder = public_path('product');
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }
                $filename = uniqid('product_') . '.' . $requestData['images']->getClientOriginalExtension();
                $requestData['images']->move($folder, $filename);
                $data['images'] = 'product/' . $filename;
            }

            $product = $this->model->create($data);

            // حفظ صور المنتج الفرعية
            if (!empty($requestData['product_images'])) {
                foreach ($requestData['product_images'] as $img) {
                    if (isset($img['url']) && $img['url'] instanceof \Illuminate\Http\UploadedFile) {
                        $folder = public_path('product_images');
                        if (!file_exists($folder)) {
                            mkdir($folder, 0777, true);
                        }
                        $filename = uniqid('product_img_') . '.' . $img['url']->getClientOriginalExtension();
                        $img['url']->move($folder, $filename);
                        $img['url'] = 'product_images/' . $filename;
                    }
                    $product->images()->create($img);
                }
            }

            // حفظ الفاريانت والقيم
            if (!empty($requestData['variants'])) {
                foreach ($requestData['variants'] as $variant) {
                    $variantData = Arr::only($variant, ['name', 'type']);
                    $productVariant = $product->variants()->create($variantData);
                    if (!empty($variant['values'])) {
                        foreach ($variant['values'] as $value) {
                            $productVariant->values()->create($value + ['product_id' => $product->id]);
                        }
                    }
                }
            }

            DB::commit();
            return [
                'status' => true,
                'message' => 'تم إنشاء المنتج بنجاح',
               // 'data' => $product->load(['images', 'variants.values'])
                'data' => $product
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء المنتج'
            ];
        }
    }

    public function editProduct($id)
    {
        return $this->model->with(['images', 'variants.values'])->find($id);
    }

    public function updateProduct(array $requestData, $id)
    {
        DB::beginTransaction();
        try {
            $product = $this->model->find($id);
            if (!$product) {
                return [
                    'status' => false,
                    'message' => 'المنتج غير موجود'
                ];
            }
            $data = Arr::only($requestData, [
                'sku',
                'name',
                'slug',
                'description',
                'price',
                'compare_price',
                'cost_price',
                'weight',
                'dimensions',
                'supplier_id',
                'category_id',
                'brand_id'
            ]);
            // تحديث صورة المنتج الأساسية
            if (isset($requestData['images']) && $requestData['images'] instanceof \Illuminate\Http\UploadedFile) {
                if ($product->images && file_exists(public_path($product->images))) {
                    @unlink(public_path($product->images));
                }
                $folder = public_path('product');
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }
                $filename = uniqid('product_') . '.' . $requestData['images']->getClientOriginalExtension();
                $requestData['images']->move($folder, $filename);
                $data['images'] = 'product/' . $filename;
            }
            $product->update($data);

            // حذف الصور القديمة وحفظ الجديدة
            if (isset($requestData['product_images'])) {
                foreach ($product->images as $oldImg) {
                    if ($oldImg->url && file_exists(public_path($oldImg->url))) {
                        @unlink(public_path($oldImg->url));
                    }
                }
                $product->images()->delete();
                foreach ($requestData['product_images'] as $img) {
                    if (isset($img['url']) && $img['url'] instanceof \Illuminate\Http\UploadedFile) {
                        $folder = public_path('product_images');
                        if (!file_exists($folder)) {
                            mkdir($folder, 0777, true);
                        }
                        $filename = uniqid('product_img_') . '.' . $img['url']->getClientOriginalExtension();
                        $img['url']->move($folder, $filename);
                        $img['url'] = 'product_images/' . $filename;
                    }
                    $product->images()->create($img);
                }
            }

            // حذف الفاريانت والقيم القديمة وحفظ الجديدة
            if (isset($requestData['variants'])) {
                foreach ($product->variants as $oldVariant) {
                    $oldVariant->values()->delete();
                }
                $product->variants()->delete();
                foreach ($requestData['variants'] as $variant) {
                    $variantData = Arr::only($variant, ['name', 'type']);
                    $productVariant = $product->variants()->create($variantData);
                    if (!empty($variant['values'])) {
                        foreach ($variant['values'] as $value) {
                            $productVariant->values()->create($value + ['product_id' => $product->id]);
                        }
                    }
                }
            }

            DB::commit();
            return [
                'status' => true,
                'message' => 'تم تحديث المنتج بنجاح',
                'data' => $product->load(['images', 'variants.values'])
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ];
        }
    }

    public function destroyProduct($id)
    {
        try {
            $product = $this->model->find($id);
            if (!$product) {
                return [
                    'status' => false,
                    'message' => 'المنتج غير موجود'
                ];
            }
            // حذف الصورة الأساسية من المجلد
            if ($product->images && file_exists(public_path($product->images))) {
                @unlink(public_path($product->images));
            }
            // حذف صور المنتج الفرعية من المجلد
            foreach ($product->images as $img) {
                if ($img->url && file_exists(public_path($img->url))) {
                    @unlink(public_path($img->url));
                }
            }
            $product->images()->delete();

            // حذف الفاريانت والقيم
            foreach ($product->variants as $variant) {
                $variant->values()->delete();
            }
            $product->variants()->delete();

            $product->delete();
            return [
                'status' => true,
                'message' => 'تم حذف المنتج بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Product deletion failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف المنتج'
            ];
        }
    }
}
