<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

use App\Models\Tag;

class Article extends Model
{
    protected $connection = "mongodb";
    protected $table = "articles";
    protected $fillable = ['title', 'category', 'content'];

    public function tags()
    {
        return $this->embedsMany(Tag::class);
    }
}
