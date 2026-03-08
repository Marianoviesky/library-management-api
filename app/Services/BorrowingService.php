<?php
namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use App\Enums\BookStatus;
use App\Events\BookBorrowed;
use Illuminate\Support\Facades\DB;
use Exception;

class BorrowingService
{
    public function borrowBook(Book $book, User $user): Borrowing
    {
        if (!$book->is_available || $book->status !== BookStatus::AVAILABLE) {
            throw new Exception("Ce livre n'est pas disponible pour l'emprunt.");
        }

        return DB::transaction(function () use ($book, $user) {

            $borrowing = Borrowing::create([
                'book_id' => $book->id,
                'user_id' => $user->id,
                'borrowed_at' => now(),
            ]);

            $book->update([
                'status' => BookStatus::BORROWED,
                'is_available' => false
            ]);

            BookBorrowed::dispatch($book->id, $user->id, now()->toDateTimeString());

            return $borrowing;
        });
    }

    public function returnBook(Borrowing $borrowing): Borrowing
    {
        if ($borrowing->returned_at !== null) {
            throw new Exception("Ce livre a déjà été retourné.");
        }

        return DB::transaction(function () use ($borrowing) {

            $borrowing->update(['returned_at' => now()]);

            $borrowing->book->update([
                'status' => BookStatus::AVAILABLE,
                'is_available' => true
            ]);


            return $borrowing;
        });
    }
}
