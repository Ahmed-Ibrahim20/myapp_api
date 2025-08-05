<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('perPage', 10);
        $customers = $this->customerService->indexCustomer($search, $perPage);
        return response()->json([
            'status' => true,
            'message' => 'قائمة العملاء',
            'data' => $customers
        ]);
    }

    public function store(CustomerRequest $request)
    {
        $result = $this->customerService->storeCustomer($request->validated());
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function show($id)
    {
        $customer = $this->customerService->editCustomer($id);
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'العميل غير موجود'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'تفاصيل العميل',
            'data' => $customer
        ]);
    }

    public function update(CustomerRequest $request, $id)
    {
        $result = $this->customerService->updateCustomer($request->validated(), $id);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function destroy($id)
    {
        $result = $this->customerService->destroyCustomer($id);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
