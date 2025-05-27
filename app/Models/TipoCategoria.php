<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCategoria extends Model
{
    protected $fillable = ['nombre', 'habilitado', 'menu'];
    use HasFactory;

    public function scopeHabilitado($query) {
        return $query->where('habilitado', true);
    }
    
    public function categorias() {
        return $this->hasMany(Categoria::class, 'tipo_id')->with(['sub_categorias']);
    }
}
