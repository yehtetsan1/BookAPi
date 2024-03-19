<?php

namespace App\Validators;

use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;

class OrderValidator
{
    public function orderCreateValidation($request){
        return Validator::make($request,[
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function (Builder $query) {
                                return $query->where('deleted_at',null);
                            })],
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })],
        ],[
            'book_id.exists' => 'Book Not Found',
            'customer_id.exists' => 'Customer Not Found'
        ]);
    }
}
