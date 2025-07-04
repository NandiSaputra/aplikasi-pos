<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = 'purchase_details';
    protected $fillable = ['purchase_id', 'product_id', 'buy_price', 'quantity', 'subtotal'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    // Auto tambah stok + update harga beli
    protected static function booted()
    {
        static::created(function ($item) {
            $item->product->increment('stock', $item->quantity);
            $item->product->update(['buy_price' => $item->buy_price]);
        });
    }
}
