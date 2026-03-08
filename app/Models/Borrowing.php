<?php

namespace App\Models;

use \App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function bookborrowed ()
    {
        return $this->belongsTo(Book::class);
    }

    public function user ()
    {
        return $this->belongsTo(User::class);
    }
}
