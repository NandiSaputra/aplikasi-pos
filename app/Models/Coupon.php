<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'usage_limit',
        'used_count',
        'expired_at',
    ];

    /**
     * Cek apakah kupon masih valid
     */
    public function isValid(): bool
    {
        $notExpired = is_null($this->expired_at) || Carbon::parse($this->expired_at)->isFuture();
        $underLimit = is_null($this->usage_limit) || $this->used_count < $this->usage_limit;

        return $notExpired && $underLimit;
    }

    /**
     * Hitung nilai diskon berdasarkan subtotal
     */
    public function calculateDiscount(int $subtotal): int
    {
        if ($this->type === 'percentage') {
            return floor($subtotal * ($this->value / 100));
        }

        return $this->value;
    }
}
