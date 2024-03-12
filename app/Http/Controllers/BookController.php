<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(){
        $bookList = Book::all()->toArray();
        if($bookList == []){
            return response()->json('No Data To Show', 204);
        }else{
            return response()->json($bookList, 200);
        }
    }

    public function create(Request $request){
        $this->bookValidation($request);
        $newBook = $this->getData($request);

        if($request->hasFile('cover_url')){

            $fileName = uniqid().$request->file('cover_url')->getClientOriginalName();
            $request->file('cover_url')->storeAs('public',$fileName);
            $newBook['cover_url'] = $fileName;
        }

        $createdBook = Book::create($newBook);
        return response()->json($createdBook,201);
    }

    public function delete(Request $request){
        $this->validationForDelete($request);
        Book::where('id',$request->id)->delete();
        return response()->json('Deleted Successfully!', 200);
    }

    public function search($key){
        $searchData = Book::where('ISBN','like','%'.$key.'%')
                ->orWhere('author','like','%'.$key.'%')
                ->orWhere('title','like','%'.$key.'%')
                ->orWhere('price','like','%'.$key.'%')
                ->orWhere('cover_url','like','%'.$key.'%')
                ->get();
        return response()->json($searchData,200);
    }

    public function update(Request $request){
        $this->bookValidation($request);
        $updateData = $this->getData($request);
        if($request->hasFile('cover_url')){

            $BookData = Book::where('id',$request->id)->first();

            if($BookData->cover_url != null){
                Storage::delete('public/'.$BookData->cover_url);
            }

            $fileName = uniqid().$request->file('cover_url')->getClientOriginalName();
            $request->file('cover_url')->storeAs('public',$fileName);
            $updateData['cover_url'] = $fileName;
        }
        Book::where('id',$request->id)->update($updateData);
        $updatedBook = Book::where('id',$request->id)->get();
        return response()->json(['updated successfully',$updatedBook], 200);

    }

    private function getData($request){
        return [
            'ISBN' => $request->ISBN,
            'author' => $request->author,
            'title' => $request->title,
            'price' => $request->price,
        ];
    }

    private function bookValidation($request){
        Validator::make($request->all(),[
            'ISBN' => 'required|unique:books,ISBN,'.$request->id,
            'author' => 'required',
            'title' => 'required',
            'price' => 'required',
        ])->validate();
    }

    private function validationForDelete($request){
        Validator::make($request->all(),[
            'id' => 'required'
        ],[
            'id.required' => 'need id to delete'
        ])->validate();
    }
}
