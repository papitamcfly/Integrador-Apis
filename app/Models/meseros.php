<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class  meseros extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    public $timestamps = false;
    protected $collection = 'mesero';
}