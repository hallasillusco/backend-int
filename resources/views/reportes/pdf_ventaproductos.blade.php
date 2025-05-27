@extends('blank')

@section('content')
  <div class="text-center">REPORTE DE VENTA POR PRODUCTO</div>
  @if ($ini && $fin)
    <div>Del {{ date('d/m/Y',strtotime($ini)) }} al {{ date('d/m/Y',strtotime($fin)) }}</div>
  @endif
    <table class="bordes">
      <tr>
        <th>N.</th>
        <th>Código</th>
        <th>Producto</th>
        <th>Unidad</th>
        {{-- <th>Precio</th> --}}
        {{-- <th>Categoría</th> --}}
        <th>Cantidad</th>
        <th>Total</th>
        {{-- <th>Valor</th> --}}
      </tr>
      
      @foreach ($datos as $item)
        <tr>
          <td class="text-center">{{ ++$i }}</td>
          {{-- <td>{{ date('d-m-Y',strtotime($item->fecha)) }}</td>
          <td>{{ date('H:i',strtotime($item->fecha)) }}</td> --}}
          <td class="text-center">{{ $item->codigo }}</td>
          <td>{{ $item->nombre }}</td>
          <td>{{ $item->unidad->sigla }}</td>
          {{-- <td>{{ $item->precio_unit }}</td> --}}
          {{-- <td>{{ $item->categoria->nombre }}</td> --}}
          <td class="text-center">{{ $item->v_cantidad }}</td>
          <td class="text-center">{{ $item->v_total }}</td>
        </tr>
      @endforeach
    </table>
    {{-- <div>{{ $datos->total }}</div> --}}
@endsection

@section('scripts')
  <style>
    table {
      width: 100%;
      font-size: 16px;
    }
  </style>
@endsection