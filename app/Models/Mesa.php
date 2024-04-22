<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Mesa extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'mesas';

    protected $fillable = [
        'id',
        'propietario',
        'indicaciones',
    ];
}
