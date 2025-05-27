<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;
    
    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id')
        ->with(['categoria','unidad']);
    }
    public function venta() {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
    public function lote() {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
