<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use illuminate\database\eloquent\Relations\BelongsTo;


class Products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'category_id',
        'price',
        'stock',
        'image',
    ];
   


 public function category()
    {
        return $this->belongsTo(Categories::class ,'category_id');
    }
}

