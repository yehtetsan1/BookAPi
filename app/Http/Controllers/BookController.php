<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function book(){
        $data = Book::all();
        return $data;
    }

    public function create(Request $request){
        $this->bookValidation($request);
        $newBook = $this->getData($request);
        $createdBook = Book::create($newBook)->get()->last();
        return [
            'Successfully Created',
            $createdBook
        ];
    }

    public function delete(Request $request){
        Book::where('id',$request->id)->delete();
        return ['Successfully Deleted'];}

    public function search($key){
        $data = Book::where('ISBN','like','%'.$key.'%')
                ->orWhere('author','like','%'.$key.'%')
                ->orWhere('title','like','%'.$key.'%')
                ->orWhere('price','like','%'.$key.'%')
                ->orWhere('cover_url','like','%'.$key.'%')
                ->get();
        return $data;
    }

    public function update(Request $request){
        $this->bookValidation($request);
        $updateData = $this->getData($request);
        Book::where('id',$request->id)->update($updateData);
        $updatedBook = Book::where('id',$request->id)->get();
        return [
            'Updated Successfully',
            $updatedBook
        ];

    }

    private function getData($request){
        return [
            'ISBN' => $request->ISBN,
            'author' => $request->author,
            'title' => $request->title,
            'price' => $request->price,
            'cover_url' => $request->cover_url,
        ];
    }

    private function bookValidation($request){
        Validator::make($request->all(),[
            'ISBN' => 'required',
            'author' => 'required',
            'title' => 'required',
            'price' => 'required',
            'cover_url' => 'required',
        ])->validate();
    }
}
