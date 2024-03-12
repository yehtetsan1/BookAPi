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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->double('amount')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->dateTime('date')->default(Carbon::now());
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
