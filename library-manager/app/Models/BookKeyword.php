<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookKeyword extends Model
{
    use HasFactory;

    protected $table = 'book_keywords';

    protected $fillable = ['book_id', 'keyword_id'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }
}
