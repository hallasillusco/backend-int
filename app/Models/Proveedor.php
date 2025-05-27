<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    
    public function scopeSterm($query,$value,$prefijo) {
        $columns = ['razon_social','contacto'];
        if ($value) {
            $words_search = explode(' ', $value);
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
    
    public function scopeHabilitado($query) {
        return $query->where('habilitado', true);
    }
}
