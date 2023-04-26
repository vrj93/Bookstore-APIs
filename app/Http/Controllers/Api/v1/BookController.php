<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $books = Book::getBooks($request);

        if (!$books->isEmpty()) {
            $response = response(['data' => $books, 'message' => 'Books fetched successfully'], 200);
        } else {
            $response = response(['data' => $books, 'message' => 'No books found'], 200);
        }

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bookDetails = Book::getBook($id);

        if (!empty($bookDetails)) {
            $response = response(['data' => $bookDetails, 'message' => 'Book details fetched successfully'], 200);
        } else {
            $response = response(['data' => $bookDetails, 'message' => 'Something went wrong'], 500);
        }

        return $response;
    }

    public function addBook(ValidateBookRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // Inserting/Finding Unique Author, Genre, Publisher
                $author = Author::firstOrCreate([
                    'name' => $request->author
                ]);

                $genre = Genre::firstOrCreate([
                    'name' => $request->genre
                ]);

                $publisher = Publisher::firstOrCreate([
                    'name' => $request->publisher
                ]);

                //Creating a Book record
                $bookObj = new Book();
                $bookObj->title = $request->title;
                $bookObj->author_id = $author['id'];
                $bookObj->genre_id = $genre['id'];
                $bookObj->description = $request->description;
                $bookObj->isbn = $request->isbn;
                $bookObj->published = date('Y-m-d', strtotime($request->published));
                $bookObj->publisher_id = $publisher['id'];
                $bookObj->save();
            });

            return response(['message' => "Book saved successfully"], 201);
        } catch (Exception $ex) {
            return response(['message' => $ex->getMessage()], 500);
        }
    }

    public function updateBook(ValidateBookRequest $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                // Inserting/Finding Unique Author, Genre, Publisher
                $author = Author::firstOrCreate([
                    'name' => $request->author
                ]);

                $genre = Genre::firstOrCreate([
                    'name' => $request->genre
                ]);

                $publisher = Publisher::firstOrCreate([
                    'name' => $request->publisher
                ]);

                //Editing a Book record
                $bookObj = Book::find($id);
                $bookObj->title = $request->title;
                $bookObj->author_id = $author['id'];
                $bookObj->genre_id = $genre['id'];
                $bookObj->description = $request->description;
                $bookObj->isbn = $request->isbn;
                $bookObj->published = date('Y-m-d', strtotime($request->published));
                $bookObj->publisher_id = $publisher['id'];
                $bookObj->save();
            });

            return response(['message' => "Book updated successfully"], 200);
        } catch (Exception $ex) {
            return response(['message' => $ex->getMessage()], 500);
        }
    }

    public function deleteBook($id)
    {
        try {
            Book::destroy($id);
            return response(['message' => 'Book deleted successfully'], 200);
        } catch (Exception $ex) {
            return response(['message' => $ex->getMessage()], 500);
        }
    }
}
