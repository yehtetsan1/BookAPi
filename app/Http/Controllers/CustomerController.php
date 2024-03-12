<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function customer(){
        $data = Customer::all();
        return $data;
    }

    public function create(Request $request){
        $this->customerValidation($request);
        $newCustomer = $this->getData($request);
        Customer::create($newCustomer);
        $createdCustomer = Customer::get()->last();
        return ['Successfully Created',$createdCustomer];
    }

    public function delete(Request $request){
        Customer::where('id',$request->id)->delete();
        return ['Successfully Deleted'];
    }

    public function search($key){
        $data = Customer::where('name','like','%'.$key.'%')
                ->orWhere('address','like','%'.$key.'%')
                ->orWhere('city','like','%'.$key.'%')
                ->get();
        return $data;
    }

    public function update(Request $request){
        $this->customerValidation($request);
        $updateData = $this->getData($request);
        Customer::where('id',$request->id)->update($updateData);
        $updatedCustomer = Customer::where('id',$request->id)->get();
        return [
            'Updated Successfully',
            $updatedCustomer
        ];

    }

    private function getData($request){
        return ['name' => $request->name,
        'address' => $request->address,
        'city' => $request->city];
    }

    private function customerValidation($request){
        Validator::make($request->all(),[
            'name' => 'required',
            'address' => 'required',
            'city' => 'required'
        ])->validate();
    }
}
