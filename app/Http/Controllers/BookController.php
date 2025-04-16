<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $middleware = [
        'auth:sanctum' => ['except' => ['index', 'show']]
    ];

    public function index(Request $request)
    {
        $query = Book::with('author');

        if ($request->has('author')) {
            $query->where('author', $request->author);
        }

        if ($request->has('publisher')) {
            $query->where('publisher', $request->publisher);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        return $query->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|unique:books',
            'author' => 'required|exists:authors,name',
            'isbn' => 'required|unique:books|min:20',
            'genre' => 'required|string',
            'publisher' => 'required|string',
            'year' => 'required|integer'
        ]);
        $book = Book::create($validated);
        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'isbn' => 'sometimes|string|unique:books,isbn,'.$book->id.'|max:255',
            'author_id' => 'sometimes|exists:authors,id',
            'genre' => 'sometimes|string|max:255',
        ]);

        $book->update($validated);

        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request , Book $book)
    {
         // Only admin can delete books
         if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $book->delete();
        return response()->json(null, 204);
    }
}
