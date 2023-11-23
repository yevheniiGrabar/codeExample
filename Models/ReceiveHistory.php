<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiveHistory extends Model
{
    use HasFactory;

    protected $hidden = ['product_id','receive_id','location_id','sub_location_id','product_id'];

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class,'location_id', 'id');
    }

    public function subLocation(): BelongsTo
    {
        return $this->belongsTo(SubLocation::class,'sub_location_id', 'id');
    }

    public function lines(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class,'order_line_id','id');
    }

    public function receive(): BelongsTo
    {
        return $this->belongsTo(Receive::class);
    }
}
