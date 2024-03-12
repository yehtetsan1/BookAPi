<?php

namespace App\Models;

use App\Models\BookReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'ISBN',
        'author',
        'title',
        'price',
        'cover_url',
        'deleted_at.'
    ];
    public function reviews():HasMany{
        return $this->hasMany(BookReview::class);
    }
}
