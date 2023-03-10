<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['empleados'] = Empleado::paginate(2); //el nombre empleados lo puedo acceder desde las vistas
        return view('empleado.index', $data); //entonces la vista index toma esa vista index y le pasa $data
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('empleado.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //--validacion
        $campos = [
            'Nombre'=>'required|string|max:100',
            'Apellido'=>'required|string|max:100',
            'Correo'=>'required|email',
            'Foto'=>'required|max:10000|mimes:jpeg,png,jpg',
        ];
        $mensaje = [
            'required'=>'El :attribute es requerido',
            'Foto.required'=>'La :attribute es requerida'
        ];

        $this->validate($request,$campos,$mensaje);

        //--fin validacion

        //$dataEmployee = request()->all();
        $dataEmployee = request()->except('_token');
        if ($request->hasFile('Foto')) {
            $dataEmployee['Foto'] = $request->file('Foto')->store('/uploads', 'public');


        }
        Empleado::insert($dataEmployee);
        return redirect('empleado')->with('mensaje','¡empleado agregado con exito!');
        return response()->json($dataEmployee);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Empleado $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Empleado $empleado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Empleado $empleado
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $empleado = Empleado::findOrFail($id);
        return view('empleado.edit', compact('empleado'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Empleado $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idONombreCualquiera)
    {
        //--validacion
        $campos = [
            'Nombre'=>'required|string|max:100',
            'Apellido'=>'required|string|max:100',
            'Correo'=>'required|email',
        ];
        $mensaje = [
            'required'=>'El :attribute es requerido',
        ];
        if ($request->hasFile('Foto')) {
            $campos=['Foto'=>'required|max:10000|mimes:jpeg,png,jpg'];
            $mensaje=['Foto.required'=>'la foto es requerida'];

        }

        $this->validate($request,$campos,$mensaje);

        //--fin validacion

        //$dataEmployee = request()->all();
        //return response()->json($dataEmployee);
        $dataEmployee = request()->except('_token', '_method');
        if ($request->hasFile('Foto')) {
            $empleado = Empleado::findOrFail($idONombreCualquiera);
            Storage::delete('public/'.$empleado->Foto);
            $dataEmployee['Foto'] = $request->file('Foto')->store('/uploads', 'public');


        }

        Empleado::where('id','=', $idONombreCualquiera)->update($dataEmployee);
        $empleado = Empleado::findOrFail($idONombreCualquiera);
        //return view('empleado.edit', compact('empleado'));
        return redirect('empleado')->with('mensaje','!se actualizo el empleado correctamente¡');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Empleado $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $empleado = Empleado::findOrFail($id);
        if(Storage::delete('public/'.$empleado->Foto)){
            Empleado::destroy($id);
        }

        Empleado::destroy($id);
        return redirect('empleado')->with('mensaje','!se elimino el empleado correctamente¡');
    }
}
