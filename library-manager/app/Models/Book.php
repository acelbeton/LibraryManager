<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';
    protected $fillable = ['title','description','author_id', 'genre_id', 'publisher_id', 'default_language_id' , 'cover_image'];

    public $timestamps = true;

    public function author() {
        return $this->belongsTo(Author::class);
    }
    public function genre() {
        return $this->belongsTo(Genre::class);
    }
    public function publisher() {
        return $this->belongsTo(Publisher::class);
    }
    public function keywords() {
        return $this->belongsToMany(Keyword::class, 'book_keywords', 'book_id', 'keyword_id');
    }
    public function defaultLanguage() {
        return $this->belongsTo(Language::class, 'default_language_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($book) {
            $book->keywords()->detach();
        });
    }
}
