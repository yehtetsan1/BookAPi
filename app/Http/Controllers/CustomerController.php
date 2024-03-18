<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Base\BaseController as BaseController;

class CustomerController extends BaseController
{
    private function getData($request){

        return $request->only(
            'key',
            'customer_id',
            'name',
            'address',
            'city'
        );
    }

    public function index(Request $request){

        $searchKey = $this->getData($request);

        if(isset($searchKey['key'])){
            $customers = Customer::where('name','like','%'.$searchKey['key'].'%')
                                ->orWhere('address','like','%'.$searchKey['key'].'%')
                                ->orWhere('city','like','%'.$searchKey['key'].'%');
        }

        elseif(isset($searchKey['name'])){
            $customers = Customer::where('name','like','%'.$searchKey['name'].'%');
        }

        elseif(isset($searchKey['address'])){
            $customers = Customer::where('address','like','%'.$searchKey['address'].'%');
        }

        elseif(isset($searchKey['city'])){
            $customers = Customer::where('city','like','%'.$searchKey['city'].'%');
        }

        else{
            $customers = Customer::query();
        }

        $customers = $customers->orderBy('created_at','desc')->get();

        return $this->sendResponse($customers,'Customer List',$customers->count());
    }


    public function create(Request $request){

        $data = $this->getData($request);

        $validator = $this->customerCreateValidation($data);

        if($validator->fails()){
            return $this->sendError('Cannot Create Customer',$validator->errors());
        }

        else{
            $createCustomer = $validator->validated();

            $createdCustomer = Customer::create($createCustomer);

            return $this->sendResponse($createdCustomer,'Customer Created Successfully!');
        }
    }


    public function delete(Request $request){

        $data = $this->getData($request);

        $validator = $this->validationForDelete($data);

        if($validator->fails()){
            return $this->sendError('Cannot Delete Customer',$validator->errors());
        }

        Customer::find($data['customer_id'])->delete();

        return $this->sendResponse([],'Customer Deleted Successfully!');
    }


    public function update(Request $request){

        $data = $this->getData($request);

        $validator = $this->customerUpdateValidation($data);

        if($validator->fails()){
            return $this->sendError('Cannot Update Customer',$validator->errors());
        }

        $updateData = $validator->validated();

        $updateData = collect($updateData)->except('customer_id')->toArray();

        Customer::find($data['customer_id'])->update($updateData);

        $updatedCustomer = Customer::find($data['customer_id']);

        return $this->sendResponse($updatedCustomer,'Customer Updated Successfully',$updatedCustomer->count());
    }

    private function customerCreateValidation($request){
        return Validator::make($request,[
            'name' => 'required',
            'address' => 'nullable',
            'city' => 'nullable'
        ]);
    }

    private function customerUpdateValidation($request){
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

    private function validationForDelete($request){
        // dd($request);
        return Validator::make($request,[
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function (Builder $query) {
                                return $query->where('deleted_at',null);
                            })],
        ],[
            'customer_id.exists' => 'Customer Not Found!'
        ]);
    }


}
