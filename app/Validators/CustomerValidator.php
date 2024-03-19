<?php

namespace App\Validators;

use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;

class CustomerValidator
{

    public function customerCreateValidation($request){
        return Validator::make($request,[
            'name' => 'required',
            'address' => 'nullable',
            'city' => 'nullable'
        ]);
    }

    public function customerUpdateValidation($request){
        return Validator::make($request,[
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

    public function validationForDelete($request){
        return Validator::make($request,[
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function (Builder $query) {
                                return $query->where('deleted_at',null);
                            })],
        ],[
            'customer_id.exists' => 'Customer Not Found!'
        ]);
    }
}
