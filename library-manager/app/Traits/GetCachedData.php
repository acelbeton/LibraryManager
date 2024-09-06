<?php

namespace App\Traits;

use App\Models\Author;
use App\Models\Genre;
use App\Models\GenreTranslation;
use App\Models\Keyword;
use App\Models\Language;
use App\Models\Publisher;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

trait GetCachedData
{
    public function refreshCache($key, $modelClass) {
        Cache::forget($key);
        return Cache::remember($key, 60 * 60, function () use ($modelClass) {
            return $modelClass::all();
        });
    }

    public function getCachedData()
    {
        $authors = Cache::remember('authors', 60 * 60, function () {
            return Author::all();
        });

        $genres = Cache::remember('genres', 60 * 60, function () {
            return Genre::all();
        });

        $publishers = Cache::remember('publishers', 60 * 60, function () {
            return Publisher::all();
        });

        $languages = Cache::remember('languages', 60 * 60, function () {
            return Language::orderBy('language_name', 'asc')->get();
        });

        $keywords = Cache::remember('keywords', 60 * 60, function () {
            return Keyword::all();
        });

        $translations = Cache::remember('translations', 60 * 60, function () {
            return Translation::all();
        });

        $genreTranslations = Cache::remember('genreTranslations', 60 * 60, function () {
            return GenreTranslation::all();
        });

        return compact('authors', 'genres', 'publishers', 'languages', 'translations', 'genreTranslations', 'keywords');
    }
}
