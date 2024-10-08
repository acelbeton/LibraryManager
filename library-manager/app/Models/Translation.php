<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'translated_title',
        'translated_description',
        'language_id',
    ];

    public function book(){
        return $this->belongsTo(Book::class);
    }
}
