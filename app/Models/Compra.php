<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'dulce_id',
        'user_id',
        'proveedor',
        'cantidad',
        'precio_unitario',
        'total',
    ];

    /**
     * Relación con el dulce adquirido.
     */
    public function dulce()
    {
        return $this->belongsTo(Dulce::class);
    }

    /**
     * Relación con el usuario que registró la compra.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
