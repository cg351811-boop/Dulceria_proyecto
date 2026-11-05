<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'dulce_id',
        'user_id',
        'cantidad',
        'precio_unitario',
        'total',
    ];

    public function dulce()
    {
        return $this->belongsTo(Dulce::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
