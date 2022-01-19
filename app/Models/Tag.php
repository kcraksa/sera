<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Tag extends Model
{
    protected $connection = "mongodb";
    protected $table = "tags";
    protected $fillable = ["tag"];
}
