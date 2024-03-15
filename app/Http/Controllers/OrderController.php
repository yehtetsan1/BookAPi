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
    public function create(Request $request){
        $validator = $this->orderValidation($request);

        if($validator->fails()){
          return $this->sendError('Cannot Create Order',$validator->errors());
        }else{
            $bookData = Book::where('id',$request->bookId)->first()->toArray();
            $order = $this->getOrderData($request,$bookData);
            $orderData = Order::create($order);
            $orderDetail = $this->getOrderDetailsData($request,$orderData);
            $orderDetailData = OrderDetail::create($orderDetail);
            $responseData = [
                $orderData,
                $orderDetailData
            ];
            return $this->sendResponse($responseData,'Order Created');
        }
    }

    private function getOrderData($request,$bookData){
        return [
            'customer_id' => $request->customerId,
            'amount' => $request->qty * $bookData['price'],
            'date' => Carbon::now(),
        ];
    }

    private function getOrderDetailsData($request,$orderData){
        return [
            'order_id' => $orderData['id'],
            'book_id' => $request->bookId,
            'qty' => $request->qty
        ];
    }

    private function orderValidation($request){
        return Validator::make($request->all(),[
            'customerId' => ['required', Rule::exists('customers', 'id')->where(function (Builder $query) {
                                return $query->where('deleted_at',null);
                            })],
            'bookId' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })],
        ],[
            'bookId.exists' => 'Book Not Found',
            'customerId.exists' => 'Customer Not Found'
        ]);
    }
}
