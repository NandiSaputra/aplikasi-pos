<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suplier extends Model
{
    protected $table = 'suppliers';
    protected $fillable = ['name', 'phone', 'address'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
