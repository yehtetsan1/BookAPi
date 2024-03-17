<?php

namespace App\Models;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookReview extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['book_id','description','delete_at'];

    // public function book():BelongsTo
    // {
    //     return $this->belongsTo(Book::class);
    // }
}
