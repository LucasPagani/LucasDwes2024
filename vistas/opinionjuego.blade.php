{{-- Usamos la vista app como plantilla --}}
@extends('app')
{{-- Sección aporta el título de la página --}}
@section('title', 'Formulario login')
{{-- Sección sobreescribe la barra de navegación de la plantilla app --}}
@section('navbar')
<li class="nav-item">
    <a class="nav-link" aria-current="page" href="juego.php">Volver</a>
</li>
@endsection
{{-- Sección muestra el formulario para dejar una opinión --}}
@section('content')
<div class="container col-md-6 mt-5">

    <div class="card">
        <div class="card-body">
            <h2 class="text-center m-4">Déjanos tu opinión</h2>
            <form method="POST" action="{{ $_SERVER['PHP_SELF'] }}" id="opinionjuego" novalidate>
                
                <div class="star-rating" id='estrellas'>
                    <input type="radio" name="rating" id="1-star" value="1"><label for="1-star" class="star">&#9733;</label>
                    <input type="radio" name="rating" id="2-stars" value="2"><label for="2-stars" class="star">&#9733;</label>
                    <input type="radio" name="rating" id="3-stars" value="3"><label for="3-stars" class="star">&#9733;</label>
                    <input type="radio" name="rating" id="4-stars" value="4"><label for="4-stars" class="star">&#9733;</label>
                    <input type="radio" name="rating" id="5-stars" value="5"><label for="5-stars" class="star">&#9733;</label>
                </div><br>
                
                <div class="mb-3">
                    <textarea name="campoopinion" rows="5" class="form-control" placeholder="Escribe tu opinión aquí"></textarea>
                </div>
                
                <div class="d-grid">
                    <button type="submit" name="botonopinion" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container p-4">
    <form method="POST" action="{{ $_SERVER['PHP_SELF'] }}" id="ratingForm">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <button id="verVotos" type="button">Ver</button>
                <p id="votosTotales"></p>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // No es necesario el envío automático del formulario con las estrellas,
        // ya que ahora todo está en un único formulario enviado con el botón "Enviar".
    });
</script>

@endsection
