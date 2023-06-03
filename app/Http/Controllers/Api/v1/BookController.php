<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateBookRequest;
use App\Models\Book;
use App\Repositories\BookRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    protected $book;
    protected $bookRepo;

    public function __construct(Book $book, BookRepository $bookRepo)
    {
        $this->book = $book;
        $this->bookRepo = $bookRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $titles = [];
        $authors = [];
        $contents = [];
        $genres = [];
        $publishers = [];
        $search = json_decode($request->search);

        foreach ($search->title as $title) {
            $titles[] = $title->text;
        }

        foreach ($search->author as $author) {
            $authors[] = $author->text;
        }

        foreach ($search->content as $content) {
            $contents[] = $content->text;
        }

        foreach ($search->genre as $genre) {
            $genres[] = $genre->text;
        }

        foreach ($search->publisher as $publisher) {
            $publishers[] = $publisher->text;
        }

        $serachArray = [
            'title' => $titles,
            'author' => $authors,
            'content' => $contents,
            'genre' => $genres,
            'isbn' => $search->isbn,
            'published' => $search->published,
            'publisher' => $publishers,
            'paginate' => $search->paginate
        ];

        $books = $this->bookRepo->getBooks($serachArray);

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
        $bookDetails = $this->bookRepo->getBook($id);

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
            $bookId = $this->bookRepo->addUpdateBook($request);

            return response(['data' => $bookId, 'message' => "Book saved successfully"], 200);
        } catch (Exception $ex) {
            return response(['message' => $ex->getMessage()], 500);
        }
    }

    public function updateBook(ValidateBookRequest $request, $id)
    {
        try {
            $this->bookRepo->addUpdateBook($request, $id);

            return response(['message' => "Book updated successfully"], 200);
        } catch (Exception $ex) {
            return response(['message' => $ex->getMessage()], 500);
        }
    }

    public function deleteBook($id)
    {
        try {
            $this->book::destroy($id);
            return response(['message' => 'Book deleted successfully'], 200);
        } catch (Exception $ex) {
            return response(['message' => $ex->getMessage()], 500);
        }
    }

    public function uploadCover(Request $request, Book $book)
    {
        if ($request->hasFile('cover')) {
            //Delect old cover
            $oldCoverPath = $book->cover != '' ? 'public/img/book_cover/' . $book->cover : '';

            if (Storage::exists($oldCoverPath)) {
                Storage::delete($oldCoverPath);
            }

            $cover = $request->file('cover');
            $extension = $cover->getClientOriginalExtension();
            $coverName = uniqid() . '.' . $extension;

            $cover->storeAs('public/img/book_cover', $coverName);

            $book->cover = $coverName;
            $book->save();

            $response = response(['message' => 'Cover saved successfully'], 200);
        } else {
            $response = response(['message' => 'No book cover found'], 400);
        }

        return $response;
    }
}
