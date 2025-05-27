<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;
    
    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id')
        ->with(['categoria','unidad']);
    }
}
