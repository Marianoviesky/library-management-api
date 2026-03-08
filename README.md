
```markdown
# Library Management System - REST API

A robust, fully-featured REST API built with Laravel for managing a library system. This project demonstrates advanced Laravel architecture, clean code principles, and modern API development techniques.

## Features

This API was built utilizing advanced Laravel concepts:
- **Authentication & Authorization**: Token-based auth using **Laravel Sanctum** and fine-grained access control via **Policies**.
- **Service Layer & DB Transactions**: Complex business logic (borrowing a book) is encapsulated in a Service class and wrapped in database transactions to ensure data integrity.
- **Advanced Eloquent**: Heavy use of Relationships, Query Scopes, Custom Casts (e.g., `PriceCast`), PHP 8.1 Enums (`BookStatus`), and Soft Deletes.
- **API Resources & Form Requests**: Clean JSON responses with custom pagination metadata and strict request validation.
- **Generic Controller**: An abstract generic controller implementation to keep CRUD operations DRY (Don't Repeat Yourself).
- **Custom Middlewares**: Forces `Accept: application/json` headers and logs request execution time.
- **Events & Broadcasting**: Triggers a `BookBorrowed` event on a private WebSocket channel.
- **Automated Testing**: Comprehensive Feature and Unit test suite covering the entire API lifecycle.
- **Custom Artisan Command**: `php artisan library:stats` to retrieve quick library analytics.

---

## Tech Stack
- **PHP** 8.1+
- **Laravel** 10.x / 11.x
- **SQLite** (Default for quick setup) / MySQL / PostgreSQL
- **PHPUnit** (Testing)

---

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Mariano/library-api.git
   cd library-api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure to configure your database connection in the `.env` file (SQLite is recommended for local testing).*

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Start the local server**
   ```bash
   php artisan serve
   ```
   The API will be accessible at `http://127.0.0.1:8000/api/v1/`.

---

## API Endpoints

All endpoints are prefixed with `/api/v1/`. All requests (except Auth) require a Bearer Token in the `Authorization` header.

### 🔐 Authentication (Public)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register` | Register a new user and receive an API token |
| POST | `/auth/login` | Login and receive an API token |

### 📖 Books
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/books` | List books (Supports filters: `?available=1`, `?author_id=x`, `?category_id=x`) |
| GET | `/books/{id}` | Get a specific book |
| POST | `/books` | Add a new book |
| PUT/PATCH| `/books/{id}` | Update a book |
| DELETE | `/books/{id}` | Delete a book (Soft Delete) |

### ✍️ Authors & 🏷️ Categories
*Both follow standard CRUD operations similar to Books.*
- `/authors`
- `/categories`

### 🔄 Borrowings (Business Logic)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/borrowings` | List borrowings (Filters: `?active=1`, `?user_id=x`) |
| POST | `/borrowings` | Borrow a book (Body: `{"book_id": 1}`) |
| PATCH | `/borrowings/{id}/return` | Return a borrowed book (Protected by Policy) |

---

## 🧪 Testing

To run the automated test suite (Unit & Feature tests):
```bash
php artisan test
```

## 📊 Artisan Commands

To view quick library statistics via the terminal:
```bash
php artisan library:stats --period=month
```
```

---

