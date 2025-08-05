<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Http\Services\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $brands = $this->brandService->indexBrand($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'قائمة البراندات',
            'data' => $brands
        ]);
    }

    public function store(BrandRequest $request)
    {
        $result = $this->brandService->storeBrand($request->validated());
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function show($id)
    {
        $brand = $this->brandService->editBrand($id);
        if (!$brand) {
            return response()->json([
                'status' => false,
                'message' => 'البراند غير موجود'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تفاصيل البراند',
            'data' => $brand
        ]);
    }

    public function update(BrandRequest $request, $id)
    {
        $result = $this->brandService->updateBrand($request->validated(), $id);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->brandService->destroyBrand($id);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
