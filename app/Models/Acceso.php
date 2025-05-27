<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acceso extends Model
{
    use HasFactory;
    
    public function scopeSfechas($query, $ini, $fin, $prefijo) {
        if ($ini && $fin) {
            return $query->whereDate($prefijo.'ingreso','>=',$ini)
                        ->whereDate($prefijo.'ingreso','<=',$fin);
        }
        return $query;
    }
    
    public function scopeSusuario($query, $value, $prefijo) {
        if ($value && $value!='null') {
            return $query->where($prefijo.'user_id', $value);
        }
        return $query;
    }
    
    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }
}
