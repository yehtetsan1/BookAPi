<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Validators\OrderValidator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OrderAndDetailResource;
use App\Http\Controllers\Base\BaseController as BaseController;

class OrderController extends BaseController
{
    protected $orderValidator;


    public function __construct(
        OrderValidator $orderValidator
    ){
        $this->orderValidator = $orderValidator;
    }


    private function getRequestData($request){
        return $request->only(
            'order_id',
            'customer_id',
            'book_id',
            'qty'
        );
    }


    public function index(){

        $responseData = Order::get();

        $responseData = OrderAndDetailResource::collection($responseData);

        return $this->sendResponse($responseData,'Order List');
    }


    public function show(Request $request){

        $data = $this->getRequestData($request);

        $validator = $this->orderValidator->orderShowValidator($data);

        if($validator->fails()){
          return $this->sendError('Cannot show Order',$validator->errors());
        }

        $responseData = Order::where('id',$data['order_id'])->get();

        $responseData = OrderAndDetailResource::collection($responseData);

        return $this->sendResponse($responseData,'Order List');
    }


    public function create(Request $request){

        $data = $this->getRequestData($request);

        $validator = $this->orderValidator->orderCreateValidation($data);

        if($validator->fails()){
          return $this->sendError('Cannot Create Order',$validator->errors());
        }

        $bookData = Book::where('id',$data['book_id'])->first()->toArray();

        $order = $this->getOrderData($data,$bookData);

        $orderData = Order::create($order);

        $orderDetail = $this->getOrderDetailsData($data,$orderData);

        $orderDetailData = OrderDetail::create($orderDetail);

        $responseData = Order::where('id',$orderData->id)->with('details')->get();

        return $this->sendResponse($responseData,'Order Created');
    }

    private function getOrderData($data,$bookData){
        return [
            'customer_id' => $data['customer_id'],
            'amount' => $data['qty'] * $bookData['price'],
            'date' => Carbon::now(),
        ];
    }

    private function getOrderDetailsData($data,$orderData){
        return [
            'order_id' => $orderData['id'],
            'book_id' => $data['book_id'],
            'qty' => $data['qty']
        ];
    }
}
