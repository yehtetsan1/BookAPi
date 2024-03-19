<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Validators\BookValidator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Base\BaseController as BaseController;

class BookController extends BaseController
{

    protected $bookValidator;

    public function __construct(
        BookValidator $bookValidator
    ){
        $this->bookValidator = $bookValidator;
    }

    private function getData($request){
        return $request->only(
            'key',
            'book_id',
            'ISBN',
            'title',
            'author',
            'price',
            'cover_url',
            'under',
            'over'
        );
    }

    public function index(Request $request){

        $searchKey = $this->getData($request);

        if(isset($searchKey['key'])){
            $bookList = Book::where('deleted_at',null)->where('author','like','%'.$searchKey['key'].'%')
                            ->orWhere('title','like','%'.$searchKey['key'].'%')
                            ->orWhere('price','like','%'.$searchKey['key'].'%');
        }

        elseif(isset($searchKey['title'])){
            $bookList = Book::where('title','like','%'.$searchKey['title'].'%');
        }

        elseif(isset($searchKey['author'])){
            $bookList = Book::where('author','like','%'.$searchKey['author'].'%');
        }

        elseif(isset($searchKey['under'])){
            $bookList = Book::where('price','<=',$searchKey['under']);
        }

        elseif(isset($searchKey['over'])){
            $bookList = Book::where('price','>=',$searchKey['over']);
        }

        else{
            $bookList = Book::query();
        }

        $bookList = $bookList->orderBy('updated_at','desc')->get();

        return $this->sendResponse($bookList,"Book List",$bookList->count());
    }


    public function create(Request $request){

        $data = $this->getData($request);

        $validator = $this->bookValidator->bookCreateValidation($data);

        if($validator->fails()){
            return $this->sendError('Book Create Failed',$validator->errors());
        }

        $dataToCreate = $validator->validated();

        if(isset($data['cover_url'])){
            $dataToCreate['cover_url'] = $this->imageStore($data['cover_url']);
        }

        $createdBook = Book::create($dataToCreate);

        return $this->sendResponse($createdBook,"Book Created Successfully");

    }


    public function delete(Request $request){

        $data = $this->getData($request);

        $validator = $this->bookValidator->bookDeleteValidation($data);

        if($validator->fails()){
            return $this->sendError('Book Delete Failed',$validator->errors());
        }

        Book::find($data['book_id'])->delete();

        return $this->sendResponse([],'Book Deleted Successfully');
    }


    public function update(Request $request){

        $data = $this->getData($request);

        $validator= $this->bookValidator->bookUpdateValidation($data);

        if($validator->fails()){
          return $this->sendError('Update Failed',$validator->errors());
        }

        $dataToUpdate = $validator->validated();

        if(isset($data['cover_url'])){

            $validator = $this->bookValidator->bookImageValidation($data);

            if($validator->fails()){
                return $this->sendError('Image Upload Failed!',$validator->errors());
            }

            $dataToUpdate['cover_url'] = $this->imageStore($data['cover_url'],$data['book_id']);
        }

        $dataToUpdate = Arr::except($dataToUpdate, ['book_id']);

        $updatedData = Book::find($data['book_id']);

        $updatedData->update($dataToUpdate);

        return $this->sendResponse($updatedData,'Updated Successfully');

    }

    public function imageUpload(Request $request){

        $data = $this->getData($request);

        $validator = $this->bookValidator->bookImageValidation($data);

        if($validator->fails()){
          return $this->sendError('Image Upload Failed!',$validator->errors());
        }

        $dataToUpload['cover_url'] = $this->imageStore($data['cover_url'],$data['book_id']);

        $uploadedData = Book::find($data['book_id']);

        $uploadedData->update($dataToUpload);

        return $this->sendResponse($uploadedData,"Image Uploaded Successfully!");
    }

    // For Create => No book_id // For Update => Need book_id
    public function imageStore($imageFile,$book_id = null){

      if($book_id){

          $bookData = Book::where('id',$book_id)->first();

          if($bookData->cover_url){
              Storage::delete('public/'.$bookData->cover_url);
          }
      }

      $fileName = uniqid().$imageFile->getClientOriginalName();

      $imageFile->storeAs('public',$fileName);

      return $fileName;
    }
}
