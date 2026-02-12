<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentPaymentModel extends Model
{
    protected $table = 'payments';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'amount',
        'status',
        'email'
    ];
}
