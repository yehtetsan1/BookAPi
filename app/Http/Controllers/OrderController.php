<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Base\BaseController as BaseController;

class OrderController extends BaseController
{
    private function getData($request){
        return $request->only(
            'customer_id',
            'book_id',
            'qty'
        );
    }
    public function create(Request $request){

        $data = $this->getData($request);

        $validator = $this->orderValidation($data);

        if($validator->fails()){
          return $this->sendError('Cannot Create Order',$validator->errors());
        }

        else{

            $bookData = Book::where('id',$data['book_id'])->first()->toArray();

            $order = $this->getOrderData($data,$bookData);

            $orderData = Order::create($order);

            $orderDetail = $this->getOrderDetailsData($data,$orderData);

            $orderDetailData = OrderDetail::create($orderDetail);

            $responseData = [
                $orderData,
                $orderDetailData
            ];

            return $this->sendResponse($responseData,'Order Created');
        }
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

    private function orderValidation($request){
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
