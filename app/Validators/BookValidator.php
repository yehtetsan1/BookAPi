<?php

namespace App\Validators;

use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;

class BookValidator
{
    public function bookPageValidator($data){
        return Validator::make($data,[
            'page' => 'required',
            'paginateBy' => 'required'
        ]);
    }


    public function bookShowValidator($data){
        return Validator::make($data,[
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })]
        ],['book_id.exists' => 'Book Not Found']);
    }


    public function bookCreateValidation($data){

        return Validator::make($data,[
            'ISBN' => 'required|unique:books,ISBN',
            'author' => 'required',
            'title' => 'required',
            'price' => 'required',
            'cover_url' => 'nullable|mimes:png,jpg,jpeg|max:4000'
        ]);
    }


    public function bookDeleteValidation($data){

        return Validator::make($data,[
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })]
        ],['book_id.exists' => 'Book Not Found']);
    }


    public function bookUpdateValidation($data){

        $book_id = isset($data['book_id'])? $data['book_id']:'';
        return Validator::make($data,[
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
