{{-- Usamos la vista app como plantilla --}}
@extends('app')
{{-- Sección aporta el título de la página --}}
@section('title', 'Formulario login')
{{-- Sección sobreescribe el barra de navegación de la plantilla app --}}
@section('navbar')
<li class="nav-item">
    <a class="nav-link" aria-current="page" href="juego.php">Volver</a>
</li>
@endsection
{{-- Sección muestra el formulario para dejar una opinion --}}
@section('content')
<div class="container col-md-6 mt-5">
    
    <div class="card">
        <div class="card-body">
            <h2 class="text-center m-4">Déjanos tu opinión</h2>
            <form method="POST" action="{{ $_SERVER['PHP_SELF'] }}" id="opinionjuego" novalidate>
                <div class="mb-3">
                    <textarea name="campoopinion" rows="10" class="form-control" placeholder="Escribe tu opinión aquí"></textarea>
                </div>
                <div class="d-grid">
                    <button type="submit" name="botonopinion" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
