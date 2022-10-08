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

    public const STATES_NAMES = [
        self::CREATED_STATE => 'Creada',
        self::PAYED_STATE => 'Pagada',
        self::REJECTED_STATE => 'Rechazada'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function request()
    {
        return $this->hasOne(Request::class, 'order_id');
    }

    public function getStatusAttribute($originalValue)
    {
        return self::STATES_NAMES[$originalValue];
    }
}
