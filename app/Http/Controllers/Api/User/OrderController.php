<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\MovementType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\CustomerInfo;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        // customer info
        $customerInfo = CustomerInfo::updateOrCreate(
            [
                'phone_number' => $request->customer_info['phone_number'],
            ],
            [
                'first_name' => $request->customer_info['first_name'],
                'last_name' => $request->customer_info['last_name'],
                'city' => $request->customer_info['city'],
                'address' => $request->customer_info['address'],
                'nearby_landmark' => $request->customer_info['nearby_landmark'],
                'phone_number' => $request->customer_info['phone_number'],
            ]
        );
        // order
        $order = $customerInfo->orders()->create([
            'status' => OrderStatus::Pending->value,
            'total_price' => 0,
        ]);

        // customer info

        // order items
        $totalPrice = 0;
        foreach ($request->items as $item) {
            $productVariant = ProductVariant::find($item['variant_id']);

            $attributeValues = $productVariant->attributeValues()->get();
            $result = [];

            foreach ($attributeValues as $value) {
                $result[$value->type->name] = $value->value;
            }

            $order->items()->create([
                'product_variant_id' => $productVariant->id,
                'quantity' => $item['quantity'],
                'unit_price' => $productVariant->price,
                'attributes_snapshot' => $result,
            ]);
            $totalPrice += $productVariant->price * $item['quantity'];
            $productVariant->movements()->create([
                'type' => MovementType::Sale->value,
                'quantity' => $item['quantity'],
            ]);

        }
        $order->update(['total_price' => $totalPrice]);

        // payment
        $order->payment()->create([
            'method' => PaymentMethod::Cod->value,
            'status' => PaymentStatus::Pending->value,
            'amount' => $totalPrice,
        ]);

        return response()->json('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
