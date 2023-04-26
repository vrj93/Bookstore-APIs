<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportBookDataController extends Controller
{
    public function index($quantity)
    {
        try {
            $response = Http::get('https://fakerapi.it/api/v1/books?_quantity=' . $quantity);
            $books = $response['data'];

            foreach ($books as $book) {
                DB::transaction(function () use ($book) {
                    // Inserting/Finding Unique Author, Genre, Publisher
                    $author = Author::firstOrCreate([
                        'name' => $book['author']
                    ]);

                    $genre = Genre::firstOrCreate([
                        'name' => $book['genre']
                    ]);

                    $publisher = Publisher::firstOrCreate([
                        'name' => $book['publisher']
                    ]);

                    //Creating a Book record
                    $bookObj = new Book();
                    $bookObj->title = $book['title'];
                    $bookObj->author_id = $author['id'];
                    $bookObj->genre_id = $genre['id'];
                    $bookObj->description = $book['description'];
                    $bookObj->isbn = $book['isbn'];
                    $bookObj->published = date('Y-m-d', strtotime($book['published']));
                    $bookObj->publisher_id = $publisher['id'];
                    $bookObj->save();
                });
            }

            return response(['message' => "Books Imported successfully"], 201);
        } catch (Exception $ex) {
            return response(['message' => $ex->getMessage()], 500);
        }
    }
}
