<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    // Mutator para el nombre del producto
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
    }
}
