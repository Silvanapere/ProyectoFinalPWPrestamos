<?php

namespace App\Http\Controllers;

use App\Models\TipoPrestamo;
use Illuminate\Http\Request;

class TipoPrestamoController extends Controller
{
    public function index()
        {
            $tipos = TipoPrestamo::orderBy('nombre')->get();
            return view('tipos_prestamo.index', compact('tipos'));
            
        }


        public function store(Request $request)
        {
            $datos = $request->validate([
                'nombre' => 'required|string|max:100|unique:tipos_prestamo,nombre',
                'descripcion' => 'required|string|max:255',
                'estado' => 'required|in:Activo,Inactivo',
            ]);

            TipoPrestamo::create($datos);

            return redirect()
                ->route('tipos-prestamo.index')
                ->with('success', 'Tipo de préstamo registrado correctamente.');
        }

        public function update(Request $request, $id)
            {
                $tipo = TipoPrestamo::findOrFail($id);

                $datos = $request->validate([
                    'nombre' => 'required|string|max:100|unique:tipos_prestamo,nombre,' . $id,
                    'descripcion' => 'required|string|max:255',
                    'estado' => 'required|in:Activo,Inactivo',
                ]);

                $tipo->update($datos);

                return redirect()
                    ->route('tipos-prestamo.index')
                    ->with('success', 'Tipo de préstamo actualizado correctamente.');
            }

}
