@extends('layouts.app')
    @section('content')

    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

    <h1 style="color: white; font-size: 4rem; font-weight: bold;" class="text-center">403 - Acceso denegado</h1>
    <p style="font-size: 1.5rem;" class="text-center">Lo sentimos, no tienes permiso para acceder a esta página.</p>
    <div class="text-center">
        <a href="{{ url('/') }}" class="btn btn-primary">Volver al inicio</a>
    </div>



    @endsection