<?php

namespace App\Validators;

use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;

class CustomerValidator
{

    public function customerPageValidator($data){
        return Validator::make($data,[
            'page' => 'required',
            'paginateBy' => 'required'
        ]);
    }


    public static function customerShowValidator($data){
        return Validator::make($data,[
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function (Builder $query) {
                                return $query->where('deleted_at',null);
                            })],
        ]);

    }


    public function customerCreateValidator($data){
        return Validator::make($data,[
            'name' => 'required',
            'address' => 'nullable',
            'city' => 'nullable'
        ]);
    }


    public function customerUpdateValidator($data){
        return Validator::make($data,[
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function (Builder $query) {
                                return $query->where('deleted_at',null);
                            })],
            'name' => 'required',
            'address' => 'nullable',
            'city' => 'nullable'
        ],[
            'customer_id.exists' => 'Customer Not Found!'
        ]);
    }


    public function customerDeleteValidator($data){
        return Validator::make($data,[
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function (Builder $query) {
                                return $query->where('deleted_at',null);
                            })],
        ],[
            'customer_id.exists' => 'Customer Not Found!'
        ]);
    }
}
