<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInvoice extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $casts = ['sale_order_id' => 'array']; //what this


    public function saleOrders()
    {
        $this->hasMany(SaleOrder::class);
    }
}
