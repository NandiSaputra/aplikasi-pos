<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
  
    protected $table = 'transactions'; // sesuaikan jika nama tabel bukan jamak

    protected $fillable = [
        'user_id',
        'invoice_number',
        'total_price',
        'paid_amount',
        'change_amount',
        'payment_method',
        'payment_status',
        'snap_token',
        'coupon_code', // ← ini penting
    'discount_amount',

    'product_discount_total'
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke detail item
    public function details()
    {
        return $this->hasMany(TransaksiDetail::class ,'transaction_id');
    }
}
