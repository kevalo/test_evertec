<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'request_id', 'process_url', 'status'];

    public const APPROVED_STATE = 'APPROVED';
    public const PENDING_STATE = 'PENDING';
    public const REJECTED_STATE = 'REJECTED';

    public const STATES_NAMES = [
        self::APPROVED_STATE => 'Aprobado',
        self::PENDING_STATE => 'Pendiente',
        self::REJECTED_STATE => 'Rechazado'
    ];

    public function getStatusAttribute($originalValue)
    {
        return self::STATES_NAMES[$originalValue];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
