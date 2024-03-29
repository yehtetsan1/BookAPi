<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['customer_id','amount','date','deleted_at'];

    public function customer():BelongsTo{
        return $this->belongsTo(Customer::class);
    }

    public function details():HasMany{
        return $this->hasMany(OrderDetail::class);
    }
}
