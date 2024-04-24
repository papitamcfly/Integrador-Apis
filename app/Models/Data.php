<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Data extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'data';

    protected $fillable = [
        'ID',
        'identificador',
        'valor',
        'unidad'
    ];
}

