<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    
    protected $appends = ['txt_nro'];

    public function getTxtNroAttribute() {
        $kard = '00000'.$this->nro;
        $number = mb_substr($kard,-5,5);
        return '' . $number;
    }

    public function scopeSfechas($query, $ini, $fin, $prefijo) {
        if ($ini && $fin) {
            return $query->whereDate($prefijo.'fecha_registro','>=',$ini)
                        ->whereDate($prefijo.'fecha_registro','<=',$fin);
        }
        return $query;
    }
    public function scopeSterm($query, $value, $prefijo) {
        $columns = ['razon_social','nit'];
        $words_search = explode(' ', $value);
        if ($value) {
            return $query->where(function ($query) use ($columns, $prefijo, $words_search) {
                foreach ($words_search as $word) {
                    $query = $query->where(function ($query) use ($columns, $prefijo, $word) {
                        foreach ($columns as $column) {
                            $query->orWhere($prefijo.$column,'LIKE','%'.$word.'%');
                        }
                    });
                }
            });
        }
        return $query;
    }
    public function scopeSnumero($query, $value, $prefijo) {
        if ($value && $value!='null') {
            return $query->where($prefijo.'nro', $value);
        }
        return $query;
    }
    public function scopeSusuario($query, $value, $prefijo) {
        if ($value && $value!='null') {
            return $query->where($prefijo.'user_id', $value);
        }
        return $query;
    }
    
    public function detalle() {
        return $this->hasMany(DetalleVenta::class, 'venta_id')->with(['producto','lote']);
    }
    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
