<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'slug',
        'img_url',
        'stock',
        'detalle',
        'descripcion',
        'precio_desc',
        'precio_unit',
        'descuento',
        'habilitado',
        'destacado',
        'tipo_id',
        'categoria_id',
        'unidad_id',
        'marca_id',
        'sub_categoria_id',
    ];
    use HasFactory;
    public function scopeSterm($query, $value, $prefijo) {
        $columns = ['nombre'];
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
    public function scopeScategoria($query, $value, $prefijo) {
        if ($value) {
            return $query->where($prefijo.'categoria_id',$value);
        }
        return $query;
    }
    public function scopeSsubcategoria($query, $value, $prefijo) {
        if ($value) {
            return $query->where($prefijo.'sub_categoria_id',$value);
        }
        return $query;
    }
    public function scopeSmarca($query, $value, $prefijo) {
        if ($value) {
            return $query->where($prefijo.'marca_id',$value);
        }
        return $query;
    }
    public function scopeSdescuento($query, $value, $prefijo) {
        if ($value) {
            return $query->where($prefijo.'descuento','>',0)->orderBy('descuento');
        }
        return $query;
    }
    public function scopeSnuevo($query, $value, $prefijo) {
        if ($value) {
            return $query->where($prefijo.'created_at','>=',Carbon::now()->subDays(3));
        }
        return $query;
    }
    public function scopeHabilitado($query) {
        return $query->where('habilitado', true);
    }
    public function scopeDestacado($query) {
        return $query->where('habilitado', true)->where('destacado', true);
    }
    public function tipo() {
        return $this->belongsTo(TipoCategoria::class, 'tipo_id');
    }
    public function categoria() {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    public function sub_categoria() {
        return $this->belongsTo(SubCategoria::class, 'sub_categoria_id');
    }
    public function unidad() {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }
    public function marca() {
        return $this->belongsTo(Marca::class, 'marca_id');
    }
    public function imagenes() {
        return $this->hasMany(Galeria::class, 'producto_id');
    }
    public function colores() {
        return $this->hasMany(Color::class, 'producto_id');
    }
    public function stock_sucursales() {
        return $this->hasMany(ProductoSucursal::class, 'producto_id');
    }
    public function detalleventas() {
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }
    public function lotes() {
        return $this->hasMany(Lote::class, 'producto_id');
    }
}
