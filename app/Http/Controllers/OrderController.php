<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function create(Request $request){
        $this->orderValidation($request);

        $bookData = Book::where('id',$request->bookId)->first()->toArray();
        $order = $this->getOrderData($request,$bookData);
        $orderData = Order::create($order);
        $orderDetail = $this->getOrderDetailsData($request,$orderData);
        $orderDetailData = OrderDetail::create($orderDetail);

        return response()->json([
            'order detail created successfully',
            $orderData,
            $orderDetailData
        ], 201);
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
        Validator::make($request->all(),[
            'customerId' => 'required',
            'bookId' => 'required'
        ])->validate();
    }
}
