<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $products = $this->productService->indexProduct($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'قائمة المنتجات',
            'data' => $products
        ]);
    }

    public function store(ProductRequest $request)
    {
        $result = $this->productService->storeProduct($request->validated());
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function show($id)
    {
        $product = $this->productService->editProduct($id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تفاصيل المنتج',
            'data' => $product
        ]);
    }

    public function update(ProductRequest $request, $id)
    {
        $result = $this->productService->updateProduct($request->validated(), $id);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->productService->destroyProduct($id);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
