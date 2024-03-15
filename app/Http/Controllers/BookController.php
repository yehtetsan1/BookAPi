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

    public function index(){

        $bookList = Book::where('deleted_at',null)->get();
        return $this->sendResponse($bookList,"Book List",$bookList->count());
    }


    public function create(Request $request){

        $validator = $this->bookCreateValidation($request);
        if($validator->fails()){
            return $this->sendError('Book Create Failed',$validator->errors());
        }else{
            $newBook = $this->getData($request);
            if($request->hasFile('cover_url')){
                $validator = Validator::make($request->all(),[
                    'cover_url' =>'mimes:png,jpg,jpeg|max:4000'
                ]);
                if($validator->fails()){
                    return $this->sendError('Image Upload Failed',$validator->errors());
                }else{
                    $newBook['cover_url'] = $this->imageStore($request->cover_url);
                }
            }
            $createdBook = Book::create($newBook);
            return $this->sendResponse($createdBook,"Book Created Successfully",$createdBook->count());
        }
    }


    public function delete(Request $request){

        $validator = Validator::make($request->all(),[
                        'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })]
                    ],['book_id.exists' => 'This book has already been deleted']);
        if($validator->fails()){
            return $this->sendError('Book Delete Failed',$validator->errors());
        }else{
            $deleteBookData = ['deleted_at' => Carbon::now()];
            Book::where('id',$request->book_id)->update($deleteBookData);
            $deletedBookData = Book::where('id',$request->book_id)->get();
            return $this->sendResponse($deletedBookData,'Book Deleted Successfully',$deletedBookData->count());
        }
    }


    public function search(Request $request){

        $dbData = Book::where('deleted_at',null)->where('author','like','%'.$request->key.'%')
                ->orWhere('title','like','%'.$request->key.'%')
                ->orWhere('price','like','%'.$request->key.'%')
                ->get();
        $searchedData = $dbData->where('deleted_at',null);
        return $this->sendResponse($searchedData,'Result',$searchedData->count());
    }


    public function update(Request $request){

        $validator= $this->bookUpdateValidation($request);
        if($validator->fails()){
          return $this->sendError('Update Failed',$validator->errors());
        }else{
          $updateData = $this->getData($request);
          $updateData['updated_at'] = Carbon::now();
          if($request->hasFile('cover_url')){
              $validator = $this->bookImageValidation($request->all());
              if($validator->fails()){
                  return $this->sendError('Image Upload Failed!',$validator->errors());
              }else{
                  $updateData['cover_url'] = $this->imageStore($request->cover_url,$request->book_id);
              }
          }
          Book::where('id',$request->book_id)->update($updateData);
          $updatedData = Book::where('id',$request->book_id)->get();
          return $this->sendResponse($updatedData,'Updated Successfully',$updatedData->count());
        }
    }

    public function imageUpload(Request $request){

        $uploadData = $request->only(
            'book_id',
            'cover_url',
        );
        $validator = $this->bookImageValidation($uploadData);
        if($validator->fails()){
          return $this->sendError('Image Upload Failed!',$validator->errors());
        }else{
          $coverUrlData = [
              'cover_url' => $this->imageStore($uploadData['cover_url'],$uploadData['book_id'])
          ];
          Book::where('id',$uploadData['book_id'])->update($coverUrlData);
          $uploadedData = Book::where('id',$uploadData['book_id'])->get();
          return $this->sendResponse($uploadedData,"Image Uploaded Successfully!",$uploadedData->count());
        }
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


    private function getData($request){

        return [
            'ISBN' => $request->ISBN,
            'author' => $request->author,
            'title' => $request->title,
            'price' => $request->price,
        ];
    }

    private function bookCreateValidation($request){

        return Validator::make($request->all(),[
            'ISBN' => 'required|unique:books,ISBN,'.$request->id,
            'author' => 'required',
            'title' => 'required',
            'price' => 'required',
        ]);
    }

    private function bookUpdateValidation($request){

        return Validator::make($request->all(),[
            'book_id' => ['required', Rule::exists('books', 'id')->where(function (Builder $query) {
                            return $query->where('deleted_at',null);
                        })],
            'ISBN' => 'required|unique:books,ISBN,'.$request->book_id,
            'author' => 'required',
            'title' => 'required',
            'price' => 'required',
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
