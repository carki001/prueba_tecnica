<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [

        'nombre',
        "email",
        "sexo",
        "area_id",
        "boletin",
        "descripcion",
    ];

    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'empleado_role', 'empleado_id', 'role_id');
    }
}
