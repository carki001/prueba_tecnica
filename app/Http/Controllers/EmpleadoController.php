<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Area;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::get();
        $formattedEmpleados = [];

        foreach ($empleados as $empleado) {
            $formattedEmpleados[] = $this->formatEmpleado($empleado);
        }

        return $empleados;
    }

    private function formatEmpleado($empleado)
    {
        $areaName = Area::where('id', $empleado->area_id)->first()?->nombre;
        $empleado->area_name = $areaName;
        $empleado->boletin = $empleado->boletin === 1 ? true : false;

        $roleIds = $empleado->roles()->pluck('roles.id');
        $empleado->roles = $roleIds;

        return $empleado;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'sexo' => ['required', 'string', 'max:1', 'in:M,F'],
            'area_id' => ['required', 'integer', 'min:1'],
            'descripcion' => ['required', 'string', 'max:10000'],
            'boletin' => ['required', 'boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return ['isError' => TRUE, 'message' => 'general.validation_failed'];
        } else {
            $empleadoWithEmailCount = Empleado::where('email', strtolower($request->email))->count();

            if ($empleadoWithEmailCount > 0) {
                return ['isError' => TRUE, 'message' => 'general.email_already_in_use'];
            }

            $empleado = new Empleado();
            $empleado->nombre = $request->nombre;
            $empleado->email = strtolower($request->email);
            $empleado->sexo = $request->sexo;
            $empleado->area_id = $request->area_id;
            $empleado->descripcion = $request->descripcion;
            $empleado->boletin = $request->boletin ? 1 : 0;

            $empleado->save();

            $empleado->roles()->sync($request->roles);

            return $this->formatEmpleado($empleado);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'sexo' => ['required', 'string', 'max:1', 'in:M,F'],
            'area_id' => ['required', 'integer', 'min:1'],
            'descripcion' => ['required', 'string', 'max:10000'],
            'boletin' => ['required', 'boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return ['isError' => TRUE, 'message' => 'general.validation_failed'];
        } else {
            $empleado = Empleado::findOrFail($id);

            $empleadoWithEmailCount = Empleado::where('id', '!=', $id)->where('email', strtolower($request->email))->count();

            if ($empleadoWithEmailCount > 0) {
                return ['isError' => TRUE, 'message' => 'general.email_already_in_use'];
            }

            $empleado->nombre = $request->nombre;
            $empleado->email = strtolower($request->email);
            $empleado->sexo = $request->sexo;
            $empleado->area_id = $request->area_id;
            $empleado->descripcion = $request->descripcion;
            $empleado->boletin = $request->boletin ? 1 : 0;

            $empleado->save();

            $empleado->roles()->sync($request->roles);

            return $this->formatEmpleado($empleado);
        }
    }

    public function delete(Request $request, $id)
    {
        $user = Empleado::findOrFail($id);
        $user->delete();

        return 204;
    }
}
