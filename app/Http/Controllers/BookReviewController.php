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
    private function getData($request){
        return $request->only(
            'book_id',
            'bookReview_id',
            'description'
        );
    }

    public function index(Request $request){
        $data = $this->getData($request);
        $bookReviewList = BookReview::all();
        if(isset($data['book_id'])){
            $bookReviewList = BookReview::where('book_id',$data['book_id'])->get();
        }
        if(isset($data['bookReview_id'])){
            $bookReviewList = BookReview::where('id',$data['bookReview_id'])->get();
        }
        return $this->sendResponse($bookReviewList,'Book Reviews',$bookReviewList->count());
    }


    public function create(Request $request){
        $data = $this->getData($request);
        $validator = $this->bookReviewCreateValidator($data);
        if($validator->fails()){
          return $this->sendError('Cannot Create Book Review!',$validator->errors());
        }
        $reviewData = $validator->validated();
        $createdReview = BookReview::create($reviewData);
        return $this->sendResponse($createdReview,'Book Review Created');
    }


    public function delete(Request $request){
        $data = $this->getData($request);
        $validator = $this->validationForDelete($data);
        if($validator->fails()){
            return $this->sendError('Cannot Delete Book Review!',$validator->errors());
        }
        BookReview::find($data['bookReview_id'])->delete();
        return $this->sendResponse([],'Book Review Deleted');
    }


    public function update(Request $request){
        $data = $this->getData($request);
        $validator = $this->validationForUpdate($data);
        if($validator->fails()){
            return $this->sendError('Cannot Update Book Review!',$validator->errors());
        }
        $updateBookReview = $validator->validated();
        $updateBookReview = collect($updateBookReview)->except('bookReview_id')->toArray();
        BookReview::find($data['bookReview_id'])->update($updateBookReview);
        $updatedBookReview = BookReview::find($data['bookReview_id']);
        return $this->sendResponse($updatedBookReview,'Book Review Updated');
    }


    private function bookReviewCreateValidator($request){
        return Validator::make($request,[
                  'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                                  return $query->where('deleted_at',null);
                              })],
                  'description' => 'required'
              ],[
                'book_id.exists' => 'The Book Not Found',
              ]);
    }

    private function validationForDelete($request){
        return Validator::make($request,[
            'bookReview_id' => ['required', Rule::exists('book_reviews', 'id')->where(function (Builder $query) {
                                    return $query->where('deleted_at',null);
                              })],
        ],[
            'bookReview_id.exists' => 'Review Not Found!'
        ]);
    }

    private function validationForUpdate($request){
        return Validator::make($request,[
            'bookReview_id' => ['required', Rule::exists('book_reviews', 'id')->where(function (Builder $query) {
                                    return $query->where('deleted_at',null);
                              })],
            'description' => 'required'
        ],[
            'bookReview_id.exists' => 'Review Not Found!'
        ]);
    }

}
