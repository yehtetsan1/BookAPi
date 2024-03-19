<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BookReview;
use Illuminate\Http\Request;
use App\Validators\BookReviewValidator;
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
        elseif(isset($data['bookReview_id'])){
            $bookReviewList = BookReview::where('id',$data['bookReview_id']);
        }
        else{
            $bookReviewList = BookReview::query();
        }

        $bookReviewList = $bookReviewList->orderBy('updated_at','desc')->get();

        return $this->sendResponse($bookReviewList,'Book Reviews',$bookReviewList->count());
    }


    public function create(Request $request){

        $data = $this->getData($request);

        $validator = $this->bookReviewValidator->bookReviewCreateValidator($data);

        if($validator->fails()){
          return $this->sendError('Cannot Create Book Review!',$validator->errors());
        }

        $dataToCreate = $validator->validated();

        $createdReview = BookReview::create($dataToCreate);

        return $this->sendResponse($createdReview,'Book Review Created');
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

        $dataToUpdate = $validator->validated();

        $dataToUpdate = collect($dataToUpdate)->except('bookReview_id')->toArray();

        $updatedBookReview = BookReview::find($data['bookReview_id']);

        $updatedBookre->update($dataToUpdate)-git();

        return $this->sendResponse($updatedBookReview,'Book Review Updated');
    }
}
