<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantFeedback extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'restaurant_feedback';

    protected $fillable = ['table_id', 'order_id', 'rating', 'comment'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
