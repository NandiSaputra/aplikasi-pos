<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'type',            // 'percentage' atau 'fixed'
        'value',           // Nilai diskon (misal 10 untuk 10% atau 5000 untuk Rp5.000)
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relasi ke produk (many-to-many)
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Products::class, 'discount_product', 'discount_id', 'product_id');
    }
    
    

    // Mengecek apakah diskon masih aktif sekarang
    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active || !$this->start_date || !$this->end_date) {
            return false;
        }
    
        $now = now();
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->endOfDay();
    
        return $now->between($start, $end);
    }
    

    // Hitung potongan berdasarkan harga awal
    public function calculateDiscount(float $originalPrice): float
    {
        if (! $this->isCurrentlyActive()) return 0;

        return match ($this->type) {
            'percentage' => $originalPrice * ($this->value / 100),
            'fixed'      => min($this->value, $originalPrice), // tidak melebihi harga
            default      => 0,
        };
    }
}
