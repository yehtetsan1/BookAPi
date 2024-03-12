<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
