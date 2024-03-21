<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BookReview;
use Illuminate\Http\Request;
use App\Validators\BookReviewValidator;
use App\Http\Resources\BookReviewResource;
use App\Http\Controllers\Base\BaseController as BaseController;

class BookReviewController extends BaseController
{
    protected $bookReviewValidator;

    public function __construct(
        BookReviewValidator $bookReviewValidator
    ){
        $this->bookReviewValidator = $bookReviewValidator;
    }

    private function getData($request){
        return $request->only(
            'book_id',
            'bookReview_id',
            'description'
        );
    }

    public function index(Request $request){

        $data = $this->getData($request);

        if(isset($data['book_id'])){
            $bookReviewList = BookReview::where('book_id',$data['book_id']);
        }
        else{
            $bookReviewList = BookReview::query();
        }

        $bookReviewList = $bookReviewList->orderBy('updated_at','desc')->get();

        $bookReviewList = BookReviewResource::collection($bookReviewList);

        return $this->sendResponse($bookReviewList,'Book Reviews',$bookReviewList->count());
    }

    public function show(Request $request){

        $data = $this->getData($request);

        $validator = $this->bookReviewValidator->bookReviewShowValidator($data);

        if($validator->fails()){
            return $this->sendError('Cannot Show BookReview',$validator->errors());
        }

        $bookReview = BookReview::where('id',$data['bookReview_id'])->get();

        $bookReview = BookReviewResource::collection($bookReview);

        return $this->sendResponse($bookReview,'Book Reviews');
    }


    public function create(Request $request){

        $data = $this->getData($request);

        $validator = $this->bookReviewValidator->bookReviewCreateValidator($data);

        if($validator->fails()){
          return $this->sendError('Cannot Create Book Review!',$validator->errors());
        }

        $attributes = $validator->validated();

        $bookReview = BookReview::create($attributes);

        return $this->sendResponse($bookReview,'Book Review Created');
    }


    public function delete(Request $request){

        $data = $this->getData($request);

        $validator = $this->bookReviewValidator->validationForDelete($data);

        if($validator->fails()){
            return $this->sendError('Cannot Delete Book Review!',$validator->errors());
        }

        BookReview::find($data['bookReview_id'])->delete();

        return $this->sendResponse([],'Book Review Deleted');
    }


    public function update(Request $request){

        $data = $this->getData($request);

        $validator = $this->bookReviewValidator->validationForUpdate($data);

        if($validator->fails()){
            return $this->sendError('Cannot Update Book Review!',$validator->errors());
        }

        $attributes = $validator->validated();

        $attributes = collect($attributes)->except('bookReview_id')->toArray();

        $bookReview = BookReview::find($data['bookReview_id']);

        $bookReview->update($attributes);

        return $this->sendResponse($bookReview,'Book Review Updated');
    }
}
