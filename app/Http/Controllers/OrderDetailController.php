<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Customer;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function testing(){
        $orderDetails = OrderDetail::all();
        return view('testing',compact('orderDetails'));
    }
}
