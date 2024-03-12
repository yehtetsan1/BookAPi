<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(){
        $data = Customer::all();
        if(empty($data)){
            return response()->json($data, 204);
        }else{
            return response()->json($data, 200);
        }
    }

    public function create(Request $request){
        $this->customerValidation($request);
        $newCustomer = $this->getData($request);
        $createdCustomer = Customer::create($newCustomer);
        return response()->json($createdCustomer, 201);
    }

    public function delete(Request $request){
        $this->validationForDelete($request);
        Customer::where('id',$request->id)->delete();
        return response()->json('Deleted Successfully', 200);
    }

    public function search($key){
        $searchData = Customer::where('name','like','%'.$key.'%')
                ->orWhere('address','like','%'.$key.'%')
                ->orWhere('city','like','%'.$key.'%')
                ->get();
        return response()->json($searchData, 200);
    }

    public function update(Request $request){
        $this->customerValidation($request);
        $updateData = $this->getData($request);
        Customer::where('id',$request->id)->update($updateData);
        $updatedCustomer = Customer::where('id',$request->id)->get();
        return response()->json(['updated successfully',$updatedCustomer], 200);

    }

    private function getData($request){
        return ['name' => $request->name,
        'address' => $request->address,
        'city' => $request->city];
    }

    private function customerValidation($request){
        Validator::make($request->all(),[
            'name' => 'required',
        ])->validate();
    }

    private function validationForDelete($request){
        Validator::make($request->all(),[
            'id' => 'required'
        ],[
            'id.required' => 'need id to delete'
        ])->validate();
    }
}
