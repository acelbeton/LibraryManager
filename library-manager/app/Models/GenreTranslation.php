<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenreTranslation extends Model
{
    use HasFactory;

    protected $table = 'genre_translations';
    protected $fillable = ['genre_id', 'language_id', 'translated_name'];
    public $timestamps = true;
}
