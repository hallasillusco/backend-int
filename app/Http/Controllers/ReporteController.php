<?php

namespace App\Http\Controllers;

use DB;
use PDF;
use App\Models\Lote;
use App\Models\User;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Ingreso;
use App\Models\Producto;
use App\Models\Proforma;
use App\Models\Categoria;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleIngreso;
use App\Models\ProductoSucursal;
use Luecano\NumeroALetras\NumeroALetras;

class ReporteController extends Controller
{
    public function datos(Request $request) {
        $categorias = Categoria::count();
        $clientes = Cliente::count();
        $pedidos = Venta::count();
        $productos = Producto::count();

        $query = Venta::whereYear('fecha_registro',date('Y'))->whereMonth('fecha_registro',date('m'))->get();
        $ventas = 0;
        foreach ($query as $key => $value) {
            $ventas += $value->total;
        }
        $query = Venta::whereDate('fecha_registro',date('Y-m-d'))->get();
        $d_ventas = 0;
        foreach ($query as $key => $value) {
            $d_ventas += $value->total;
        }
        $data = [
            'ventas' => number_format($ventas,2,',',''),
            'd_ventas' => number_format($d_ventas,2,',',''),
            'pedidos' => Venta::whereYear('fecha_registro',date('Y'))->whereMonth('fecha_registro',date('m'))->count(),
            'd_pedidos' => Venta::whereDate('fecha_registro',date('Y-m-d'))->count(),
            'categorias' => $categorias,
            'clientes' => $clientes,
            'pedidos' => $pedidos,
            'productos' => $productos,
        ];
        return response()->json($data, 200);
    }

