{{-- Usamos la vista app como plantilla --}}
@extends('app')
{{-- Sección aporta el título de la página --}}
@section('title', 'Introduce Jugada')
@section('navbar')
<li class="nav-item">
    <a class="nav-link" aria-current="page" href="juego.php">Volver</a>
</li>
@endsection
{{-- Sección muestra vista de juego para que el usuario elija una letra --}}
@section('content')
<div class="container">
    <h2 class="my-5 text-center">Resumen de partidas jugadas</h2>
    <div class="row">
        <div class="col-6">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Partidas ganadas</th>
                        <th scope="col"># Errores</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1;
                    @endphp
                    @forelse($partidasGanadas as $palabraOculta=> $numErrores)
                    <tr>
                        <th scope="row">{{ $i++ }}</th>
                        <td>{{ $palabraOculta }}</td>
                        <td>{{ $numErrores }}</td>
                    </tr>
                    @empty
                    <tr><td>No hay palabras</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Partidas perdidas</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1;
                    @endphp
                    @forelse($partidasPerdidas as $partidaPerdida)
                    <tr>
                        <th scope="row">{{ $i++ }}</th>
                        <td>{{ $partidaPerdida }}</td>
                    </tr>
                    @empty
                    <tr><td>No hay palabras</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>    
@endsection