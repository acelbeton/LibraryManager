<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $table = 'genres';
    protected $fillable = ['name'];
    public $timestamps = true;

    public function books() {
        return $this->hasMany(Book::class);
    }
    public function genreTranslations() {
        return $this->hasMany(GenreTranslation::class);
    }

}
