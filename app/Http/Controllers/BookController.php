<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with(['author', 'categories']);

        if ($request->filled('available')) { $query->available(); }
        if ($request->filled('author_id')) { $query->byAuthor($request->author_id); }
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        return new BookCollection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->safe()->except('category_ids'));

        // On attache les catégories si elles existent
        if ($request->has('category_ids')) {
            $book->categories()->attach($request->category_ids);
        }

        $book->load(['author', 'categories']);
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['author', 'categories']);
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request,Book $book)
    {
        $book->update($request->safe()->except('category_ids'));

        if ($request->has('category_ids')) {
            $book->categories()->sync($request->category_ids);
        }

        $book->load(['author', 'categories']);
        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete(); // Soft delete
        return response()->json(null, 204);
    }
}
