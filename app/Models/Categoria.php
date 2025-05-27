<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'habilitado', 'tipo_id'];
    use HasFactory;
    
    public function scopeSterm($query,$value,$prefijo) {
        $columns = ['nombre'];
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

    public function sub_categorias() {
        return $this->hasMany(SubCategoria::class, 'categoria_id')->habilitado();
    }
    public function tipo() {
        return $this->belongsTo(TipoCategoria::class, 'tipo_id');
    }
}
