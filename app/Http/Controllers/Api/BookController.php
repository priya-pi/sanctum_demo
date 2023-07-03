<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Controllers\Api\BaseController as BaseController;


class BookController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $book = Book::with('user')->where('user_id',Auth::id())->get();
        // return [
        //     'success' => true,
        //     'data' => $book,
        //     'message' => "Data Retrived successfully",
        // ];
        return $this->sendResponse($book,"Data Retrive Successfully");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|regex:/^[a-zA-Z ]*$/',
            'description' => 'required',
            'no_of_page' => 'required|regex:/^[0-9]*$/',
            'author' => 'required|regex:/^[a-zA-Z ]*$/',
            'category' => 'required|regex:/^[a-zA-Z ]*$/',
            'price' => 'required|regex:/^[0-9]*$/',
            'released_year' => 'required|regex:/^[0-9]*$/|min:4|max:4',
            'status'=> 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $book = Book::create([
            "user_id" => Auth::id(),
            "name" => $request->get('name'),
            "description" =>  $request->get('description'),
            "no_of_page" => $request->get('no_of_page'),
            "author" => $request->get('author'),
            "category" => $request->get('category'),
            "price" => $request->get('price'),
            "released_year" => $request->get('released_year'),
            "status" => $request->get('status'),

        ]);
        return $this->sendResponse( new BookResource($book),
            'Book created successfully.'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        if (is_null($book)) {
            return $this->sendError('Book not found.');
        }
        return $this->sendResponse(new BookResource($book),
            'Books retrieved successfully.'
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $rules = [
            'name' => 'required|regex:/^[a-zA-Z ]*$/',
            'description' => 'required',
            'no_of_page' => 'required|regex:/^[0-9]*$/',
            'author' => 'required|regex:/^[a-zA-Z ]*$/',
            'category' => 'required|regex:/^[a-zA-Z ]*$/',
            'price' => 'required|regex:/^[0-9]*$/',
            'released_year' => 'required|regex:/^[0-9]*$/|min:4|max:4',
            'status'=> 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $book->update([

            "user_id" => Auth::id(),
            "name" => $request->input('name'),
            "description" =>  $request->input('description'),
            "no_of_page" => $request->input('no_of_page'),
            "author" => $request->input('author'),
            "category" => $request->input('category'),
            "price" => $request->input('price'),
            "released_year" => $request->input('released_year'),
            "status" => $request->input('status'),

        ]);
        return $this->sendResponse( new BookResource($book),
            'Book updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return $this->sendResponse([], 'Book deleted successfully.');
    }
}
