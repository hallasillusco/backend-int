<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $appends = ['txt_nro'];

    public function getTxtNroAttribute() {
        // $kard = '00000'.$this->nro;
        // $number = mb_substr($kard,-5,5);
        return '' . $this->nro;
        return 'P' . $number;
    }
    public function scopeSfechas($query, $ini, $fin, $prefijo) {
        if ($ini && $fin) {
            return $query->whereDate($prefijo.'fecha_registro','>=',$ini)
                        ->whereDate($prefijo.'fecha_registro','<=',$fin);
        }
        return $query;
    }
    
    public function scopeVisible($query) {
        return $query->where('visible', true);
    }
    public function detalle() {
        return $this->hasMany(DetalleIngreso::class, 'ingreso_id')->with(['producto','lote']);
    }
    public function proveedor() {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
