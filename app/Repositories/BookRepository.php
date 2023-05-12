<?php

namespace App\Repositories;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Support\Facades\DB;

class BookRepository
{
    protected $book;
    protected $author;
    protected $genre;
    protected $publisher;

    public function __construct(Book $book, Author $author, Genre $genre, Publisher $publisher)
    {
        $this->book = $book;
        $this->author = $author;
        $this->genre = $genre;
        $this->publisher = $publisher;
    }

    public function getBooks($search = null)
    {
        $bookObj = $this->book->select(
            'books.id',
            'books.title'
        );

        $bookObj->join('authors', 'books.author_id', '=', 'authors.id')
            ->join('genres', 'books.genre_id', '=', 'genres.id')
            ->join('publishers', 'books.publisher_id', '=', 'publishers.id');

        if (!empty($search->title)) {
            $bookObj->where('books.title', 'like', '%' . $search->title . '%');
        }

        if (!empty($search->author)) {
            $bookObj->where('authors.name', 'like', '%' . $search->author . '%');
        }

        if (!empty($search->genre)) {
            $bookObj->where('genres.name', '=', $search->genre);
        }

        if (!empty($search->isbn)) {
            $bookObj->where('books.isbn', '=', $search->isbn);
        }

        if (!empty($search->published)) {
            $bookObj->where('books.published', '=', date('Y-m-d', strtotime($search->published)));
        }

        if (!empty($search->publisher)) {
            $bookObj->where('publishers.name', 'like', '%' . $search->publisher . '%');
        }

        if (!empty($search->sortname) && !empty($search->sortorder)) {
            $bookObj->orderBy($search->sortname, $search->sortorder);
        }

        if (!empty($search->paginate) && is_numeric($search->paginate)) {
            return $bookObj->paginate($search->paginate);
        } else {
            return $bookObj->paginate(10);
        }
    }

    public function getBook($id)
    {
        return $this->book->select('id', 'title', 'author_id', 'genre_id', 'description', 'isbn', 'published', 'publisher_id')
            ->with(['author' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['genre' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['publisher' => function ($query) {
                $query->select('id', 'name');
            }])
            ->find($id);
    }

    public function addUpdateBook($data, $id = null)
    {
        DB::transaction(function () use ($data, $id) {
            // Inserting/Finding Unique Author, Genre, Publisher
            $author = $this->author->firstOrCreate([
                'name' => $data->author
            ]);

            $genre = $this->genre->firstOrCreate([
                'name' => $data->genre
            ]);

            $publisher = $this->publisher->firstOrCreate([
                'name' => $data->publisher
            ]);

            //Adding/Updating a Book record
            if (!isset($id)) {
                $bookObj = $this->book;
            } else {
                $bookObj = $this->book->find($id);
            }

            $bookObj->title = $data->title;
            $bookObj->author_id = $author['id'];
            $bookObj->genre_id = $genre['id'];
            $bookObj->description = $data->description;
            $bookObj->isbn = $data->isbn;
            $bookObj->published = date('Y-m-d', strtotime($data->published));
            $bookObj->publisher_id = $publisher['id'];
            $bookObj->save();
        });
    }
}
