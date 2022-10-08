@extends('layouts.base')

@section('title', 'Comprar')

@section('content')

    <div class="container py-3">

        <h3 class="w-100 text-center">Orden # {{ $order->id }}</h3>

        <div class="row mt-5">

            <div class="col-6">
                <div class="card ">
                    <div class="card-body">
                        <h5 class="card-title">Cliente</h5>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Nombre: </strong>{{ $order->customer->name }}</li>
                            <li class="list-group-item"><strong>Correo
                                    electrónico: </strong>{{ $order->customer->email }}
                            </li>
                            <li class="list-group-item"><strong>Número celular: </strong>{{ $order->customer->mobile }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card ">
                    <div class="card-body">
                        <h5 class="card-title">Producto</h5>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Nombre: </strong> Biblia</li>
                            <li class="list-group-item"><strong>Valor: </strong>$9.999
                            </li>
                            <li class="list-group-item"><strong>Estado de la orden: </strong>{{ $order->status }}
                            </li>
                            <li class="list-group-item"><strong>Estado del pago: </strong>{{ $order->request->status }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <a class="btn btn-outline-secondary" href="{{ route('home') }}">Inicio</a>

        @if($showPaymentButton)
            <a class="btn btn-outline-warning" href="{{ $order->request->process_url}}">Reintentar pago</a>
        @endif

        @if($showNewPaymentButton)
            <a class="btn btn-outline-primary" href="{{ route('orders.newPayment', $order->id) }}">Reintentar pago</a>
        @endif

    </div>

@endsection
