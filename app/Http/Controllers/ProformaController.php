<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Lote;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Deposito;
use App\Models\Producto;
use App\Models\Proforma;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleProforma;
use App\Models\ProductoSucursal;

class ProformaController extends Controller
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
        $data = Proforma::snumero($numero,'')
        ->sfechas($fecha_inicio,$fecha_fin,'')
        ->susuario($user_id,'')
        ->visible()
        ->orderBy('activo','DESC')
        ->latest()
        ->with(['usuario','cliente'])->get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Proforma::with(['detalle','usuario','cliente','comprobante'])->find($id);
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
            $lote_query = Lote::find($lista['lote_id']);
            // $prod_query = Producto::find($lista['producto_id']);
            $total += $lista['cantidad'] * $lista['precio'];
            if ($lista['cantidad'] <= 0) {
                return response()->json('La cantidad debe ser mayor a 0', 409);
            }
            if ($lista['precio'] <= 0) {
                return response()->json('El precio debe ser mayor a 0', 409);
            }
        }
        // $reg_sucursal = null;
        // if ($verify['rol'] == 1) {
        //     $reg_sucursal = $request->sucursal_id;
        // } else {
        //     if ($verify['rol'] == 2) {
        //         $reg_sucursal = $verify['sucursal_id'];
        //     }
        // }
        $reg_sucursal = true;
        $cliente = Cliente::find($request->cliente_id);
        try {
            DB::beginTransaction();
            $newData = new Proforma;
            $newData->nro = $this->getNro();
            $newData->tipo_pago = $request->tipo_pago;
            $newData->razon_social = $cliente->razon_social;
            $newData->nit = $cliente->nit;
            $newData->fecha_registro = date('Y-m-d H:i:s');
            $newData->fecha_vigencia = $fecha->addDay(30)->format('Y-m-d H:i:s');
            $newData->factura = false;
            $newData->activo = true;
            if ($request->factura == 'true' || $request->factura == '1') {
                $newData->factura = true;
            }
            $newData->cliente_id = $cliente->id;
            $newData->sucursal_id = $reg_sucursal;
            $newData->user_id = Auth::user()->id;
            $newData->visible = true;
            $newData->web = false;
            // $newData->tipo_pago_id = 3;
            $newData->save();
            if ($request->detalle) {
                foreach ($request->detalle as $lista) {
                    $lote = Lote::find($lista['lote_id']);
                    $newItem = new DetalleProforma;
                    $newItem->cantidad = $lista['cantidad'];
                    $newItem->precio = $lista['precio'];
                    $newItem->producto_id = $lote->producto_id;
                    $newItem->lote_id = $lote->id;
                    $newItem->proforma_id = $newData->id;
                    $newItem->save();
                }
            }
            $newData->total = $total;
            $newData->save();
            DB::commit();
            $data = [
                'id' => $newData->id,
                'success' => 'Se ha realizado un nuevo registro.'
            ];
            return response()->json($data, 201);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            return response()->json($th, 409);
        }
    }
    public function update(Request $request, $id) {
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
        $editData = Proforma::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        if (!$editData->activo) {
            return response()->json('La proforma ha pasado a venta anteriormente', 409);
        }
        foreach ($request->detalle as $lista) {
            if ($lista['cantidad'] <= 0) {
                return response()->json('La cantidad debe ser mayor a 0', 409);
            }
            if ($lista['precio'] <= 0) {
                return response()->json('El precio debe ser mayor a 0', 409);
            }
        }
        $fecha = Carbon::now();
        $total = 0;
        foreach ($request->detalle as $lista) {
            $total += $lista['cantidad'] * $lista['precio'];
        }
        $cliente = Cliente::find($request->cliente_id);
        $editData->tipo_pago = $request->tipo_pago;
        $editData->razon_social = $cliente->razon_social;
        $editData->nit = $cliente->nit;
        $editData->factura = false;
        if ($request->factura == 'true' || $request->factura == '1') {
            $editData->factura = true;
        }
        $editData->cliente_id = $request->cliente_id;
        foreach ($editData->detalle as $key => $value) {
            $value->delete();
        }
        // if ($request->detalle) {
            foreach ($request->detalle as $lista) {
                $lote = Lote::find($lista['lote_id']);
                $newItem = new DetalleProforma;
                $newItem->cantidad = $lista['cantidad'];
                $newItem->precio = $lista['precio'];
                $newItem->producto_id = $lote->producto_id;
                $newItem->lote_id = $lote->id;
                $newItem->proforma_id = $editData->id;
                $newItem->save();
                // if ($request->dolar) {
                //     $total += $anterior + $lista['cantidad'] * $lista['precio'] * $config->cambio_moneda;
                // } else {
                //     $total += $anterior + $lista['cantidad'] * $lista['precio'];
                // }
            }
        // }
        $editData->total = $total;
        $editData->save();

        $data = [
            'id' => $editData->id,
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = Proforma::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        // $valid = Venta::where('proforma_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        $data->visible = false;
        $data->save();
        return response()->json($data, 200);
    }
    public function ventaproforma(Request $request,$id) {
        try {
            DB::beginTransaction();

            $proforma = Proforma::find($id);
            if (!$proforma) {
                return response()->json('No se encontro el registro.', 409);
            }
            $validVenta = Venta::where('proforma_id',$id)->first();
            if ($validVenta) {
                return response()->json('Ya se realizo la venta anteriormente.', 409);
            }
            $proforma->activo = false;
            $proforma->save();
            $total = 0;
            foreach ($request->detalle as $lista) {
                $total += $lista['cantidad'] * $lista['precio'];
            }
            foreach ($proforma->detalle as $key => $value) {
                $value->delete();
            }
            foreach ($request->detalle as $lista) {
                $lote = Lote::find($lista['lote_id']);
                $newItem = new DetalleProforma;
                $newItem->cantidad = $lista['cantidad'];
                $newItem->precio = $lista['precio'];
                $newItem->producto_id = $lote->producto_id;
                $newItem->lote_id = $lote->id;
                $newItem->proforma_id = $proforma->id;
                $newItem->save();
                // if ($request->dolar) {
                //     $total += $anterior + $lista['cantidad'] * $lista['precio'] * $config->cambio_moneda;
                // } else {
                //     $total += $anterior + $lista['cantidad'] * $lista['precio'];
                // }
            }
            $this->calcularTotal($proforma->id);
            $proforma = Proforma::find($proforma->id);
            // return response()->json('$data', 200);

            $newData = new Venta;
            $newData->nro = $this->getNroVenta();
            $newData->tipo_pago = $proforma->tipo_pago;
            $newData->razon_social = $proforma->razon_social;
            $newData->nit = $proforma->nit;
            $newData->fecha_registro = date('Y-m-d H:i:s');
            $newData->factura = $proforma->factura;
            $newData->estado = 'VENTA';
            $newData->cancelado = true;
            $newData->total = $proforma->total;
            $newData->proforma_id = $proforma->id;
            $newData->cliente_id = $proforma->cliente_id;
            $newData->sucursal_id = $proforma->sucursal_id;
            // $newData->user_id = $proforma->user_id;
            $newData->user_id = Auth::user()->id;
            $newData->save();

            foreach ($proforma->detalle as $lista) {
                $lote = Lote::find($lista['lote_id']);
                $newItem = new DetalleVenta;
                $newItem->cantidad = $lista->cantidad;
                $newItem->precio = $lista->precio;
                $newItem->producto_id = $lista->producto_id;
                $newItem->lote_id = $lote->id;
                $newItem->venta_id = $newData->id;
                $newItem->save();
                if (!$this->descontarStockSucursal($newItem,$newData->sucursal_id)) {
                    $txtDetail = $newItem->producto->nombre;
                    DB::rollback();
                    return response()->json('Error de stock insuficiente * ' . $txtDetail, 409);
                };
            }
            DB::commit();
            $data = [
                'success' => 'Se ha realizado la venta.'
            ];
            return response()->json($data, 200);
        } catch (\Exception $th) {
            DB::rollback();
            //throw $th;
            return response()->json($th, 409);
        }
    }
    public function ventawebproforma(Request $request) {
        $proforma = Proforma::find($request->proforma_id);
        if (!$proforma) {
            return response()->json('No se encontro el registro.', 409);
        }
        $comprobante = Deposito::find($request->id);
        if (!$comprobante) {
            return response()->json('No se encontro el registro.', 409);
        }
        $validVenta = Venta::where('proforma_id',$request->proforma_id)->first();
        if ($validVenta) {
            return response()->json('Ya se realizo la venta anteriormente.', 409);
        }
        if ($request->monto != $proforma->total) {
            return response()->json('El monto no coincide con el total del pedido', 409);
            return response()->json('El valor debe ser mayor a 0', 409);
        }
        try {
            DB::beginTransaction();

            $comprobante->monto = $request->monto;
            $comprobante->revisado = true;
            $comprobante->save();
            $proforma->activo = false;
            $proforma->save();

            $newData = new Venta;
            $newData->nro = $this->getNroVenta();
            $newData->tipo_pago = $proforma->tipo_pago;
            $newData->razon_social = $proforma->razon_social;
            $newData->nit = $proforma->nit;
            $newData->fecha_registro = date('Y-m-d H:i:s');
            $newData->factura = $proforma->factura;
            $newData->estado = 'VENTA';
            $newData->cancelado = true;
            $newData->total = $proforma->total;
            $newData->proforma_id = $proforma->id;
            $newData->cliente_id = $proforma->cliente_id;
            $newData->sucursal_id = $proforma->sucursal_id;
            // $newData->user_id = $proforma->user_id;
            $newData->user_id = Auth::user()->id;
            $newData->save();

            foreach ($proforma->detalle as $lista) {
                // $lote = Lote::find($lista->lote_id);
                $newItem = new DetalleVenta;
                $newItem->cantidad = $lista->cantidad;
                $newItem->precio = $lista->precio;
                $newItem->producto_id = $lista->producto_id;
                $newItem->lote_id = $lista->lote_id;
                $newItem->venta_id = $newData->id;
                $newItem->save();
                if (!$this->descontarStockSucursal($newItem,$newData->sucursal_id)) {
                    $txtDetail = $newItem->producto->nombre;
                    DB::rollback();
                    return response()->json('Error de stock insuficiente * ' . $txtDetail, 409);
                };
            }
            DB::commit();
            $data = [
                'success' => 'Se ha realizado la venta.'
            ];
            return response()->json($data, 200);
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
                return 'Lote repetido: [' . $psearch->lote . ']';
            }
        }
        return false;
    }

    function getNro() {
        $ultimo_registro = Proforma::latest()->first();
        if (!$ultimo_registro) {
            return 1;
        }
        $nro = $ultimo_registro->nro + 1;
        $flag = false;
        for ($i=0; !$flag; $i++) {
            // previene que no se repita el nro
            $query = Proforma::where('nro',$nro)->first();
            if (!$query) {
                $flag = true;
            } else {
                $nro++;
            }
        }
        return $nro;
    }
    function getNroVenta() {
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
    function calcularTotal($id) {
        $query = Proforma::find($id);
        $total = 0;
        foreach ($query->detalle as $key => $value) {
            $total = $value->cantidad * $value->precio;
        }
        $query->total = $total;
        $query->save();
    }
}
