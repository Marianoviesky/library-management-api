
**Project: Build a REST API for a "Library Management System"**

A simple API where users can manage books, authors, categories, and borrowings. This exercise covers the core Laravel concepts you'll need on the job.

---

## Database & Models

1. Create migrations for these tables:
    - `authors` — `id`, `name`, `bio` (text, nullable), `timestamps`, `softDeletes`
    - `categories` — `id`, `name`, `slug` (unique), `timestamps`
    - `books` — `id`, `title`, `isbn` (unique), `description` (text, nullable), `author_id` (FK), `status` (string, default `'available'`), `published_at` (date, nullable), `is_available` (boolean, default true), `price` (integer, stores cents), `timestamps`, `softDeletes`
    - `book_category` — pivot table linking books and categories
    - `borrowings` — `id`, `book_id` (FK), `user_id` (FK), `borrowed_at` (timestamp), `returned_at` (timestamp, nullable), `notes` (text, nullable), `timestamps` 
    
2. Create the corresponding Eloquent Models with relationships:
    - **Author**: `hasMany` → Books, use `SoftDeletes`
    - **Book**: `belongsTo` → Author, `belongsToMany` → Categories, `hasMany` → Borrowings, use `SoftDeletes`
    - **Category**: `belongsToMany` → Books
    - **Borrowing**: `belongsTo` → Book, `belongsTo` → User

3. Add **query scopes** on `Book`:
    - `scopeAvailable($query)` — where `is_available` is true
    - `scopeByAuthor($query, $authorId)` — filter by author
    - `scopePublished($query)` — where `published_at` is not null

4. Add **casts** on `Book`: `is_available` → `boolean`, `published_at` → `date`

5. Create a PHP **enum** `BookStatus: string` with cases: `AVAILABLE = 'available'`, `BORROWED = 'borrowed'`, `RESERVED = 'reserved'`, `DAMAGED = 'damaged'`. Cast the `status` field to this enum in the model.

6. Run `php artisan migrate`, open Tinker, create records, and verify relationships work (`$author->books`, `$book->categories`, etc.)

---

## Routing & Controllers

7. Create **API resource routes** in `routes/api.php` using `Route::apiResource()`, grouped under a `v1` prefix:
    - `/api/v1/authors` → `AuthorController`
    - `/api/v1/books` → `BookController`
    - `/api/v1/categories` → `CategoryController`

8. Implement `AuthorController`:
    - `index` — return paginated authors with their book count
    - `store` — create an author
    - `show` — return one author with their books loaded
    - `update` — update an author
    - `destroy` — soft delete an author

9. Implement `BookController`:
    - `index` — support optional query params `?available=1`, `?author_id=5`, `?category_id=3` using the scopes defined earlier
    - Full CRUD like `AuthorController`

10. All responses must be JSON with proper HTTP status codes: `200` success, `201` created, `404` not found, `204` delete.

---

## Validation & API Resources

11. Create **Form Requests**:
    - `StoreAuthorRequest` — `name` required|string|max:255, `bio` nullable|string
    - `UpdateAuthorRequest` — same but `name` is `sometimes`
    - `StoreBookRequest` — `title` required, `isbn` required|unique:books, `author_id` required|exists:authors,id, `price` required|integer|min:0, `category_ids` nullable|array, `category_ids.*` exists:categories,id
    - `UpdateBookRequest` — adapt accordingly

12. Create an abstract `BaseFormRequest` that overrides `failedValidation()` to return:

    ```json
    {
        "message": "Validation failed",
        "errors": { "field": ["error message"] }
    }
    ```

13. Create **API Resources**:
    - `AuthorResource` — returns `id`, `name`, `bio`, `books_count` (when loaded), `created_at`
    - `BookResource` — returns `id`, `title`, `isbn`, `description`, `price_formatted` (e.g. "$12.99" computed from the cents integer), `is_available`, `status`, `author` (nested AuthorResource when loaded), `categories` (collection), `published_at`
    - `BookCollection` — extends `ResourceCollection`, adds pagination metadata (`current_page`, `total`, `per_page`, `last_page`)

14. Refactor all controllers to use Form Requests for input and API Resources for output.

---

## Middleware & Helpers

