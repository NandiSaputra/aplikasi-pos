<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';
    protected $fillable = ['supplier_id', 'purchase_date', 'total_price'];
    protected $casts = [
        'purchase_date' => 'date',
    ];
    

    public function supplier()
    {
        return $this->belongsTo(Suplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
