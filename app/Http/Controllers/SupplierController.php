<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $service;

    public function __construct(SupplierService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $suppliers = $this->service->indexSupplier($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'قائمة الموردين',
            'data' => $suppliers
        ]);
    }

    public function show($id)
    {
        $supplier = $this->service->editSupplier($id);
        if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' => 'المورد غير موجود'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تفاصيل المورد',
            'data' => $supplier
        ]);
    }

    public function store(SupplierRequest $request)
    {
        $result = $this->service->storeSupplier($request->validated());
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function update(SupplierRequest $request, $id)
    {
        $result = $this->service->updateSupplier($request->validated(), $id);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->service->destroySupplier($id);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
