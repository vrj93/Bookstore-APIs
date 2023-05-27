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

    public function getBooks($search = [])
    {
        $bookObj = $this->book->select(
            'books.id',
            'books.title',
            'books.description'
        );

        $bookObj->join('authors', 'books.author_id', '=', 'authors.id')
            ->join('genres', 'books.genre_id', '=', 'genres.id')
            ->join('publishers', 'books.publisher_id', '=', 'publishers.id');

        if (!empty($search['title'])) {
            $titles = $search['title'];

            $bookObj->where(function ($query) use ($titles) {
                foreach ($titles as $title) {
                    $query->where('books.title', 'like', '%' . $title . '%');
                }
            });
        }

        if (!empty($search['content'])) {
            $contents = $search['content'];

            $bookObj->where(function ($query) use ($contents) {
                foreach ($contents as $content) {
                    $query->where('books.description', 'like', '%' . $content . '%');
                }
            });
        }

        if (!empty($search['author'])) {
            $authors = $search['author'];

            $bookObj->where(function ($query) use ($authors) {
                foreach ($authors as $author) {
                    $query->where('authors.name', 'like', '%' . $author . '%');
                }
            });
        }

        if (!empty($search['genre'])) {
            $genres = $search['genre'];

            $bookObj->where(function ($query) use ($genres) {
                foreach ($genres as $genre) {
                    $query->where('genres.name', 'like', '%' . $genre . '%');
                }
            });
        }

        if (!empty($search['isbn'])) {
            $bookObj->where('books.isbn', $search['isbn']);
        }

        if (!empty($search['published'])) {
            $year = $search['published']->year;
            $operator = $search['published']->duration === 'Before' ? '<' : '>';
            $bookObj->whereYear('books.published', $operator, $year);
        }

        if (!empty($search['publisher'])) {
            $publishers = $search['publisher'];

            $bookObj->where(function ($query) use ($publishers) {
                foreach ($publishers as $publisher) {
                    $query->where('publishers.name', 'like', '%' . $publisher . '%');
                }
            });
        }

        if (!empty($search['sortorder'])) {
            $bookObj->orderBy('title', $search['sortorder']);
        }

        if (!empty($search['paginate']) && is_numeric($search['paginate'])) {
            return $bookObj->paginate($search['paginate']);
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
