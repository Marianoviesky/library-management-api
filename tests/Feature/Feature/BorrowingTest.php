<?php

namespace Tests\Feature\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Author;
use App\Models\Book;
use App\Models\Borrowing;
use App\Enums\BookStatus;

class BorrowingTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_user_can_borrow_an_available_book()
    {
        $user = User::factory()->create();
        $author = Author::create(['name' => 'Test Author']);
        $book = Book::create([
            'title' => 'Livre Test',
            'isbn' => '111',
            'author_id' => $author->id,
            'price' => 1000,
            'is_available' => true,
            'status' => BookStatus::AVAILABLE->value
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/borrowings', [
            'book_id' => $book->id
        ]);

        // Vérifie la réponse
        $response->assertStatus(201);

        // Vérifie la Base de données !
        $this->assertDatabaseHas('borrowings', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'returned_at' => null
        ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'is_available' => false,
            'status' => 'borrowed'
        ]);
    }

    public function test_user_cannot_return_someone_elses_book()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create(); // Le voleur !

        $author = Author::create(['name' => 'Test Author']);
        $book = Book::create(['title' => 'Livre Test', 'isbn' => '222', 'author_id' => $author->id, 'price' => 1000]);

        $borrowing = Borrowing::create([
            'user_id' => $user1->id,
            'book_id' => $book->id,
            'borrowed_at' => now()
        ]);

        // Le User 2 essaie de rendre le livre du User 1
        $response = $this->actingAs($user2)->patchJson("/api/v1/borrowings/{$borrowing->id}/return");

        $response->assertStatus(403); // 403 = Interdit ! La Policy a fait son travail !
    }
}
