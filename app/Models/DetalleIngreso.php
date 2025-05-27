<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleIngreso extends Model
{
    use HasFactory;
    
    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id')
        ->with(['categoria','unidad']);
    }
    public function lote() {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
