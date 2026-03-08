<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function (){


     // Routes Publiques (Auth)
     Route::post('/auth/register', [AuthController::class, 'register']);
     Route::post('/auth/login', [AuthController::class, 'login']);

  // Routes Protégées (Nécessitent le Token Sanctum)
  Route::middleware('auth:sanctum')->group(function () {

      Route::apiResource('authors',AuthorController::class);
      Route::apiResource('books',BookController::class);
      Route::apiResource('categories',CategoryController::class);

       // Routes pour les emprunts
       Route::get('/borrowings', [BorrowingController::class, 'index']);
       Route::post('/borrowings', [BorrowingController::class, 'store']); // Emprunter
       Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'update']);
  });
});
