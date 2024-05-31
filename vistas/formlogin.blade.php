{{-- Usamos la vista app como plantilla --}}
@extends('app')
{{-- Sección aporta el título de la página --}}
@section('title', 'Formulario login')
{{-- Sección aporta el enlace de autoregistro --}}
@section('navbar')

                <div class="m-2">Valoración de la Aplicación</div>
                  <div class="star-rating m-3" id='estrellas'>
                    @if(isset($mediaVotos))
                        @php
                            function mostrarEstrellas($mediaVotos) {
                                $estrellasLlenas = floor($mediaVotos);
                                $mediaEstrella = $mediaVotos - $estrellasLlenas;
                                $estrellasVacias = 5 - ceil($mediaVotos);
                                $html = '';

                                for ($i = 0; $i < $estrellasLlenas; $i++) {
                                    $html .= '<label class="star">&#9733;</label>'; // Estrella llena
                                }

                                if ($mediaEstrella > 0) {
                                    $html .= '<label class="star">&#9733;</label>'; // Media estrella (opcional)
                                }

                                for ($i = 0; $i < $estrellasVacias; $i++) {
                                    $html .= '<label class="star">&#9734;</label>'; // Estrella vacía
                                }

                                return $html;
                            }
                        @endphp
                        {!! mostrarEstrellas($mediaVotos) !!}
                    @else
                        <div class="m-2">No hay valoraciones aún</div>
                    @endif
                </div>


<li class="nav-item">
    <a class="nav-link" aria-current="page" href="index.php?botonregistro">Regístrate</a>
</li>
<li class="nav-item">
    <a class="nav-link" aria-current="page" href="admin.php?botonloginAdmin">Login Administrador</a>
</li>
@endsection
@section('usermenu')
@endsection
{{-- Sección muestra el formulario de login del usuario --}}
@section('content')
<div class="container col-md-8">
    <div class="panel panel-default">
        @if (isset($mensaje)) 
        <div class="alert alert-primary" role="alert">{{ $mensaje }}</div>
        @endif
        @if (isset($error)) 
        <div class="alert alert-danger" role="alert">Error Credenciales</div>
        @endif
        <h2 class="text-center">Login</h2>
        <div class="panel-body mt-3">
            <form class="form-horizontal" method="POST" action="index.php" id='formlogin'>
                <div class="mb-3 row">                            
                    <label for="inputNombre" class="col-sm-2 col-form-label">Nombre</label>
                    <div class="col-sm-10">
                        <input id="inputNombre" type="text" class="form-control col-sm-10" placeholder="Nombre" name="nombre">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control col-sm-10" id="inputPassword" placeholder="Password" name="clave">
                    </div>        
                </div>
                <div class="mb-3">
                    <div class="col-md-8 col-md-offset-4">
                        <input type="submit" class="btn btn-primary" name="botonproclogin" value="Login">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
