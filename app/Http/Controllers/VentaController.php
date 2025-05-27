<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Lote;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\ProductoSucursal;

class VentaController extends Controller
{
    public function index(Request $request) {
        $term = $request->get('term');
        $numero = $request->get('numero');
        $user_id = $request->get('user_id');
        $fecha_inicio = date('Y-m-d',strtotime($request->fecha_inicio));
        $fecha_fin = date('Y-m-d',strtotime($request->fecha_fin));
        if (!$request->fecha_inicio && !$request->fecha_fin) {
            // $fecha_inicio = date('Y-m-d');
            // $fecha_fin = date('Y-m-d');
            $fecha_inicio = null;
            $fecha_fin = null;
        }
        $data = Venta::snumero($numero,'')
        ->sfechas($fecha_inicio,$fecha_fin,'')
        ->susuario($user_id,'')
        ->where('estado','VENTA')
        ->latest()
        ->with(['usuario','cliente'])->get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Venta::with(['detalle','usuario','cliente'])->find($id);
        // foreach ($data->detalle as $key => $value) {
        //     $value->total = $value->cantidad * $value->precio;
        // }
        // foreach ($data->detalle as $key => $value) {
        //     $query = Producto::find($value->producto_id);
        //     $st = $query->disponible->sum('cantidad');
        //     $value->producto->stock = $st;
        // }
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        // $verify = $this->verifySeller();
        // // return $verify;
        // if (!$verify['status']) {
        //     return response()->json($verify['msg'], 409);
        // } else {
        //     // return 'ok';
        // }
        $validProductR = $this->validProductRepeat($request->detalle);
        if ($validProductR) {
            return response()->json($validProductR, 409);
        }
        $fecha = Carbon::now();
        $total = 0;

        foreach ($request->detalle as $lista) {
            $lote = Lote::find($lista['lote_id']);
            if (!$lote) {
                return response()->json('No se encontro el lote', 409);
            }
            if ($lote->cantidad_actual < $lista['cantidad']) {
                return response()->json('No hay stock suficiente ' . $lote->lote . ' ' . $lote->producto->nombre, 409);
            }
            $total += $lista['cantidad'] * $lista['precio'];
            if ($lista['cantidad'] <= 0) {
                return response()->json('La cantidad debe ser mayor a 0', 409);
            }
            if ($lista['precio'] <= 0) {
                return response()->json('El precio debe ser mayor a 0', 409);
            }
        }
        $reg_sucursal = 1;
        // if ($verify['rol'] == 1) {
        //     $reg_sucursal = $request->sucursal_id;
        // } else {
        //     if ($verify['rol'] == 2) {
        //         $reg_sucursal = $verify['sucursal_id'];
        //     }
        // }
        $cliente = Cliente::find($request->cliente_id);
        try {
            DB::beginTransaction();
            $newData = new Venta;
            $newData->nro = $this->getNro();
            $newData->tipo_pago = $request->tipo_pago;
            $newData->razon_social = $cliente->razon_social;
            $newData->nit = $cliente->nit;
            $newData->fecha_registro = date('Y-m-d H:i:s');
            // $newData->fecha_vigencia = $fecha->addDay(10)->format('Y-m-d H:i:s');
            $newData->factura = false;
            // if ($request->factura == 'true' || $request->factura == '1') {
            //     $newData->factura = true;
            // }
            $newData->cancelado = true;
            $newData->cliente_id = $request->cliente_id;
            $newData->sucursal_id = $reg_sucursal;
            $newData->user_id = Auth::user()->id;
            $newData->estado = 'VENTA';
            $newData->save();
            // if ($request->detalle) {
                foreach ($request->detalle as $lista) {
                    $lote = Lote::find($lista['lote_id']);
                    $newItem = new DetalleVenta;
                    $newItem->cantidad = $lista['cantidad'];
                    $newItem->precio = $lista['precio'];
                    $newItem->producto_id = $lote->producto_id;
                    $newItem->lote_id = $lote->id;
                    $newItem->venta_id = $newData->id;
                    $newItem->save();
                    // if ($request->dolar) {
                    //     $total += $lista['cantidad'] * $lista['precio'] * $config->cambio_moneda;
                    // } else {
                    //     $total += $lista['cantidad'] * $lista['precio'];
                    // }
                    
                    if (!$this->descontarStockSucursal($newItem,$reg_sucursal)) {
                        // $txtDetail = $newItem->producto->nombre;
                        $txtDetail = 'algo';
                        DB::rollback();
                        return response()->json('Error de stock insuficiente * ' . $txtDetail, 409);
                    };
                }
            // }
            $this->calcularTotal($newData->id);
            DB::commit();
            $data = [
                'success' => 'Se ha realizado un nuevo registro.'
            ];
            return response()->json($data, 201);
        } catch (\Exception $th) {
            DB::rollback();
            //throw $th;
            return response()->json($th, 409);
        }
    }
    function verifySeller() {
        $user = Auth::user();
        $flag = false;
        $rol = 0;
        $sucursal_id = null;
        $text = 'No cuenta con los permisos';
        foreach ($user->roles as $rol) {
            // rol vendedor
            if ($rol->id == 1) {
                $text = 'Todo bien.';
                $flag = true;
                $rol = 1;
            } else {
                if ($rol->id == 4) {
                    $text = 'Todo bien.';
                    $flag = true;
                    $rol = 2;
                    // if ($flag) {
                        if (!$user->sucursal_id) {
                            $text = 'Debe tener asignado una sucursal.';
                            $flag = false;
                        } else {
                            $sucursal_id = $user->sucursal_id;
                        }
                    // }
                }
            }
        }
        $info = [
            'msg' => $text,
            'rol' => $rol,
            'sucursal_id' => $sucursal_id,
            'status' => $flag
        ];
        return $info;
    }
    function validProductRepeat($productos) {
        $lote_id = null;
        foreach ($productos as $value) {
            $count = 0;
            $lote_id = $value['lote_id'];
            foreach ($productos as $prod) {
                if ($lote_id == $prod['lote_id']) {
                    $count++;
                }
            }
            if ($count > 1) {
                $psearch = Lote::find($value['lote_id']);
                return 'Producto repetido: [' . $psearch->producto->nombre . ']';
            }
        }
        return false;
    }
    function descontarStockSucursal($detail,$sucursal_id) {
        // recibe el detalle de venta
        // DESCUENTO STOCK EN SUCURSAL
        $stock_suc = ProductoSucursal::where('sucursal_id',$sucursal_id)
        ->where('producto_id',$detail->producto_id)->first();
        if (!$stock_suc) {
            return false;
        }
        if ($stock_suc->cantidad < $detail->cantidad) {
            return false;
        }
        $stock_suc->cantidad -= $detail->cantidad;
        if ($stock_suc->cantidad == 0) {
            $stock_suc->disponible = false;
        }
        $stock_suc->save();

        // DESCUENTO STOCK EN LOTES ALMACEN
        $lote = Lote::find($detail->lote_id);
        if (!$lote) {
            return false;
        }
        if ($lote->cantidad_actual < $detail->cantidad) {
            return false;
        }
        $lote->cantidad_actual -= $detail->cantidad;
        $lote->producto->stock -= $detail->cantidad;
        $lote->producto->save();
        if ($lote->cantidad_actual == 0) {
            $lote->disponible = false;
        }
        $lote->save();
        return true;
    }
    public function update(Request $request, $id) {
    }
    public function destroy($id) {
        $data = Venta::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        try {
            DB::beginTransaction();

            foreach ($data->detalle as $dtventa) {
                // DEVOLVER STOCK EN SUCURSAL
                $stock_suc = ProductoSucursal::where('sucursal_id',$data->sucursal_id)
                ->where('producto_id',$dtventa->producto_id)->first();
                if ($stock_suc) {
                    $stock_suc->cantidad += $dtventa->cantidad;
                    $stock_suc->disponible = true;
                    $stock_suc->save();
                }
                // DEVOLVER STOCK EN LOTES ALMACEN
                $lote = Lote::find($dtventa->lote_id);
                if ($lote) {
                    $lote->cantidad_actual += $dtventa->cantidad;
                    $lote->disponible = true;
                    $lote->save();
                    $lote->producto->stock += $dtventa->cantidad;
                    $lote->producto->save();
                }
            }
            $data->estado = 'ANULADO';
            $data->save();
            DB::commit();
            $data = [
                'success' => 'Eliminado'
            ];
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            return response()->json($th, 409);
        }
    }
    function getNro() {
        $ultimo_registro = Venta::latest()->first();
        if (!$ultimo_registro) {
            return 1;
        }
        $nro = $ultimo_registro->nro + 1;
        $flag = false;
        for ($i=0; !$flag; $i++) {
            // previene que no se repita el nro
            $query = Venta::where('nro',$nro)->first();
            if (!$query) {
                $flag = true;
            } else {
                $nro++;
            }
        }
        return $nro;
    }
    function calcularTotal($id) {
        $query = Venta::find($id);
        $total = 0;
        foreach ($query->detalle as $key => $value) {
            $total = $value->cantidad * $value->precio;
        }
        $query->total = $total;
        $query->save();
    }
}
