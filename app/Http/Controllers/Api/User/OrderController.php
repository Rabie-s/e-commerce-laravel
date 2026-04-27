<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\MovementType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\CustomerInfo;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Find or create customer info
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

            // Create order
            $order = $customerInfo->orders()->create([
                'status' => OrderStatus::Pending->value,
                'total_price' => 0,
            ]);

            // Process order items with stock locking
            $totalPrice = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                // Lock the variant row to prevent race conditions
                $productVariant = ProductVariant::lockForUpdate()->find($item['variant_id']);

                if (! $productVariant) {
                    throw new \Exception("Product variant with ID {$item['variant_id']} not found.");
                }

                // Re-validate stock availability within the transaction
                $availableStock = $productVariant->stock;
                $requestedQuantity = $item['quantity'];

                if ($requestedQuantity > $availableStock) {
                    throw new \Exception(
                        "Not enough stock for variant SKU: {$productVariant->sku}. ".
                        "Available: {$availableStock}, Requested: {$requestedQuantity}"
                    );
                }

                // Get attribute values for snapshot
                $attributeValues = $productVariant->attributeValues()->get();
                $attributesSnapshot = [];
                foreach ($attributeValues as $value) {
                    $attributesSnapshot[$value->type->name] = $value->value;
                }

                // Create order item
                $orderItem = $order->items()->create([
                    'product_variant_id' => $productVariant->id,
                    'quantity' => $requestedQuantity,
                    'unit_price' => $productVariant->price ?? $productVariant->product->base_price,
                    'attributes_snapshot' => $attributesSnapshot,
                ]);

                // Calculate item total
                $itemTotal = $orderItem->unit_price * $requestedQuantity;
                $totalPrice += $itemTotal;

                // Record inventory movement (stock deduction)
                $productVariant->movements()->create([
                    'type' => MovementType::Sale->value,
                    'quantity' => $requestedQuantity,
                ]);

                // Prepare data for response
                $itemsData[] = [
                    'id' => $orderItem->id,
                    'variant_id' => $productVariant->id,
                    'sku' => $productVariant->sku,
                    'product_name' => $productVariant->product->name,
                    'quantity' => $requestedQuantity,
                    'unit_price' => (float) $orderItem->unit_price,
                    'attributes' => $attributesSnapshot,
                    'subtotal' => (float) $itemTotal,
                ];
            }

            // Update order total price
            $order->update(['total_price' => $totalPrice]);

            // Create payment record
            $payment = $order->payment()->create([
                'method' => PaymentMethod::Cod->value,
                'status' => PaymentStatus::Pending->value,
                'amount' => $totalPrice,
            ]);

            DB::commit();

            // Reload relationships for accurate response
            $order->load('customerInfo', 'items', 'payment');

            return response()->json([
                'message' => 'Order placed successfully',
                'data' => [
                    'tracking_number' => $order->tracking_number,
                    'order_id' => $order->id,
                    'status' => $order->status->label(),
                    'total_price' => (float) $order->total_price,
                    'customer' => [
                        'first_name' => $customerInfo->first_name,
                        'last_name' => $customerInfo->last_name,
                        'phone_number' => $customerInfo->phone_number,
                        'city' => $customerInfo->city,
                        'address' => $customerInfo->address,
                        'nearby_landmark' => $customerInfo->nearby_landmark,
                    ],
                    'items' => $itemsData,
                    'payment' => [
                        'method' => $payment->method->label(),
                        'status' => $payment->status->label(),
                        'amount' => (float) $payment->amount,
                    ],
                    'created_at' => $order->created_at->toIso8601String(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order placement failed: '.$e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $trackingNumber): JsonResponse
    {
        try {
            // Find order by UUID with relationships
            $order = Order::with([
                'customerInfo',
                'items.variant.product',
                'items.variant.attributeValues.type',
                'payment',
            ])->where('tracking_number', $trackingNumber)->first();

            if (! $order) {
                return response()->json([
                    'message' => 'Order not found',
                    'error' => "No order found with Tracking Number: {$trackingNumber}",
                ], 404);
            }

            // Prepare items data with attributes
            $itemsData = collect($order->items)->map(function ($item) {
                $attributesSnapshot = $item->attributes_snapshot ?? [];

                return [
                    'id' => $item->id,
                    'variant_id' => $item->product_variant_id,
                    'sku' => $item->variant->sku ?? 'N/A',
                    'product_name' => $item->variant->product->name ?? 'N/A',
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'attributes' => $attributesSnapshot,
                    'subtotal' => (float) ($item->unit_price * $item->quantity),
                ];
            });

            return response()->json([
                'message' => 'Order retrieved successfully',
                'data' => [
                    'tracking_number' => $order->tracking_number,
                    'order_id' => $order->id,
                    'status' => $order->status->label(),
                    'status_color' => $order->status->color(),
                    'total_price' => (float) $order->total_price,
                    'customer' => [
                        'first_name' => $order->customerInfo->first_name,
                        'last_name' => $order->customerInfo->last_name,
                        'phone_number' => $order->customerInfo->phone_number,
                        'city' => $order->customerInfo->city,
                        'address' => $order->customerInfo->address,
                        'nearby_landmark' => $order->customerInfo->nearby_landmark,
                    ],
                    'items' => $itemsData,
                    'payment' => [
                        'method' => $order->payment->method->label(),
                        'method_color' => $order->payment->method->color(),
                        'status' => $order->payment->status->label(),
                        'status_color' => $order->payment->status->color(),
                        'amount' => (float) $order->payment->amount,
                    ],
                    'created_at' => $order->created_at->format('Y/m/d'),
                    'updated_at' => $order->updated_at->toIso8601String(),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Order retrieval failed: '.$e->getMessage(), [
                'uuid' => $trackingNumber,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
