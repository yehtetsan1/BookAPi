<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $validator = $this->validationForDelete($request);
        if($validator->fails()){
            return $this->sendError('Cannot Delete Book Review!',$validator->errors());
        }else{
            BookReview::where('id',$request->bookReviewId)->update(['deleted_at'=>Carbon::now()]);
            $deletedBookReview = BookReview::where('id',$request->bookReviewId)->get();
            return $this->sendResponse($deletedBookReview,'Book Review Deleted',$deletedBookReview->count());
        }
    }

    public function update(Request $request){
        $validator = $this->validationForUpdate($request);
        if($validator->fails()){
            return $this->sendError('Cannot Update Book Review!',$validator->errors());
        }else{
            $updateData = ['description' => $request->description];
            BookReview::where('id',$request->bookReviewId)->update($updateData);
            $updatedBookReview = BookReview::where('id',$request->bookReviewId)->get();
            return $this->sendResponse($updatedBookReview,'Book Review Updated',$updatedBookReview->count());
        }
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
                'bookId.exists' => 'The Book Not Found',
              ]);
    }

    private function validationForDelete($request){
        return Validator::make($request->all(),[
            'bookReviewId' => ['required', Rule::exists('book_reviews', 'id')->where(function (Builder $query) {
                                    return $query->where('deleted_at',null);
                              })],
        ],[
            'bookReviewId.exists' => 'Review Not Found!'
        ]);
    }

    private function validationForUpdate($request){
        return Validator::make($request->all(),[
            'bookReviewId' => ['required', Rule::exists('book_reviews', 'id')->where(function (Builder $query) {
                                    return $query->where('deleted_at',null);
                              })],
            'description' => 'required'
        ],[
            'bookReviewId.exists' => 'Review Not Found!'
        ]);
    }

}
