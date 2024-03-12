<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function order(Request $request){
        $bookData = $this->getBookOrder($request);
        $order = $this->getOrderData($request,$bookData);
        $orderData = Order::create($order)->get()->last()->toArray();
        $orderDetail = $this->getOrderDetailsData($request,$orderData);
        $orderDetailData = OrderDetail::create($orderDetail)->first();
        return [
            'order detail created successfully',
            $orderData,
            $orderDetailData
        ];
    }

    private function getBookOrder($request){
        return Book::where('id',$request->bookId)->first()->toArray();
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
}
