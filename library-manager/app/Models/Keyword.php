<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;

    protected $table = 'keywords';

    protected $fillable = ['keyword', 'language_id'];

    public function language() {
        return $this->belongsTo(Language::class);
    }

    public function books() {
        return $this->belongsToMany(Book::class, 'book_keywords', 'keyword_id', 'book_id');
    }
}
