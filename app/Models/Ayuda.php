<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ayuda extends Model
{
    use HasFactory;
    
    public function scopeHabilitado($query) {
        return $query->where('habilitado', true);
    }
}
