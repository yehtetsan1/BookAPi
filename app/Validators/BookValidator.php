<?php

namespace App\Validators;

use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;

class BookValidator
{
    public function bookShowValidator($request){
        return Validator::make($request,[
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })]
        ],['book_id.exists' => 'Book Not Found']);
    }


    public function bookCreateValidation($request){

        return Validator::make($request,[
            'ISBN' => 'required|unique:books,ISBN',
            'author' => 'required',
            'title' => 'required',
            'price' => 'required',
            'cover_url' => 'nullable|mimes:png,jpg,jpeg|max:4000'
        ]);
    }


    public function bookDeleteValidation($request){

        return Validator::make($request,[
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })]
        ],['book_id.exists' => 'Book Not Found']);
    }


    public function bookUpdateValidation($request){

        $book_id = isset($request['book_id'])? $request['book_id']:'';
        return Validator::make($request,[
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })],
            'ISBN' => 'required|unique:books,ISBN,'.$book_id,
            'author' => 'required',
            'title' => 'required',
            'price' => 'required',
        ],[
            'book_id.exists' => 'Book Not Found'
        ]);
    }


    public function bookImageValidation($uploadData){

        return Validator::make($uploadData,[
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })],
            'cover_url' => 'required|mimes:png,jpg,jpeg|max:4000'
        ],[
            'book_id.exists' => 'Book Not Found'
        ]);
    }
}