15. Create a `ForceJsonResponse` **middleware** that sets `Accept: application/json` on every request. Register it globally for API routes.

16. Create a `LogRequestDuration` **middleware** with a `terminate()` method that logs how long each request took.

17. Create a `helpers.php` file with:
    - `json($data, $status = 200)` — shortcut for `response()->json()`
    - `formatCents(int $cents): string` — converts `1299` to `"$12.99"`

18. Register `helpers.php` in `composer.json`'s `autoload.files` array and run `composer dump-autoload`.

---

## Service Layer, Traits & Events

19. Create a `BorrowingService` with:
    - `borrowBook(Book $book, User $user): Borrowing` — check availability, create borrowing, set book status to `BORROWED`
    - `returnBook(Borrowing $borrowing): Borrowing` — set `returned_at`, set book status back to `AVAILABLE`
    - Throw exceptions if the book is unavailable or already returned

20. Create a `BorrowingController`:
    - `POST /api/v1/borrowings` — borrow a book (inject `BorrowingService` via constructor)
    - `PATCH /api/v1/borrowings/{borrowing}/return` — return a book
    - `GET /api/v1/borrowings` — list borrowings, filterable by `?user_id=` and `?active=1`

21. Create a **trait** `LogsActivity`:
    - Use `bootLogsActivity()` to hook into `created`, `updated`, `deleted` model events
    - Log to Laravel's logger: `"[ModelName] #id was created/updated/deleted"`
    - Apply it to `Book` and `Borrowing`

22. Create an **Event** `BookBorrowed` implementing `ShouldBroadcast`:
    - Properties: `$bookId`, `$userId`, `$borrowedAt`
    - Channel: `PrivateChannel('library')`
    - Dispatch it from `BorrowingService::borrowBook()`

---

## Authentication & Authorization

23. Set up **Laravel Sanctum** for token-based auth:
    - `POST /api/v1/auth/register` — create user, return token
    - `POST /api/v1/auth/login` — validate credentials, return token
    - Protect all other routes with `auth:sanctum` middleware

24. Create a **custom Cast** `PriceCast` implementing `CastsAttributes`:
    - `get()` — converts stored cents `1299` to float `12.99`
    - `set()` — converts input `12.99` to stored cents `1299`
    - Apply it to `Book::price`

25. Create a `BorrowingPolicy`:
    - `return`: only the user who borrowed can return the book
    - `viewAny`: any authenticated user
    - Register the policy and use `$this->authorize()` in the controller

---

## Testing & Artisan Commands

26. Write **Feature tests**:
    - `AuthorTest` — test CRUD operations and validation errors
    - `BookTest` — test index with filters (available, by author), test store with categories
    - `BorrowingTest` — test borrow flow, test can't borrow unavailable book, test return, test unauthorized return
    - Use `actingAs()` for auth, `assertJson()` and `assertStatus()` for assertions

27. Write **Unit tests**:
    - Test `formatCents()` helper
    - Test `BookStatus` enum values
    - Test `PriceCast` get/set logic

28. Create an Artisan **Command** `app:library-stats`:
    - Signature: `library:stats {--period=month}`
    - Outputs: total books, total borrowings this period, most borrowed book, top borrower
    - Use `$this->info()` and `$this->table()` for output

29. Run the full test suite with `php artisan test` — everything should pass.

---

## Facades, Abstractions & Transactions

30. Add a **Facade** for `BorrowingService`:
    - Create the Facade class with the proper `getFacadeAccessor()`
    - Bind the service in a Service Provider
    - Create a global helper `borrowBook($book, $user)` that uses the Facade under the hood

31. Create a custom **ResourceCollection** with a different pagination format. Instead of Laravel's default, return:

    ```json
    {
      "itemsReceived": 10,
      "curPage": 1,
      "nextPage": 2,
      "itemsTotal": 47,
      "pageTotal": 5,
      "items": [...]
    }
    ```

    Apply it to at least one endpoint.

32. Add a **GenericController** class that handles CRUD for any model. It should receive the model class, resource class, and form request class as parameters. Refactor at least `AuthorController` and `CategoryController` to delegate to it.

33. Wrap the entire borrowing flow (`borrowBook` and `returnBook`) in `DB::transaction()` to ensure atomicity — if any step fails, nothing gets persisted.

---
