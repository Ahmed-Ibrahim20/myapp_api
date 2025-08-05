<?php

namespace App\Http\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function list()
    {
        return Order::with(['user', 'orderItems.product'])->paginate(15);
    }

    public function get($id)
    {
        return Order::with(['user', 'orderItems.product'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $orderData = $data;
            unset($orderData['order_items']);
            $order = Order::create($orderData);
            foreach ($data['order_items'] as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }
            return $this->get($order->id);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $order = Order::findOrFail($id);
            $orderData = $data;
            unset($orderData['order_items']);
            $order->update($orderData);
            // حذف الأصناف القديمة
            $order->orderItems()->delete();
            // إضافة الأصناف الجديدة
            foreach ($data['order_items'] as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }
            return $this->get($order->id);
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $order = Order::findOrFail($id);
            $order->orderItems()->delete();
            $order->delete();
            return true;
        });
    }
}
