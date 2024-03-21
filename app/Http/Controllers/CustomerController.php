<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Validators\CustomerValidator;
use App\Http\Resources\CustomerResource;
use App\Http\Controllers\Base\BaseController as BaseController;

class CustomerController extends BaseController
{
    protected $customerValidator;

    public function __construct(
        CustomerValidator $customerValidator
    ){
        $this->customerValidator = $customerValidator;
    }

    private function getRequestData($request){

        return $request->only(
            'page',
            'paginateBy',
            'key',
            'customer_id',
            'name',
            'address',
            'city'
        );
    }


    private function getCustomersByCondition(){

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

        return $customers;
    }


    public function index(Request $request){

        $searchKey = $this->getRequestData($request);

        $customers = $this->getCustomersByCondition($searchKey);

        if(isset($searchKey['page']) || isset($searchKey['paginateBy'])){

            $validator = $this->customerValidator->customerPageValidator($searchKey);

            if($validator->fails()){
                return $this->sendError('Pagination Error',$validator->errors());
            }

            $customers = $customers->orderBy('updated_at','desc')->paginate($searchKey['paginateBy'],'*','books',$searchKey['page']);
        }else{
            $customers = $customers->orderBy('updated_at','desc')->paginate();
        }

        $total = $customers->total();

        $currentPage = $customers->currentPage();

        $lastPage = $customers->lastPage();

        $customers = CustomerResource::collection($customers);

        return $this->sendResponse($customers,"Customer List",$total,$currentPage,$lastPage);
    }


    public function show(Request $request){

        $data = $this->getData($request);

        $validator = $this->customerValidator->customerShowValidator($data);

        if($validator->fails()){
            return $this->sendError('Cannot Show Customer List',$validator->errors());
        }

        $customer = Customer::find($data['customer_id']);

        $customer = new CustomerResource($customer);

        return $this->sendResponse($customer,'Book Reviews');
    }


    public function create(Request $request){

        $data = $this->getRequestData($request);

        $validator = $this->customerValidator->customerCreateValidator($data);

        if($validator->fails()){
            return $this->sendError('Cannot Create Customer',$validator->errors());
        }

        $attributes = $validator->validated();

        $customer = Customer::create($attributes);

        $customer = new CustomerResource($customer);

        return $this->sendResponse($customer,'Customer Created Successfully!');
    }


    public function delete(Request $request){

        $data = $this->getRequestData($request);

        $validator = $this->customerValidator->customerDeleteValidator($data);

        if($validator->fails()){
            return $this->sendError('Cannot Delete Customer',$validator->errors());
        }

        Customer::find($data['customer_id'])->delete();

        return $this->sendResponse([],'Customer Deleted Successfully!');
    }


    public function update(Request $request){

        $data = $this->getRequestData($request);

        $validator = $this->customerValidator->customerUpdateValidator($data);

        if($validator->fails()){
            return $this->sendError('Cannot Update Customer',$validator->errors());
        }

        $attributes = $validator->validated();

        $attributes = collect($attributes)->except('customer_id')->toArray();

        $customer = Customer::find($data['customer_id']);

        $customer->update($attributes);

        $customer = new CustomerResource($customer);

        return $this->sendResponse($customer,'Customer Updated Successfully');
    }
}