    public function proformaPdf(Request $request, $id) {
        $datos = Proforma::visible()->find($id);
        $txt_total = $this->numberToString($datos->total);
        // $txt_total = 'SON 741 bs';
        $data = [
            'i' => 0,
            'title' => 'Nota de compra',
            'datos' => $datos,
            'txt_total' => $txt_total,
            'ini' => $request->get('fecha_inicio'),
            'fin' => $request->get('fecha_fin'),
        ];

        $pdf = PDF::loadView('reportes.pdf_proforma', $data);
        return $pdf->stream('reporte.pdf');
    }
    public function ventaPdf(Request $request, $id) {
        // $config = Configuracion::first();
        $datos = Venta::where('estado','VENTA')->find($id);
        $sub_total = 0;
        // foreach ($datos->detalle as $dt) {
        //     $sub_total += $dt->cantidad * $dt->precio;
        // }
        // $datos->stotal = $sub_total;
        // $txt_total = 'SON 741 bs';
        $txt_total = $this->numberToString($datos->total);
        $data = [
            'i' => 0,
            // 'config' => $config,
            'auto' => $this->generateRandomCode(),
            'codigo' => $this->generateCodigo(),
            'title' => 'Factura electrónica',
            'datos' => $datos,
            'txt_total' => $txt_total,
            // 'ini' => $request->get('fecha_inicio'),
            // 'fin' => $request->get('fecha_fin'),
        ];

        $pdf = PDF::loadView('reportes.pdf_venta', $data);
        return $pdf->stream('reporte.pdf');
    }
    function generateCodigo() {
        // Genera un valor hexadecimal aleatorio de 2 dígitos
        $part1 = dechex(rand(0, 255));
        $part2 = dechex(rand(0, 255));
        $part3 = dechex(rand(0, 255));
        $part4 = dechex(rand(0, 255));
        $part5 = dechex(rand(0, 255));
    
        // Asegurarse de que cada parte tenga 2 caracteres
        $part1 = str_pad(strtoupper($part1), 2, '0', STR_PAD_LEFT);
        $part2 = str_pad(strtoupper($part2), 2, '0', STR_PAD_LEFT);
        $part3 = str_pad(strtoupper($part3), 2, '0', STR_PAD_LEFT);
        $part4 = str_pad(strtoupper($part4), 2, '0', STR_PAD_LEFT);
        $part5 = str_pad(strtoupper($part5), 2, '0', STR_PAD_LEFT);
    
        // Concatenar las partes con guiones
        $randomCode = "$part1-$part2-$part3-$part4-$part5";
        
        return $randomCode;
    }
    function generateRandomCode($length = 65) {
        // Define los caracteres que pueden estar en el código
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
    
        // Genera una cadena aleatoria de la longitud especificada
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
    
        return $randomString;
    }
    public function ingresoPdf(Request $request, $id) {
        $datos = Ingreso::visible()->find($id);
        $txt_total = $this->numberToString($datos->total);
        $data = [
            'i' => 0,
            'title' => 'Nota de ingreso',
            'datos' => $datos,
            'txt_total' => $txt_total,
            'ini' => $request->get('fecha_inicio'),
            'fin' => $request->get('fecha_fin'),
        ];

        $pdf = PDF::loadView('reportes.pdf_ingreso', $data);
        return $pdf->stream('reporte.pdf');
    }
    public function ventaproductos(Request $request) {
        $term = $request->get('term');
        $f_ini = date('Y-m-d',strtotime($request->fecha_inicio));
        $f_fin = date('Y-m-d',strtotime($request->fecha_fin));
        if (!$request->fecha_inicio && !$request->fecha_fin) {
            $f_ini = date('Y-m-d');
            $f_fin = date('Y-m-d');
        }
        $data = Producto::sterm($term,'')
        ->with(['unidad','categoria',
            'detalleventas.venta' => function ($query) use ($f_ini,$f_fin) {
            $query->sfechas($f_ini,$f_fin,'');
        }])
        ->get();
        // return $data;
        foreach ($data as $producto) {
            // $producto->v_cantidad = $producto->detalleventas->sum('cantidad');
            // $producto->v_total = $producto->detalleventas->sum(function ($query) {
            //     return $query->cantidad * $query->precio;
            // });
            $producto->v_cantidad = 0;
            $producto->v_total = 0;
            foreach ($producto->detalleventas as $dt) {
                if ($dt->venta) {
                    $producto->v_cantidad += $dt->cantidad;
                    $producto->v_total += ($dt->cantidad * $dt->precio);
                }
            }
        }
        $data = $data->sortByDesc('v_cantidad')->values();
        return response()->json($data, 200);
    }
    public function ventaproductospdf(Request $request) {
        $term = $request->get('term');
        $f_ini = date('Y-m-d',strtotime($request->fecha_inicio));
        $f_fin = date('Y-m-d',strtotime($request->fecha_fin));
        if (!$request->fecha_inicio && !$request->fecha_fin) {
            // $f_ini = date('Y-m-d');
            // $f_fin = date('Y-m-d');
            $f_ini = null;
            $f_fin = null;
        }
        $datos = Producto::sterm($term,'')
        ->with(['unidad','categoria',
            'detalleventas.venta' => function ($query) use ($f_ini,$f_fin) {
            $query->sfechas($f_ini,$f_fin,'');
        }])
        ->get();
        foreach ($datos as $producto) {
            // $producto->v_cantidad = $producto->detalleventas->sum('cantidad');
            // $producto->v_total = $producto->detalleventas->sum(function ($query) {
            //     return $query->cantidad * $query->precio;
            // });
            $producto->v_cantidad = 0;
            $producto->v_total = 0;
            foreach ($producto->detalleventas as $dt) {
                if ($dt->venta) {
                    $producto->v_cantidad += $dt->cantidad;
                    $producto->v_total += ($dt->cantidad * $dt->precio);
                }
            }
        }
        $datos = $datos->sortByDesc('v_cantidad')->values();
        $data = [
            'i' => 0,
            'title' => 'Reporte de venta por producto',
            'datos' => $datos,
            'ini' => $f_ini,
            'fin' => $f_fin,
        ];

        $pdf = PDF::loadView('reportes.pdf_ventaproductos', $data);
        return $pdf->stream('reporte.pdf');
    }
    
    function numberToString($number) {
        $decimals = 2;
        $currency = 'Bolivianos';
        $sufijo = '';
        $cents = 'Centavos';
        $prefijo = 'SON: ';
        $formatter = new NumeroALetras;
        $formatter->apocope = true;
        $data = $formatter->toInvoice($number, $decimals, $currency);
        return $prefijo . $data . $sufijo;
    }
    public function borrar(Request $request) {
        // control de stock
        $productos = Producto::get();
        foreach ($productos as $prod) {
            if ($prod->stock != $prod->lotes->sum('cantidad_actual')) {
                return response()->json('No cuadra lote '.$prod->stock .' P-ID'.$prod->id, 409);
            }
            if ($prod->stock != $prod->stock_sucursales->sum('cantidad')) {
                return response()->json('No cuadra lote', 409);
            }
        }
        return response()->json('Todo bien', 200);
        $data = Lote::get();
        foreach ($data as $key => $value) {
            $value->delete();
            // $value->cantidad_actual = $value->cantidad;
            // $value->save();
        }
        $data = Proforma::get();
        foreach ($data as $key => $value) {
            $value->delete();
        }
        // $data = Producto::get();
        // foreach ($data as $key => $value) {
        //     $value->delete();
        // }
        $data = Ingreso::get();
        foreach ($data as $key => $value) {
            $value->delete();
        }
        $data = ProductoSucursal::get();
        foreach ($data as $key => $value) {
            $value->delete();
        }
        $data = ProductoSucursal::get();
        foreach ($data as $key => $value) {
            $value->cantidad = 0;
            $value->save();
        }
        $data = Producto::get();
        foreach ($data as $key => $value) {
            $value->stock = 0;
            $value->save();
        }
        foreach ($data as $prod) {
            $ingreso = DetalleIngreso::where('producto_id',$prod->id)->get();
            foreach ($ingreso as $ing) {
                $prod->stock += $ing->cantidad;
                $prod->save();
                $prodsuc = ProductoSucursal::where('producto_id',$prod->id)->get();
                foreach ($prodsuc as $ps) {
                    $ps->cantidad += $ing->cantidad;
                    $ps->save();
                }
            }
        }
        $data = Venta::get();
        foreach ($data as $key => $value) {
            $value->delete();
        }
        $data = [
            'success' => 'Eliminados'
        ];
        return response()->json($data, 200);
    }

