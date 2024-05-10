{{-- Usamos la vista app como plantilla --}}
@extends('app')

{{-- Sección aporta el título de la página --}}
@section('title', 'Perfil Administrador')

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
            <li class="nav-item">
                <a class="btn btn-secondary m2" aria-current="page" href="index.php?botonregistro">Crear Usuario</a>
            </li>
            <table class="table">
                <thead>
                    <tr>  
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Clave</th>
                        <th scope="col">email</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios ?? [] as $item)
                    <tr class=" text-center">   
                        <td>{{$item->getId()}}</td>
                        <td>{{$item->getNombre()}}</td>
                        <td>{{$item->getClave()}}</td>
                        <td>{{$item->getEmail()}}</td>
                        <td>{{$item->getRol()}}  </td>
                        <td>
                            <form action="" method='POST' class="d-inline">
                                <input type="submit" class="btn btn-warning m2" value="Actualizar" name="" formaction="modificar.php">
                                <input type="hidden" name="id" value="{{$item->getId()}}"> <!-- mandamos el código del producto a borrar -->
                                <input type="submit" onclick="return confirm('¿Borrar Producto?')" class="btn btn-danger" value="Borrar" name="borrar">
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
