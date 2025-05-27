@extends('blank')

@section('content')
  <div class="text-center">REPORTE DE EXISTENCIAS</div>

    <table class="bordes">
      <tr>
        <th>N.</th>
        <th>Código</th>
        <th>Producto</th>
        <th>Unidad</th>
        {{-- <th>Precio</th> --}}
        <th>Categoría</th>
        <th>Stock</th>
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
          <td>{{ $item->categoria->nombre }}</td>
          <td class="text-center">{{ $item->stock }}</td>
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