<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        // Chain the title filter with the match for different filters
        $books = Book::when($title, function ($query, $title) {
            return $query->title($title);
        });


        // Apply the filter using match statement
        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6month' => $books->highestRatedLast6Months(),
            default => $books->latest(), // Assuming `latest()` orders by `created_at` desc
        };

        // Fetch the books based on the filters applied
        $books = $books->get();

        // Return the books data to the view
        return view('books.index', [
            'books' => $books,
        ]);
    }

    public function show(Book $book)
    {
        return view('books.show', [
            'book' => $book->load([
                'reviews' => fn($query) => $query->latest()
            ]),
        ]);
    }
}
