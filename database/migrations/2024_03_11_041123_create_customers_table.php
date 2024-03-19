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
        if(!Schema::hasTable('customers')){
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name',100);
                $table->string('address',255)->nullable();
                $table->string('city',100)->nullable();
                $table->dateTime('deleted_at')->nullable();
                $table->dateTime('created_at')->default(Carbon::now());
                $table->dateTime('updated_at')->default(Carbon::now());

                $table->index('name','idx_customers_name');
                $table->index('address','idx_customers_address');
                $table->index('city','idx_customers_city');
                $table->index('deleted_at','idx_customers_deleted_at');
                $table->index('updated_at','idx_customers_updated_at');

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
