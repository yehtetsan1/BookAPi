<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Base\BaseController as BaseController;

class BookController extends BaseController
{

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
        $bookList = Book::all();
        if(isset($searchKey['key'])){
            $bookList = Book::where('deleted_at',null)->where('author','like','%'.$searchKey['key'].'%')
                            ->orWhere('title','like','%'.$searchKey['key'].'%')
                            ->orWhere('price','like','%'.$searchKey['key'].'%')
                            ->get();
        }
        if(isset($searchKey['title'])){
            $bookList = Book::where('title','like','%'.$searchKey['title'].'%')->get();
        }
        if(isset($searchKey['author'])){
            $bookList = Book::where('author','like','%'.$searchKey['author'].'%')->get();
        }
        if(isset($searchKey['under'])){
            $bookList = Book::where('price','<=',$searchKey['under'])->get();
        }
        if(isset($searchKey['over'])){
            $bookList = Book::where('price','>=',$searchKey['over'])->get();
        }
        return $this->sendResponse($bookList,"Book List",$bookList->count());
    }


    public function create(Request $request){
        $data = $this->getData($request);
        $validator = $this->bookCreateValidation($data);
        if($validator->fails()){
            return $this->sendError('Book Create Failed',$validator->errors());
        }
        $createData = $validator->validated();
        if(isset($data['cover_url'])){
            $validator = Validator::make($data,[
                            'cover_url' =>'mimes:png,jpg,jpeg|max:4000'
                        ]);
            if($validator->fails()){
                return $this->sendError('Image Upload Failed',$validator->errors());
            }
            $createData['cover_url'] = $this->imageStore($data['cover_url']);
        }
        $createdBook = Book::create($createData);
        return $this->sendResponse($createdBook,"Book Created Successfully");

    }


    public function delete(Request $request){
        $data = $this->getData($request);
        $validator = $this->bookDeleteValidation($data);
        if($validator->fails()){
            return $this->sendError('Book Delete Failed',$validator->errors());
        }
        Book::find($data['book_id'])->delete();
        return $this->sendResponse([],'Book Deleted Successfully');
    }


    public function update(Request $request){
        $data = $this->getData($request);
        $validator= $this->bookUpdateValidation($data);
        if($validator->fails()){
          return $this->sendError('Update Failed',$validator->errors());
        }
        $updateData = $validator->validated();
        $updateData['updated_at'] = Carbon::now();

        if(isset($data['cover_url'])){
            $validator = $this->bookImageValidation($data);
            if($validator->fails()){
                return $this->sendError('Image Upload Failed!',$validator->errors());
            }
            $updateData['cover_url'] = $this->imageStore($data['cover_url'],$data['book_id']);
        }

        $updateData = collect($updateData)->except('book_id')->toArray();
        Book::find($data['book_id'])->update($updateData);
        $updatedData = Book::find($data['book_id']);
        return $this->sendResponse($updatedData,'Updated Successfully');

    }

    public function imageUpload(Request $request){

        $uploadData = $this->getData($request);
        $validator = $this->bookImageValidation($uploadData);
        if($validator->fails()){
          return $this->sendError('Image Upload Failed!',$validator->errors());
        }
        $coverUrlData['cover_url'] = $this->imageStore($uploadData['cover_url'],$uploadData['book_id']);
        Book::find($uploadData['book_id'])->update($coverUrlData);
        $uploadedData = Book::find($uploadData['book_id']);
        return $this->sendResponse($uploadedData,"Image Uploaded Successfully!");
    }

    // For Create => No book_id // For Update => Need book_id
    public function imageStore($imageFile,$book_id = null){

      if($book_id != null){
          $bookData = Book::where('id',$book_id)->first();
          if($bookData->cover_url){
              Storage::delete('public/'.$bookData->cover_url);
          }
      }
      $fileName = uniqid().$imageFile->getClientOriginalName();
      $imageFile->storeAs('public',$fileName);
      return $fileName;
    }


    private function bookCreateValidation($request){

        return Validator::make($request,[
            'ISBN' => 'required|unique:books,ISBN',
            'author' => 'required',
            'title' => 'required',
            'price' => 'required',
        ]);
    }


    public function bookDeleteValidation($request){

        return Validator::make($request,[
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })]
        ],['book_id.exists' => 'Book Not Found']);
    }


    private function bookUpdateValidation($request){

        return Validator::make($request,[
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })],
            'ISBN' => 'nullable|unique:books,ISBN,'.$request['book_id'],
            'author' => 'nullable',
            'title' => 'nullable',
            'price' => 'nullable',
        ],[
            'book_id.exists' => 'Book Not Found'
        ]);
    }


    Private function bookImageValidation($uploadData){

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
