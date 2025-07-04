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
     
        'buy_price'
    ];
   

    public function getOriginalPriceAttribute()
{
    return $this->price;
}

  
 public function category()
    {
        return $this->belongsTo(Categories::class ,'category_id');
    }
    public function details()
{
    return $this->hasMany(TransaksiDetail::class, 'product_id');
}
public function purchaseDetails()
{
    return $this->hasMany(PurchaseDetail::class, 'product_id');
}
public function discounts()
{
    return $this->belongsToMany(Discount::class, 'discount_product', 'product_id', 'discount_id');
}

public function getActiveDiscount(): ?Discount
{
    return $this->discounts->first(fn($d) => $d->isCurrentlyActive());
}

public function getDiscountedPriceAttribute(): float
{
    $discount = $this->getActiveDiscount();
    if (! $discount) return $this->price;

    return $this->price - $discount->calculateDiscount($this->price);
}


}


