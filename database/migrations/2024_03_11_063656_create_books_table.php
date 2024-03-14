<?php

use Carbon\Carbon;
use App\Models\BookReview;
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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('ISBN',100)->unique();
            $table->string('author',100);
            $table->string('title',100);
            $table->double('price');
            $table->string('cover_url',100)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('created_at')->default(Carbon::now());
            $table->dateTime('updated_at')->default(Carbon::now());

            $table->index('author');
            $table->index('title');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }


};
