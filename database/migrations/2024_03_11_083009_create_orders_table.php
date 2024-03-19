<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if(!Schema::hasTable('orders')){
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
                $table->double('amount')->nullable();
                $table->dateTime('date')->default(Carbon::now());
                $table->dateTime('deleted_at')->nullable();
                $table->dateTime('created_at')->default(Carbon::now());
                $table->dateTime('updated_at')->default(Carbon::now());

                $table->index('customer_id','idx_orders_customer_id');
                $table->index('amount','idx_orders_amount');
                $table->index('date','idx_orders_date');
                $table->index('deleted_at','idx_orders_deleted_at');
                $table->index('created_at','idx_orders_created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
