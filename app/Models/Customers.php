<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Customers extends Model
{
    protected $connection = "mongodb";
}
