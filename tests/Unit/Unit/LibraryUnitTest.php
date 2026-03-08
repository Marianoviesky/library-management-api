<?php

namespace Tests\Unit\Unit;

use PHPUnit\Framework\TestCase;
use App\Enums\BookStatus;
use App\Models\Book;
use App\Casts\PriceCast;

class LibraryUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    // Test 1 : Le Helper formatCents()
    public function test_format_cents_helper_works()
    {
        // On vérifie que 1299 centimes donne bien $12.99
        $this->assertEquals('$12.99', formatCents(1299));
        $this->assertEquals('$0.05', formatCents(5));
    }

     // Test 2 : L'Enum BookStatus
     public function test_book_status_enum_has_correct_values()
     {
         $this->assertEquals('available', BookStatus::AVAILABLE->value);
         $this->assertEquals('borrowed', BookStatus::BORROWED->value);
     }

      // Test 3 : Le Custom Cast PriceCast
    public function test_price_cast_converts_correctly()
    {
        $cast = new PriceCast();
        $model = new Book();

        // Si on lit 1599 depuis la BDD, on veut 15.99
        $this->assertEquals(15.99, $cast->get($model, 'price', 1599, []));

        // Si on écrit 15.99 dans le code, on veut 1599 en BDD
        $this->assertEquals(1599, $cast->set($model, 'price', 15.99, []));
    }
}
