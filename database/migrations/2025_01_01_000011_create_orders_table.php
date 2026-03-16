<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default(OrderStatus::Pending->value);
            $table->decimal('total_price', 10, 2);
            $table->foreignId('customer_info_id')
                ->constrained('customer_info')
                ->restrictOnDelete();
            $table->string('shipping_first_name');
            $table->string('shipping_last_name');
            $table->text('shipping_address');
            $table->string('shipping_phone');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
