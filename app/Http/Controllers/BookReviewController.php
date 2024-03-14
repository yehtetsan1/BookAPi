<?php

namespace App\Http\Controllers;

use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Base\BaseController as BaseController;

class BookReviewController extends BaseController
{
    public function index(Request $request){
        if($request->book_id){
            $bookReviewList = BookReview::where('book_id',$request->book_id)->where('deleted_at',null)->get();
        }else{
            $bookReviewList = BookReview::where('deleted_at',null)->get();
        }
        return $this->sendResponse($bookReviewList,'Book Reviews',$bookReviewList->count());
    }

    public function create(Request $request){
        $validator = $this->bookReviewCreateValidator($request);
        if($validator->fails()){
          return $this->sendError('Cannot Create Book Review!',$validator->errors());
        }else{
          $reviewData = $this->getReviewData($request);
          $createdReview = BookReview::create($reviewData);
          return $this->sendResponse($createdReview,'Book Review Created',$createdReview->count());
        }
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

    private function bookReviewCreateValidator($request){
        return Validator::make($request->all(),[
                  'bookId' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                                  return $query->where('deleted_at',null);
                              })],
                  'description' => 'required'
              ],[
                'bookId.exists' => 'The book has been deleted',
              ]);
    }

    private function validationForDelete($request){
        Validator::make($request->all(),[
            'bookReviewId' => 'required'
        ],[
            'bookReviewId.required' => 'need id to delete'
        ])->validate();
    }

}
