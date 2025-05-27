<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected function nombres():Attribute {
        return new Attribute(
            set: fn($value) => mb_strtoupper($value)
        );
    }
    protected function apellidos():Attribute {
        return new Attribute(
            set: fn($value) => mb_strtoupper($value)
        );
    }
    protected function nombreCompleto():Attribute {
        return new Attribute(
            set: fn($value) => mb_strtoupper($value)
        );
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function scopeSrol($query,$value,$prefijo) {
        if ($value) {
            return $query->where('rol_id',$value);
        }
        return $query;
    }
    
    public function scopeSterm($query,$value,$prefijo) {
        $columns = ['nombre_completo','celular','email','ci'];
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
    
    // si se usa roles mediante la tabla intermedia
    public function hasRole($role) {
        $flag = false;
        foreach ($this->roles as $key => $value) {
            if ($value->name == $role) {
                $flag = true;
            }
        }
        return $flag;
    }
    
    public function rol() {
        return $this->belongsTo(Role::class);
    }
    
    public function ventas_completadas() {
        return $this->hasMany(Venta::class,'user_id')->where('estado','VENTA');
    }
}
