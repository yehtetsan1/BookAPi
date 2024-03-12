<?php

namespace App\Http\Controllers;

use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookReviewController extends Controller
{
    public function index(){
        $bookReviewList = BookReview::all()->toArray();
        if($bookReviewList == []){
            return response()->json('No Data To Show', 204);
        }else{
            return response()->json($bookReviewList, 200);
        }
    }

    public function create(Request $request){
        $this->bookReviewValidator($request);
        $reviewData = $this->getReviewData($request);
        $bookReview = BookReview::create($reviewData);
        return response()->json($bookReview,201);
    }

    public function delete(Request $request){
        $this->validationForDelete($request);
        BookReview::where('id',$request->bookReviewId)->delete();
        return response()->json('Deleted Successfully!', 200);
    }

    public function update(Request $request){
        $updateData = [
            'description' => $request->description
        ];
        BookReview::where('id',$request->id)->update($updateData);
        $updatedBookReview = BookReview::where('id',$request->id)->get();
        return response()->json(['updated successfully',$updatedBookReview], 200);
    }


    private function getReviewData($request){
        return [
            'book_id' => $request->bookId,
            'description' => $request->description,
        ];
    }

    private function bookReviewValidator($request){
            Validator::make($request->all(),[
                'bookId' => 'required',
                'description' => 'required'
            ])->validate();
    }

    private function validationForDelete($request){
        Validator::make($request->all(),[
            'bookReviewId' => 'required'
        ],[
            'bookReviewId.required' => 'need id to delete'
        ])->validate();
    }

}
