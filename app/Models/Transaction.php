<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    // Indicar la tabla si no sigue el nombre por defecto
    protected $table = 'transactions';

    // Indicar los atributos que son asignables en masa
    protected $fillable = [
        'reference_code',
        'description',
        'value',
        'currency',
        'payment_method',
        'transaction_id',
        'transaction_status',
        'payer_id',
        'buyer_id',
    ];

    // Indicar los atributos que se deben tratar como números decimales
    protected $casts = [
        'value' => 'decimal:2',
    ];
}
