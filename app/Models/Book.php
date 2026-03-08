<?php

namespace App\Models;

use \App\Traits\LogsActivity;
use App\Casts\PriceCast;
use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_available' => 'boolean',
        'published_at' => 'date',
        'status' => BookStatus::class,
        'price' => PriceCast::class,
    ];

    public function author ()
    {
        return $this->belongsTo(Author::class);
    }
    public function categories ()
    {
        return $this->belongsToMany(Category::class);
    }
    public function borrowings ()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id',$authorId);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }
}
