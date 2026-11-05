<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dulce extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'categoria_id',
        'precio',
        'stock',
        'descripcion',
        'imagen',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
