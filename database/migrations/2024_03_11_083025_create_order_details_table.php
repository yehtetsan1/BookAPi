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
        if(!Schema::hasTable('order_details')){
            Schema::create('order_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
                $table->unsignedBigInteger('book_id');
                $table->foreign('book_id')->references('id')->on('books')->onUpdate('cascade')->onDelete('cascade');
                $table->tinyInteger('qty')->nullable();
                $table->dateTime('deleted_at')->nullable();
                $table->dateTime('created_at')->default(Carbon::now());
                $table->dateTime('updated_at')->default(Carbon::now());

                $table->index('order_id','idx_order_details_order_id');
                $table->index('book_id','idx_order_details_book_id');
                $table->index('deleted_at','idx_order_details_deleted_at');
                $table->index('created_at','idx_order_details_created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }

};
