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
{{-- Sección muestra el formulario de una partida personalizada --}}
@section('content')
<div class="container col-md-6">
    <div>
        <h2 class="text-center m-4">Partida Personalizada</h2>
        <div>
            <form method="POST" action="{{ $_SERVER['PHP_SELF'] }}" id='formpartidapersonalizada' novalidate>
                <div class="input-group mb-3">                            
                    <label for="minlongitud" class="col-sm-2 col-form-label">Longitud Mínima</label>
                    <div class="col-sm-10">
                        <input id="minlongitud" type="number"
                               class="form-control col-sm-10 {{ isset($minLongitudError) ? ($minLongitudError ? "is-invalid" : "is-valid") : "" }}" name="minlongitud" value="{{ $minLongitud ?? "" }}">
                        <div class="col-sm-10 invalid-feedback">
                            El valor de la longitud mínima debe estar entre 3 y 14
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">                            
                    <label for="maxlongitud" class="col-sm-2 col-form-label">Longitud Máxima</label>
                    <div class="col-sm-10">
                        <input id="maxlongitud" type="number"
                               class="form-control col-sm-10 {{ isset($maxLongitudError) ? ($maxLongitudError ? "is-invalid" : "is-valid") : "" }}" name="maxlongitud" value="{{ $maxLongitud ?? "" }}">
                        <div class="col-sm-10 invalid-feedback">
                            El valor de la longitud máxima debe estar entre 5 y 20
                        </div>
                    </div>
                </div>
                @if (isset ($maxminError) && $maxminError)
                <div class="col-sm-10 text-danger me-3">
                    El valor de longitud mínima es mayor que el valor de longitud máxima
                </div>
                @endif
                <div class="input-group mb-3">
                    <label for="contiene" class="col-sm-2 col-form-label">Contiene las letras</label>
                    <div class="col-sm-10">
                        <input id="contiene" type="text"
                               class="form-control col-sm-10 {{ isset($contieneError) ? ($contieneError ? "is-invalid" : "is-valid") : "" }}" name="contiene" value="{{ $contiene ?? "" }}">
                        <div class="col-sm-10 invalid-feedback">
                            El valor de letras contenidas en la palabra debe tener 3 letras como máximo
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <div class="col-md-8 col-md-offset-4">
                        <input type="submit" class="btn btn-primary" name="botonpartidapersonalizada" value="Enviar">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
