@extends('layouts.base')

@section('title', 'Comprar')

@section('content')

    <div class="container py-3">

        <div class="text-center">
            <h3>50% de descuento - Solo por $9.999</h3>
            <img src="{{ asset('img/product.webp') }}" style="width: 400px;" />
        </div>

        <form method="post" action="{{ route('forms.shopping') }}">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="name" name="name"required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="mobile" class="form-label">Número celular</label>
                <input type="number" class="form-control" id="mobile" name="mobile" required>
            </div>

            <button type="submit" class="btn btn-primary">Pagar</button>
            <a class="btn btn-outline-secondary" href="{{ route('home') }}">volver</a>
        </form>
    </div>

@endsection
