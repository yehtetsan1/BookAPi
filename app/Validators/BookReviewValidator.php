<?php
namespace App\Validators;

use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;

class BookReviewValidator

{
    public function bookReviewShowValidator($data){
        return Validator::make($data,[
                'bookReview_id' => ['required', Rule::exists('book_reviews', 'id')->where(function (Builder $query) {
                                    return $query->where('deleted_at',null);
                                })],
                ],[
                    'bookReview_id.exists' => 'Review Not Found!'
                ]);

    }


    public function bookReviewCreateValidator($data){
        return Validator::make($data,[
                  'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                                  return $query->where('deleted_at',null);
                              })],
                  'description' => 'required'
              ],[
                'book_id.exists' => 'The Book Not Found',
              ]);
    }

    public function validationForDelete($data){
        return Validator::make($data,[
            'bookReview_id' => ['required', Rule::exists('book_reviews', 'id')->where(function (Builder $query) {
                                    return $query->where('deleted_at',null);
                              })],
        ],[
            'bookReview_id.exists' => 'Review Not Found!'
        ]);
    }

    public function validationForUpdate($data){
        return Validator::make($data,[
            'bookReview_id' => ['required', Rule::exists('book_reviews', 'id')->where(function (Builder $query) {
                                    return $query->where('deleted_at',null);
                              })],
            'description' => 'required'
        ],[
            'bookReview_id.exists' => 'Review Not Found!'
        ]);
    }
}
