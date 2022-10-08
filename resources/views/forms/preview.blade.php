@extends('layouts.base')

@section('title', 'Comprar')

@section('content')

    <div class="container py-3">

        <form method="post" action="{{ route('orders.store') }}">

            <div class="mb-3 ">
                <label for="name" class="form-label">Producto</label>
                <img src="{{ asset('img/product.webp') }}" class="mb-2" style="width: 100px;" alt="" />
                <input type="text" class="form-control" id="name" name="name" value="Biblia" readonly>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Valor</label>
                <input type="text" class="form-control" id="name" name="name" value="$9.999" readonly>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $data['name'] }}" readonly>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $data['email'] }}" readonly>
            </div>

            <div class="mb-3">
                <label for="mobile" class="form-label">Número celular</label>
                <input type="number" class="form-control" id="mobile" name="mobile" value="{{ $data['mobile'] }}" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Pagar</button>
            <a class="btn btn-outline-secondary" href="{{ route('forms.shopping') }}">volver</a>
        </form>
    </div>

@endsection
