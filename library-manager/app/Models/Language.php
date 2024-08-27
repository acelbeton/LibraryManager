<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $table = 'languages';
    protected $fillable = ['language_name', 'language_code'];
    public $timestamps = true;

    public function books()
    {
        return $this->hasMany(Book::class, 'default_language_id');
    }

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    public function genreTranslations()
    {
        return $this->hasMany(GenreTranslation::class);
    }

    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }

    /**
     * Accessors and Mutators
     */

    public function getFullLanguageAttribute()
    {
        return "{$this->language_name} ({$this->language_code})";
    }

    /**
     * Scopes
     */

    public function scopeByCode($query, $code)
    {
        return $query->where('language_code', $code);
    }

    public function scopeByName($query, $name)
    {
        return $query->where('language_name', 'like', "%{$name}%");
    }
}
