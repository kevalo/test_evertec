<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'customer_id'];

    public const CREATED_STATE = 'CREATED';
    public const PAYED_STATE = 'PAYED';
    public const REJECTED_STATE = 'REJECTED';

    public function customer()
    {
        $this->belongsTo(customer::class, 'customer_id');
    }
}
