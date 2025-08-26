<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [

        'nombre',
    ];

    public $timestamps = false;

    public function empleados()
    {
        return $this->belongsToMany(Empleado::class, 'empleado_role', 'role_id', 'empleado_id');
    }
}
