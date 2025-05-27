<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategoria extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'habilitado', 'categoria_id'];
    
    public function scopeHabilitado($query) {
        return $query->where('habilitado', true);
    }
    
    public function categoria() {
        return $this->belongsTo(Categoria::class,'categoria_id')->with(['tipo']);
    }
}
