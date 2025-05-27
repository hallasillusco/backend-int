<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte</title>

    <style>
      /** 
        Establezca los márgenes de la página en 0, por lo que el pie de página y el encabezado
        puede ser de altura y anchura completas.
      **/
      @page {
        margin: 0cm 0cm;
      }

      /** Defina ahora los márgenes reales de cada página en el PDF **/
      body {
        margin-top: 3cm;
        margin-left: 1cm;
        margin-right: 1cm;
        margin-bottom: 2cm;
      }

      /** Definir las reglas del encabezado **/
      header {
        position: fixed;
        top: 1cm;
        left: 1cm;
        right: 1cm;
        height: 2cm;

        /** Estilos extra personales **/
        /* background-color: #03a9f4; */
        background-color: #ffffff;
        /* color: white; */
        text-align: center;
        /* line-height: 1.5cm; */
      }

      /** Definir las reglas del pie de página **/
      footer {
        position: fixed; 
        bottom: 0cm; 
        left: 0cm; 
        right: 0cm;
        height: 2cm;

        /** Estilos extra personales **/
        /* background-color: #03a9f4; */
        /* color: white; */
        text-align: center;
        line-height: 1.5cm;
      }
    </style>
  </head>

  <body>
    @yield('content')

    @yield('scripts')
    
    <style>
      body {
        font-family: Arial, Helvetica, sans-serif;
      }
      table {
        border-collapse: collapse;
      }
      .text-center {
        text-align: center;
      }
      .text-right {
        text-align: right;
      }
      .text-left {
        text-align: left;
      }
      .bordes th, .bordes td {
        border: 1px solid black;
        padding-left: 2px;
        padding-right: 2px;
        padding: 2px;
      }
      .negrita {
        font-weight: bold;
      }
    </style>
  </body>

</html>