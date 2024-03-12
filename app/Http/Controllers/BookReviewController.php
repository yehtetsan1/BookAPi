<?php

namespace App\Http\Controllers;

use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookReviewController extends Controller
{
    public function bookReview(){
        $bookReviewData = BookReview::all();
        if($bookReviewData->empty()){
            return 'No Data to Show';
        }else{
            return $bookReviewData;

        }
    }

    public function create(Request $request){
        $this->bookReviewValidator($request);
        $reviewData = $this->getReviewData($request);
        BookReview::create($reviewData);
        $bookReview = BookReview::get()->last();
        return [
            'Created Successfully',
            $bookReview
        ];
    }

    public function delete(Request $request){
        BookReview::where('id',$request->bookReviewId)->delete();
        return [
            'Deleted Successfully'
        ];
    }

    public function update(Request $request){
        $updateData = [
            'description' => $request->description
        ];
        BookReview::where('id',$request->id)->update($updateData);
        $updatedData = BookReview::where('id',$request->id)->get();
        return [
            'updated Successfully',
            $updatedData
        ];
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

}
