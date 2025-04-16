<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Author::paginate(10); // This is the query to get all the authors and maybe paginate them 
        return response()->json($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:authors,name',
            'email' => 'required|email',
            'bio' => 'nullable|string'
        ]);
        $author = Author::create($request->all());
        return response()->json($author, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        //
        return $author->load('books');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        //
        $request->validate([
            'name' => 'required|string|exists:authors,name',
            'email' => 'required|email',
            'bio' => 'nullable|string'
        ]);
        $author->update($request->all());
        return response()->json($author);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request , Author $author)
    {
        // Only admin can delete authors
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $author->delete();
        return response()->json(null, 204);
    }
}
