<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    protected static function getBooks($search = null)
    {
        $bookObj = Self::select(
            'books.id',
            'books.title'
        );

        $bookObj->leftjoin('authors', 'books.author_id', '=', 'authors.id')
            ->leftjoin('genres', 'books.genre_id', '=', 'genres.id')
            ->leftjoin('publishers', 'books.publisher_id', '=', 'publishers.id');

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

    protected static function getBook($id)
    {
        return Self::select('id', 'title', 'author_id', 'genre_id', 'description', 'isbn', 'published', 'publisher_id')
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
}
