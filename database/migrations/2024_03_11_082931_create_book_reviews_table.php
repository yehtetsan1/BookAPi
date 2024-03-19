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
        if(!Schema::hasTable('book_reviews')){
            Schema::create('book_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('book_id');
                $table->foreign('book_id')->references('id')->on('books')->onUpdate('cascade')->onDelete('cascade');
                $table->longText('description',255)->nullable();
                $table->dateTime('deleted_at')->nullable();
                $table->dateTime('created_at')->default(Carbon::now());
                $table->dateTime('updated_at')->default(Carbon::now());

                $table->index('book_id','idx_book_reviews_book_id');
                $table->index('description','idx_book_reviews_description');
                $table->index('deleted_at','inx_book_reviews_deleted_at');
                $table->index('created_at','inx_book_reviews_created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_reviews');
    }
};
