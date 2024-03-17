<?php

namespace App\Models;

use App\Models\Book;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['order_id','book_id','qty','deleted_at'];

    public function book():BelongsTo{
        return $this->belongsTo(Book::class);
    }

    public function order():BelongsTo{
        return $this->belongsTo(Order::class);
    }

}
