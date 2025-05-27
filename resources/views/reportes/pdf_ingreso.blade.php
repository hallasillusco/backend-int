@extends('cr_blank')

@section('content')
  <table class="list-table">
    <tr>
      {{-- style="width: 15%;" rowspan="4" --}}
      <td style="width: 60%;" >
        {{-- <img src="{{ $config->logo_url }}" alt="" height="80"> --}}
        {{-- <img src="images/config/logo.jpg" alt="" height="80"> --}}
      </td>
      <td style="width: 40%;">
        {{-- <div class="razon-social">{{ $config->razon_social }}</div> --}}
      </td>
      {{-- <td style="width: 30%;"><div class="lbl-h">NIT:</div></td> --}}
      {{-- <td style="width: 20%;"><div class="text-h">{{ $config->nit }}</div></td> --}}
    </tr>
    <tr>
      {{-- <td><div class="text-h">CASA MATRIZ</div></td> --}}
      <td><div class="lbl-h">N°:</div></td>
      <td><div class="text-h">{{ $datos->txt_nro }}</div></td>
    </tr>
    <tr>
      {{-- <td><div class="text-h">{{ $config->punto_venta }}</div></td> --}}
      {{-- <td rowspan="3"><div class="lbl-h">CUF:</div></td> --}}
      <td rowspan="3">
        {{-- <div class="text-h width-small">{{ $config->cuf }}</div> --}}
        {{-- @foreach ($cuf as $item)
          <div class="text-h">{{ $item }}</div>
        @endforeach --}}
      </td>
    </tr>
    <tr>
      {{-- <td><div class="text-h">{{ $config->direccion }}</div></td> --}}
      <td><div class="text-h">Direccion</div></td>
    </tr>
    <tr>
      {{-- <td colspan="2"></td> --}}
      {{-- <td><div class="text-h">{{ $config->telefono }}</div></td> --}}
      <td><div class="text-h">Nro. Telf</div></td>
    </tr>
    <tr>
      {{-- <td style="align-items: baseline"><div class="text-h">{{ $config->ciudad }}</div></td> --}}
      {{-- <td><div class="lbl-h">ACTIVIDAD:</div></td>
      <td><div class="text-h width-smalls">{{ $config->actividad }}</div></td> --}}
    </tr>
  </table>
  <div class="title-factura text-center">{{ $title }}</div>
    {{-- <table>
      <tr>
        <td>
          <div>Fecha Ingreso: {{ date('d/m/Y',strtotime($datos->fecha_ingreso)) }}</div>
          <div>Proveedor: {{ $datos->proveedor->razon_social }}</div>
          <div>NIT: {{ $datos->proveedor->nit }}</div>
          <div>Teléfono: {{ $datos->proveedor->telefono }}</div>
          <div>Contacto: {{ $datos->proveedor->contacto }}</div>
          <div>Cel: {{ $datos->proveedor->celular }}</div>
          <div>Usuario: {{ $datos->usuario->username }}</div>
        </td>
      </tr>
    </table> --}}

    <table class="list-table">
      <tr>
        <td style="width: 25%;"><div class="lbl-info">Fecha:</div></td>
        <td style="width: 40%;"><div class="text-h">{{ date('d/m/Y',strtotime($datos->fecha_ingreso)) }}</div></td>
        <td style="width: 20%;"><div class="lbl-info text-rights">NIT/CI:</div></td>
        <td style="width: 15%;"><div class="text-h">{{ $datos->proveedor->nit }}</div></td>
      </tr>
      <tr>
        <td><div class="lbl-info">Nombre/Razón social:</div></td>
        <td><div class="text-h">{{ $datos->proveedor->razon_social }}</div></td>
        {{-- <td>Dirección:</td> --}}
        {{-- <td>{{ $datos->cliente->direccion }}</td> --}}
        <td><div class="lbl-info text-rights">Telf/Cel.:</div></td>
        <td><div class="text-h">{{ $datos->proveedor->telefono }}</div></td>
      </tr>
    </table>
    <div class="separe"></div>

    {{-- <table class="bordes">
      <tr>
        <th>N.</th>
        <th>Producto</th>
        <th>Lote</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Total</th>
      </tr>
      
      @foreach ($datos->detalle as $item)
        <tr class="text-center">
          <td>{{ ++$i }}</td>
          <td class="text-left">{{ $item->producto->nombre }}</td>
          <td>{{ $item->lote->lote }}</td>
          <td>{{ $item->cantidad }}</td>
          <td>{{ number_format($item->precio_compra,2,',','') }}</td>
          <td>{{ number_format($item->precio_compra * $item->cantidad,2,',','') }}</td>
        </tr>
      @endforeach
    </table> --}}
    <table class="bordes-p list-table product-table">
      <tr class="text-center" style="background-color: #003566; color: white; font-size: 13px;">
        <th style="width: 6%;">N.</th>
        <th style="width: 10%;">CÓDIGO PRODUCTO</th>
        <th style="width: 42%;">DESCRIPCIÓN</th>
        <th style="width: 15%;">CANTIDAD</th>
        <th style="width: 12%;">PRECIO UNITARIO</th>
        <th style="width: 15%;">SUB TOTAL</th>
      </tr>
      @foreach ($datos->detalle as $item)
        <tr>
          <td class="text-center">{{ ++$i }}</td>
          <td class="text-center">{{ $item->codigo }}</td>
          <td>{{ $item->producto->nombre }}</td>
          {{-- <td class="text-center">{{ date('d-m-Y H:i',strtotime($item->fecha)) }}</td> --}}
          <td class="text-center">{{ $item->cantidad }}</td>
          <td class="text-center">{{ number_format($item->precio_compra,2) }}</td>
          <td class="text-right">{{ number_format($item->cantidad * $item->precio_compra,2) }}</td>
        </tr>
      @endforeach
      <tr>
        <td class="lbl-h" style="border-bottom: 1px white;" colspan="3"></td>
        <td class="lbl-h" colspan="2">TOTAL Bs.</td>
        <td class="text-right">{{ number_format($datos->total,2) }}</td>
      </tr>
    </table>
    <div class="separe"></div>
    <div class="text-h">{{ $txt_total }}</div>
    <div class="separe"></div>
    <div class="separe"></div>
    <div class="separe"></div>
    <div class="separe"></div>
    {{-- <div class="text-center text-footer">{{ $config->footer_l1 }}</div> --}}
    {{-- <div class="text-center text-footer">{{ $config->footer_l2 }}</div> --}}
    {{-- <div class="text-center text-footer">{{ $config->footer_l3 }}</div> --}}
