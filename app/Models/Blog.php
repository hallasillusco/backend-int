<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;
    
    public function scopeHabilitado($query) {
        return $query->where('habilitado', true);
    }

    public function tipo() {
        return $this->belongsTo(TipoBlog::class, 'tipo_blog_id');
    }
}
