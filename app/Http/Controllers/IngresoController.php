<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Lote;
use App\Models\Ingreso;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleIngreso;
use App\Models\ProductoSucursal;

class IngresoController extends Controller
{
    public function index(Request $request) {
        $fecha_inicio = date('Y-m-d',strtotime($request->fecha_inicio));
        $fecha_fin = date('Y-m-d',strtotime($request->fecha_fin));
        if (!$request->fecha_inicio && !$request->fecha_fin) {
            // $fecha_inicio = date('Y-m-d');
            // $fecha_fin = date('Y-m-d');
            $fecha_inicio = null;
            $fecha_fin = null;
        }
        $data = Ingreso::latest()
        ->sfechas($fecha_inicio,$fecha_fin,'')
        ->visible()
        ->with(['proveedor','usuario'])->get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Ingreso::with(['detalle','proveedor','usuario'])->find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        foreach ($request->detalle as $lista) {
            if ($lista['cantidad'] <= 0) {
                return response()->json('La cantidad debe ser mayor a 0', 409);
            }
            if ($lista['precio_compra'] <= 0) {
                return response()->json('El precio debe ser mayor a 0', 409);
            }
        }
        try {
            DB::beginTransaction();
            $newData = new Ingreso;
            $newData->nro = $request->nro;
            // $newData->nro = $this->getNro();
            $newData->fecha_ingreso = $request->fecha_ingreso;
            $newData->fecha_registro = date('Y-m-d H:i:s');
            $newData->proveedor_id = $request->proveedor_id;
            $newData->user_id = Auth::user()->id;
            $newData->visible = true;
            $newData->save();
            if ($request->detalle) {
                foreach ($request->detalle as $lista) {
                    $lote_id = $this->nuevoLote($lista['lote'],$lista['cantidad'],$lista['precio_compra'],$lista['producto_id'],$request->proveedor_id);
                    $newItem = new DetalleIngreso;
                    $newItem->cantidad = $lista['cantidad'];
                    $newItem->precio_compra = $lista['precio_compra'];
                    $newItem->producto_id = $lista['producto_id'];
                    $newItem->lote_id = $lote_id;
                    $newItem->ingreso_id = $newData->id;
                    $newItem->save();
                    $this->addToStock($lista['cantidad'],$lista['producto_id'],1);
                }
            }
            $newData->save();
            $this->calcularTotal($newData->id);
            DB::commit();

            $data = [
                'success' => 'Se ha realizado un nuevo registro.'
            ];
            return response()->json($data, 201);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            return response()->json($th, 409);
        }
    }
    function calcularTotal($id) {
        $query = Ingreso::find($id);
        $total = 0;
        if ($query) {
            foreach ($query->detalle as $detail) {
                $total += $detail->cantidad * $detail->precio_compra;
            }
            $query->total = $total;
            $query->save();
        }
    }
    function addToStock($cantidad, $producto_id, $sucursal_id) {
        $stock = Producto::find($producto_id);
        $stock->stock += $cantidad;
        $stock->save();
        $search = ProductoSucursal::where('sucursal_id',$sucursal_id)
        ->where('producto_id',$producto_id)->first();
        if ($search) {
            $search->cantidad += $cantidad;
            $search->disponible = true;
            $search->save();
        } else {
            $newItem = new ProductoSucursal;
            $newItem->cantidad = $cantidad;
            $newItem->disponible = true;
            $newItem->sucursal_id = $sucursal_id;
            $newItem->producto_id = $producto_id;
            $newItem->save();
        }
    }
    public function update(Request $request, $id) {
        $editData = Ingreso::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        foreach ($request->detalle as $lista) {
            if ($lista['cantidad'] <= 0) {
                return response()->json('La cantidad debe ser mayor a 0', 409);
            }
            if ($lista['precio_compra'] <= 0) {
                return response()->json('El precio debe ser mayor a 0', 409);
            }
        }
        try {
            DB::beginTransaction();

            $editData->nro = $request->nro;
            $editData->fecha_ingreso = $request->fecha_ingreso;
            $editData->proveedor_id = $request->proveedor_id;
            if ($request->detalle) {
                foreach ($request->detalle as $lista) {
                    $editItem = DetalleIngreso::find($lista['id']);
                    if ($editItem) {
                        if ($this->verificarStockDetalle($editItem->id)) {
                            // return response()->json('No puede ser modificado', 409);
                        } else {
                            $this->fijarNuevoStock($editItem,$lista['cantidad']);
                            $editItem->cantidad = $lista['cantidad'];
                            $editItem->precio_compra = $lista['precio_compra'];
                            $editItem->producto_id = $lista['producto_id'];
                            $editItem->save();
                            $editItem->lote->lote = $lista['lote'];
                            $editItem->lote->cantidad = $lista['cantidad'];
                            $editItem->lote->cantidad_actual = $lista['cantidad'];
                            $editItem->lote->precio_compra = $lista['precio_compra'];
                            $editItem->lote->proveedor_id = $request->proveedor_id;
                            $editItem->lote->save();
                        }
                    } else {
                        $lote_id = $this->nuevoLote($lista['lote'],$lista['cantidad'],$lista['precio_compra'],$lista['producto_id'],$request->proveedor_id);
                        $newItem = new DetalleIngreso;
                        $newItem->cantidad = $lista['cantidad'];
                        $newItem->precio_compra = $lista['precio_compra'];
                        $newItem->producto_id = $lista['producto_id'];
                        $newItem->lote_id = $lote_id;
                        $newItem->ingreso_id = $editData->id;
                        $newItem->save();
                        $this->addToStock($lista['cantidad'],$lista['producto_id'],1);
                    }
                }
            }
            $editData->save();
            $this->calcularTotal($id);
            DB::commit();
            $data = [
                'success' => 'Se ha actualizado el registro.'
            ];
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            return response()->json($th, 409);
        }
    }
    function nuevoLote($lote,$cantidad,$precio,$producto_id,$proveedor_id) {
        $searchLote = Lote::where('lote',$lote)
        ->where('precio_compra',$precio)
        ->where('producto_id',$producto_id)
        ->first();
        if ($searchLote) {
            $searchLote->cantidad += $cantidad;
            $searchLote->cantidad_actual += $cantidad;
            // $searchLote->fecha_registro = date('Y-m-d H:i:s');
            $searchLote->disponible = true;
            $searchLote->proveedor_id = $proveedor_id;
            $searchLote->user_id = Auth::user()->id;
        } else {
            $searchLote = new Lote;
            $searchLote->lote = $lote;
            $searchLote->cantidad = $cantidad;
            $searchLote->cantidad_actual = $cantidad;
            $searchLote->precio_compra += $precio;
            $searchLote->fecha_registro = date('Y-m-d H:i:s');
            $searchLote->disponible = true;
            $searchLote->producto_id = $producto_id;
            $searchLote->proveedor_id = $proveedor_id;
            $searchLote->user_id = Auth::user()->id;
        }
        $searchLote->save();

        return $searchLote->id;
    }
    function verificarStockDetalle($id) {
        $dt_ingreso = DetalleIngreso::find($id);
            if ($dt_ingreso->lote->cantidad != $dt_ingreso->lote->cantidad_actual) {
                return true;
            }
            // $dtproforma = DetalleProforma::where('lote_id',$dt_ingreso->lote->id)->first();
            // if ($dtproforma) {
            //     return true;
            // }
            $dtproforma = DetalleVenta::where('lote_id',$dt_ingreso->lote->id)->first();
            if ($dtproforma) {
                return true;
            }
        return false;
    }
    function fijarNuevoStock($detalleingreso,$nueva_cantidad) {
        $diferencia = ($nueva_cantidad - $detalleingreso->cantidad);
        $this->addToStock($diferencia,$detalleingreso->producto_id,1);
    }
    public function destroy($id) {
        $data = Ingreso::find($id);
        if (!$data) {
            return response()->json('Registro no encontrado', 409);
        }
        if ($this->verificarStock($id)) {
            return response()->json('No puede ser eliminado', 409);
        }
        try {
            DB::beginTransaction();
            foreach ($data->detalle as $key => $value) {
                $this->addToStock(-$value->cantidad,$value->producto_id,1);
                $lote = Lote::find($value->lote_id);
                $value->delete();
                $lote->delete();
            }
            $data->visible = false;
            $data->save();
            $data->delete();
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
    
    public function eliminardetalle($id) {
        $data = DetalleIngreso::find($id);
        if (!$data) {
            return response()->json('Registro no encontrado', 409);
        }
        if ($this->verificarStockDetalle($id)) {
            return response()->json('No puede ser eliminado', 409);
        }
        try {
            DB::beginTransaction();

            $this->addToStock(-$data->cantidad,$data->producto_id,1);
            $lote = Lote::find($data->lote_id);
            $data->delete();
            $lote->delete();

            $this->calcularTotal($data->ingreso_id);
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
        $ultimo_registro = Ingreso::latest()->first();
        if (!$ultimo_registro) {
            return 1;
        }
        $nro = $ultimo_registro->nro + 1;
        $flag = false;
        for ($i=0; !$flag; $i++) {
            // previene que no se repita el nro
            $query = Ingreso::where('nro',$nro)->first();
            if (!$query) {
                $flag = true;
            } else {
                $nro++;
            }
        }
        return $nro;
    }
}