@endsection

@section('scripts')
  <style>
    .title {
      text-transform: uppercase;
      font-weight: bold;
    }
    .title-factura {
      color: #011e38;
      font-weight: bold;
      font-size: 20px;
    }
    .sub-title {
      color: #003566;
      font-weight: bold;
    }
    .list-table {
      width: 100%;
      /* font-size: 12px; */
    }
    .product-table {
      border-collapse: collapse;
      font-size: 12px;
    }
    .bordes-p th, .bordes-p td {
      border-bottom: 1px solid rgb(214, 158, 2);
      padding-left: 2px;
      padding-right: 2px;
      padding: 2px;
    }
    .width-small {
      width: 120px;
      /* overflow: auto; */
    }
    .razon-social {
      font-size: 13px;
      text-transform: uppercase;
      color: #011e38;
      font-weight: bold;
    }
    .lbl-h {
      width: 100%;
      font-size: 12px;
      text-align: right;
      color: #011e38;
      font-weight: bold;
    }
    .lbl-info {
      width: 100%;
      font-size: 12px;
      /* text-align: right; */
      color: #011e38;
    }
    .text-h {
      font-size: 12px;
    }
    .text-footer {
      font-size: 10px;
    }
    .separe {
      height: 10px;
    }
  </style>
@endsection