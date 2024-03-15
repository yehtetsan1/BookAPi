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
    public function index(){
        $customers = Customer::where('deleted_at',null)->get();
        return $this->sendResponse($customers,'Customer List',$customers->count());
    }

    public function create(Request $request){
        $validator = $this->customerCreateValidation($request);
        if($validator->fails()){
            return $this->sendError('Cannot Create Customer',$validator->errors());
        }else{
            $newCustomer = $this->getData($request);
            $createdCustomer = Customer::create($newCustomer);
            return $this->sendResponse($createdCustomer,'Customer Created Successfully!',$createdCustomer->count());
        }
    }

    public function delete(Request $request){
        $validator = $this->validationForDelete($request);
        if($validator->fails()){
            return $this->sendError('Cannot Delete Customer',$validator->errors());
        }else{
            Customer::where('id',$request->customerId)->update(['deleted_at'=>Carbon::now()]);
            $deletedCustomer = Customer::where('id',$request->customerId)->get();
            return $this->sendResponse($deletedCustomer,'Customer Deleted Successfully!',$deletedCustomer->count());
        }
    }

    public function search(Request $request){
        $customers = Customer::where('name','like','%'.$request->key.'%')
                ->orWhere('address','like','%'.$request->key.'%')
                ->orWhere('city','like','%'.$request->key.'%')
                ->get();
        $searchedCustomers = $customers->where('deleted_at',null);
        return $this->sendResponse($searchedCustomers,'Customers Search Result!',$searchedCustomers->count());
    }

    public function update(Request $request){
        $validator = $this->customerUpdateValidation($request);
        if($validator->fails()){
            return $this->sendError('Cannot Update Customer',$validator->errors());
        }else{
            $updateData = $this->getData($request);
            Customer::where('id',$request->customerId)->update($updateData);
            $updatedCustomer = Customer::where('id',$request->customerId)->get();
            return $this->sendResponse($updatedCustomer,'Customer Updated Successfully',$updatedCustomer->count());
        }

    }

    private function getData($request){
        return ['name' => $request->name,
        'address' => $request->address,
        'city' => $request->city];
    }

    private function customerCreateValidation($request){
        return Validator::make($request->all(),[
            'name' => 'required',
        ]);
    }

    private function customerUpdateValidation($request){
        return Validator::make($request->all(),[
            'customerId' => ['required', Rule::exists('customers', 'id')->where(function (Builder $query) {
                                return $query->where('deleted_at',null);
                            })],
        ],[
            'customerId.exists' => 'Customer Not Found!'
        ]);
    }

    private function validationForDelete($request){
        return Validator::make($request->all(),[
            'customerId' => ['required', Rule::exists('customers', 'id')->where(function (Builder $query) {
                                return $query->where('deleted_at',null);
                            })],
        ],[
            'customerId.exists' => 'Customer Not Found!'
        ]);
    }


}
