<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Validators\BookValidator;
use App\Http\Resources\BookResource;
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

    private function getRequestData($request){
        return $request->only(
                            'page',
                            'paginateBy',
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


    private function getBooksByCondition($searchKey){

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

        return $bookList;
    }


    public function index(Request $request){

        $searchKey = $this->getRequestData($request);

        $bookList = $this->getBooksByCondition($searchKey);

        if(isset($searchKey['page']) || isset($searchKey['paginateBy'])){

            $validator = $this->bookValidator->bookPageValidator($searchKey);

            if($validator->fails()){
                return $this->sendError('Pagination Error',$validator->errors());
            }

            $bookList = $bookList->orderBy('updated_at','desc')->paginate($searchKey['paginateBy'],'*','books',$searchKey['page']);
        }else{
            $bookList = $bookList->orderBy('updated_at','desc')->paginate();
        }

        $total = $bookList->total();

        $currentPage = $bookList->currentPage();

        $lastPage = $bookList->lastPage();

        $bookList = BookResource::collection($bookList);

        return $this->sendResponse($bookList,"Book List",$total,$currentPage,$lastPage);
    }


    public function show(Request $request){

        $data = $this->getRequestData($request);

        $validator = $this->bookValidator->bookShowValidator($data);

        if($validator->fails()){
            return $this->sendError('Cannot Show Book',$validator->errors());
        }

        $book = Book::where('id',$data['book_id'])->first();

        $book = new BookResource($book);

        return $this->sendResponse($book,"Requested Book");
    }


    public function create(Request $request){

        $data = $this->getRequestData($request);

        $validator = $this->bookValidator->bookCreateValidation($data);

        if($validator->fails()){
            return $this->sendError('Book Create Failed',$validator->errors());
        }

        $attributes = $validator->validated();

        if(isset($data['cover_url'])){
            $attributes['cover_url'] = $this->imageStore($data['cover_url']);
        }

        $book = Book::create($attributes);

        $book = new BookResource($book);

        return $this->sendResponse($book,"Book Created Successfully");

    }


    public function delete(Request $request){

        $data = $this->getRequestData($request);

        $validator = $this->bookValidator->bookDeleteValidation($data);

        if($validator->fails()){
            return $this->sendError('Book Delete Failed',$validator->errors());
        }

        Book::find($data['book_id'])->delete();

        return $this->sendResponse([],'Book Deleted Successfully');
    }


    public function update(Request $request){

        $data = $this->getRequestData($request);

        $validator= $this->bookValidator->bookUpdateValidation($data);

        if($validator->fails()){
          return $this->sendError('Update Failed',$validator->errors());
        }

        $attributes = $validator->validated();

        if(isset($data['cover_url'])){

            $validator = $this->bookValidator->bookImageValidation($data);

            if($validator->fails()){
                return $this->sendError('Image Upload Failed!',$validator->errors());
            }

            $attributes['cover_url'] = $this->imageStore($data['cover_url'],$data['book_id']);
        }

        $attributes = Arr::except($attributes, ['book_id']);

        $book = Book::find($data['book_id']);

        $book->update($attributes);

        $book = new BookResource($book);

        return $this->sendResponse($book,'Updated Successfully');
    }

    public function imageUpload(Request $request){

        $data = $this->getRequestData($request);

        $validator = $this->bookValidator->bookImageValidation($data);

        if($validator->fails()){
          return $this->sendError('Image Upload Failed!',$validator->errors());
        }

        $attributes['cover_url'] = $this->imageStore($data['cover_url'],$data['book_id']);

        $book = Book::find($data['book_id']);

        $book->update($attributes);

        $book = new BookResource($book);

        return $this->sendResponse($book,"Image Uploaded Successfully!");
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
