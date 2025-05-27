<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'habilitado'];
    
    public function scopeHabilitado($query) {
        return $query->where('habilitado', true);
    }
}
