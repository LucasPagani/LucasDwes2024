{{-- Usamos la vista app como plantilla --}}
@extends('app')

{{-- Sección aporta el título de la página --}}
@section('title', 'Perfil Administrador')

{{-- Sección para el autoregistro de usuarios --}}
@section ('navbar')
<li class='nav-item'>
    <a class='nav-link' aria-current='page' href='index.php?botonprocloginadmin'>Salir</a>
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
        <div class="">
            <a class="btn btn-secondary m2" aria-current="page" href="admin.php?botonCrearUsuario">Crear Usuario</a>
            <!-- Botón para hashear la contraseña -->
            <a class="btn btn-primary m2" aria-current="page" href="admin.php?botonHashearContraseñas">Hashear Contraseña</a>            
            <!-- Botón para quitar hashear la contraseña -->
            <a class="btn btn-danger m2" aria-current="page" href="admin.php?quitarHashContraseñas">Quitar Hash Contraseña</a>            
            <table class="table">
                <thead>
                    <tr>  
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>                        
                        <th scope="col">Email</th>
                        <th scope="col">Rol</th>
                        <!-- Añadir columnas solo cuando se soliciten los datos de las partidas -->
                        @if (isset($mostrarDatosPartidas) && $mostrarDatosPartidas)
                            <th scope="col">Partidas Ganadas</th>
                            <th scope="col">Partidas Perdidas</th>
                        @endif
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios ?? [] as $item)
                    <tr class=" text-center">   
                        <td>{{$item->getId()}}</td>
                        <td>{{$item->getNombre()}}</td>                        
                        <td>{{$item->getEmail()}}</td>
                        <td>{{$item->getRol()}}</td>
                        <!-- Añadir datos de las partidas solo si se solicitan -->
                        @if (isset($mostrarDatosPartidas) && $mostrarDatosPartidas)
                            <td>{{ $partidasGanadas }}</td>
                            <td>{{ $partidasPerdidas }}</td>
                        @endif
                        <td>
                            <form action="admin.php" method='GET' class="d-inline">
                                <input type="submit" class="btn btn-warning m2" value="Es Admin" name="otorgarRolAdmin" >
                                <input type="hidden" name="id" value="{{$item->getId()}}">
                                <input type="submit" onclick="return confirm('¿Borrar Producto?')" class="btn btn-danger" value="Borrar" name="botonEliminarUsuario">
                                <input type="submit" class="btn btn-warning m2" value="Datos Partidas" name="datosPartidas" >
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
