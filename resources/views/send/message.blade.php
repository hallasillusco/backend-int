@extends('blank')
@section('content')
  <h3>Mensaje de su web favorita</h3>
  {{-- <p>El código es el siguiente:</p> --}}
  <p>Sr(a). {{ $msg->cliente->nombre_completo }}</p>
  <p>Hemos recibido su pedido</p>
  <p>Nro de pedido: {{ $msg->txt_nro }}</p>
  <p>Valor de {{ number_format($msg->total,2) }} bs.</p>
  
@endsection

@section('scripts')
    
@endsection