    public function masvendidos() {
        // Obtener las medidas del paciente ordenadas por fecha
        $prod = Producto::habilitado()->get();
        foreach ($prod as $key => $value) {
            $cant = 0;
            // $value->cant_vendida = $value->detalleventas->sum('cantidad');
            foreach ($value->detalleventas as $detail) {
                if ($detail->venta->estado == 'VENTA') {
                    $cant += $detail->cantidad;
                }
            }
            $value->cant_vendida = $cant;
        }
        // Filtrar los productos que tienen ventas mayores a 0
        $prod = $prod->filter(function ($product) {
            return $product->cant_vendida > 0;
        });

        // Ordenar los productos por cantidad vendida de mayor a menor y tomar los 10 primeros
        $topProductos = $prod->sortByDesc('cant_vendida')->take(10);

        // Preparar los datos para la gráfica
        $fechas = $topProductos->pluck('nombre')->toArray();
        $value = $topProductos->pluck('cant_vendida')->toArray();

        $data = [
            'labels' => $fechas,
            'data' => $value,
        ];
        return response()->json($data, 200);
    }
    public function agotados() {
        // Obtener las medidas del paciente ordenadas por fecha
        $margenStock = 10;
        $prod = Producto::where('stock','<=',$margenStock)
        ->orderBy('stock','desc')
        ->habilitado()
        ->take(10)->get();

        // Preparar los datos para la gráfica
        $fechas = $prod->pluck('nombre')->toArray();
        $value = $prod->pluck('stock')->toArray();

        $data = [
            'labels' => $fechas,
            'data' => $value,
        ];
        return response()->json($data, 200);
    }
    public function graficaventas() {
        // Obtener las medidas del paciente ordenadas por fecha
        $vendedores = User::where('rol_id',3)->get();
        foreach ($vendedores as $key => $value) {
            $value->cant_vendida = $value->ventas_completadas->sum('total');
        }
        // Filtrar los vendedores que tienen ventas mayores a 0
        // $vendedores = $vendedores->filter(function ($vendedor) {
        //     return $vendedor->cant_vendida > 0;
        // });

        // Ordenar los vendedores por cantidad vendida de mayor a menor y tomar los 10 primeros
        $topVendedores = $vendedores->sortByDesc('cant_vendida')->take(10);

        // Preparar los datos para la gráfica
        $fechas = $topVendedores->pluck('nombre_completo')->toArray();
        $value = $topVendedores->pluck('cant_vendida')->toArray();

        $data = [
            'labels' => $fechas,
            'data' => $value,
        ];
        return response()->json($data, 200);
    }
    public function graficacategorias() {
        // Obtener las medidas del paciente ordenadas por fecha
        $categorias = Categoria::habilitado()->get();
        foreach ($categorias as $key => $value) {
            $query = DetalleVenta::from('detalle_ventas as dt')
            ->join('productos as p','p.id','dt.producto_id')
            ->join('ventas as v','v.id','dt.venta_id')
            ->where('v.estado','VENTA')
            ->where('p.categoria_id',$value->id)
            ->select(DB::raw('SUM(dt.cantidad * dt.precio) as total'))
            ->first();
            $value->cant_vendida = $query->total;
            // cant_vendida = $value->ventas_completadas->sum('total');
        }
        // Filtrar los categorias que tienen ventas mayores a 0
        // $categorias = $categorias->filter(function ($vendedor) {
        //     return $vendedor->cant_vendida > 0;
        // });

        // Ordenar los categorias por cantidad vendida de mayor a menor y tomar los 10 primeros
        $topCat = $categorias->sortByDesc('cant_vendida')->take(10);

        // Preparar los datos para la gráfica
        $fechas = $topCat->pluck('nombre')->toArray();
        $value = $topCat->pluck('cant_vendida')->toArray();

        $data = [
            'labels' => $fechas,
            'data' => $value,
        ];
        return response()->json($data, 200);
    }
}
