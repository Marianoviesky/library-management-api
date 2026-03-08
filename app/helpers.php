<?php

if (!function_exists('formatCents')) {
    function formatCents(int $cents): string {
        return '$' . number_format($cents / 100, 2);
    }
}

if (!function_exists('json')) {
    function json($data, $status = 200) {
        return response()->json($data, $status);
    }
}
if (!function_exists('borrowBook')) {
    function borrowBook($book, $user) {
        return \App\Facades\Borrowing::borrowBook($book, $user);
    }
}
