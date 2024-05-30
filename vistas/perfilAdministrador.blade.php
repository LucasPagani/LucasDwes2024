{{-- Usamos la vista app como plantilla --}}
@extends('app')

{{-- Sección aporta el título de la página --}}
@section('title', 'Perfil Administrador')

{{-- Sección para el autoregistro de usuarios --}}
@section ('navbar')
<li class='nav-item'>
    <a class='nav-link' aria-current='page' href='juego.php?botonnuevapartida'>Juego</a>
</li>
@endsection

{{-- Sección muestra puntuación de las partidas --}}
@section('content')
<div class="container">
    @if (isset($mensaje)) 
    <div  class="alert alert-primary" role="alert">{{ $mensaje }}</div>
    @endif
    @if (isset($error)) 
    <div class="alert alert-danger" role="alert">Error Credenciales</div>
    @endif
    <h2 class="text-center my-4">Perfil Administrador</h2>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <a class="btn btn-secondary me-2 mb-2" aria-current="page" href="admin.php?botonCrearUsuario">Crear Usuario</a>
                <a class="btn btn-primary me-2 mb-2" aria-current="page" href="admin.php?botonHashearContraseñas">Hashear Contraseña</a>            
                <a class="btn btn-danger mb-2" aria-current="page" href="admin.php?quitarHashContraseñas">Quitar Hash Contraseña</a>            
            </div>
            <table class="table">
                <thead>
                    <tr>  
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>                        
                        <th scope="col">Email</th>
                        <th scope="col">Rol</th>
                        <th scope="col">P.Ganadas</th>
                        <th scope="col">P.Perdidas</th>
                        <th scope="col">P.Jugadas</th>
                        <th scope="col">Puntuación</th>
                        <th scope="col">Voto</th>
                        <th scope="col">Opinión</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios ?? [] as $item)
                    <tr class="text-center">   
                        <td>{{$item->getId()}}</td>
                        <td>{{$item->getNombre()}}</td>                        
                        <td>{{$item->getEmail()}}</td>
                        <td>{{$item->getRol()}}</td>
                        <td>{{$item->getPartidasGanadas()}}</td>
                        <td>{{$item->getPartidasPerdidas()}}</td>
                        <td>{{$item->getPartidasJugadas()}}</td>
                        <td>{{$item->getPuntuacionPartidas()}}</td>
                        <td>{{$item->getVoto()}}</td>
                        <td>{{$item->getOpinion()}}</td>
                        <td>
                            <form action="admin.php" method='GET' class="d-inline">
                                <input type="submit" class="btn btn-warning me-2 mb-2" value="Cambiar Rol" id="btnrol" name="otorgarRolAdmin">
                                <input type="hidden" name="id" value="{{$item->getId()}}">
                                <input type="hidden" name="rol" value="{{$item->getRol()}}">
                                <input type="submit" onclick="return confirm('¿Borrar Usuario?')" class="btn btn-danger me-2 mb-2" value="Borrar" name="botonEliminarUsuario">
                                <input type="submit" id="btnReset" name="btnReset" class="btn btn-primary mb-2" value="Resetear"/>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>    
@endsection
