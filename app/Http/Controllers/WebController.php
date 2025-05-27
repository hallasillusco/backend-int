<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\Blog;
use App\Models\Lote;
use App\Models\Marca;
use App\Models\Video;
use App\Models\Banner;
use App\Models\Cliente;
use App\Models\Deposito;
use App\Models\Producto;
use App\Models\Proforma;
use App\Models\Categoria;
use App\Models\SubCategoria;
use Illuminate\Http\Request;
use App\Mail\MessageReceived;
use App\Models\TipoCategoria;
use App\Models\DetalleProforma;
use Illuminate\Support\Facades\Mail;

class WebController extends Controller
{
    public function indexweb(Request $request) {
        $term = $request->input('term');
        $sub_categoria_id = $request->get('sub_categoria_id');
        $categoria_id = $request->get('categoria_id');
        $marca_id = $request->get('marca_id');
        $descuento = $request->get('descuento');
        $nuevo = $request->get('nuevo');
        // $this->ponerPortada();
        $data = Producto::sterm($term,'')
        ->ssubcategoria($sub_categoria_id,'')
        ->scategoria($categoria_id,'')
        ->smarca($marca_id,'')
        // ->orderBy('nombre')
        ->latest()
        ->snuevo($nuevo,'')
        ->sdescuento($descuento,'')
        ->habilitado()
        ->with(['tipo','categoria','sub_categoria','marca','unidad','imagenes','colores'])
        ->get();
        // ->paginate(9);
        return response()->json($data, 200);
    }
    //
    public function top() {
        $data = Producto::inRandomOrder()
        ->habilitado()
        ->limit(5)
        ->with(['categoria','sub_categoria','marca','unidad'])
        ->get();
        // $topProductos = Producto::from('productos as p')
        // ->with('detalleventas')
        // ->join('detalle_ventas as dt', 'p.id', 'dt.producto_id')
        // ->select('p.*', DB::raw('SUM(order_product.quantity) as total_vendido'))
        // ->groupBy('p.id')
        // ->orderBy('total_vendido', 'desc')
        // ->take(5)
        // ->get();
        return response()->json($data, 200);
    }
    public function destacados(Request $request) {
        $categoria_id = $request->get('categoria_id');
        $data = Producto::inRandomOrder()
        ->scategoria($categoria_id,'')
        ->destacado()
        ->limit(8)
        ->with(['categoria','sub_categoria','marca','unidad'])
        ->get();
        return response()->json($data, 200);
    }
    public function tipos() {
        $query = TipoCategoria::get();
        $counvali = Categoria::count();
        if ($counvali < 3) {
            // foreach ($query as $tipo) {
            //     $countcat = 1;
            //     for ($i=0; $i < 4; $i++) { 
            //         $newCat = new Categoria;
            //         $newCat->nombre = 'CATEGORIA ' . $countcat++;
            //         $newCat->habilitado = true;
            //         $newCat->tipo_id = $tipo->id;
            //         $newCat->save();
            //         $randItem = rand(4,9);
            //         $countsub = 1;
            //         for ($is=0; $is < $randItem; $is++) {
            //             $newSub = new SubCategoria;
            //             $newSub->nombre = 'Sub category - ' . $countsub++;
            //             $newSub->habilitado = true;
            //             $newSub->categoria_id = $newCat->id;
            //             $newSub->save();
            //         }
            //     }
            // }
        }
        $data = TipoCategoria::with([
            'categorias' => function ($query) {
                $query->where('habilitado',true);
            }
        ])->get();
        return response()->json($data, 200);
    }
    public function banners() {
        $data = Banner::habilitado()->get();
        return response()->json($data, 200);
    }
    public function videos() {
        $data = Video::habilitado()->get();
        return response()->json($data, 200);
    }
    public function tipoblogs($id) {
        $data = Blog::where('tipo_blog_id',$id)->habilitado()->get();
        return response()->json($data, 200);
    }
    public function blogs($id) {
        $blog = Blog::find($id);
        $query = Blog::where('tipo_blog_id',$blog->tipo_blog_id)
        ->where('id','!=',$id)
        ->habilitado()->get();
        $data = [
            'blog' => $blog,
            'blogs' => $query,
        ];
        return response()->json($data, 200);
    }
    public function catdestacados(Request $request) {
        $data = Categoria::from('categorias as c')
        ->join('productos as p','p.categoria_id','c.id')
        ->where('p.habilitado',true)
        ->where('p.destacado',true)
        ->where('c.habilitado',true)
        ->distinct('c.id')
        ->select('c.*')
        ->limit(4)
        ->get();

        foreach ($data as $cat) {
            $cat->cantidad = Producto::where('categoria_id',$cat->id)
            ->where('destacado',true)
            ->where('habilitado',true)
            ->count();
        }
        return response()->json($data, 200);
    }
    
    public function marcas() {
        $data = Marca::habilitado()->get();
        return response()->json($data, 200);
    }
    
    public function categorias() {
        $data = Categoria::with(['sub_categorias'])->get();
        foreach ($data as $cat) {
            $cat->cantidad = Producto::where('categoria_id',$cat->id)->habilitado()->count();
        }
        return response()->json($data, 200);
    }
    public function pedido(Request $request) {

        $fecha = Carbon::now();
        $total = 0;
        foreach ($request->detalle as $lista) {
            $lote = Lote::where('producto_id',$lista['producto_id'])
            ->where('disponible',true)->first();
            $total += $lista['cantidad'] * $lote->producto->precio_unit;
            if ($lista['cantidad'] <= 0) {
                return response()->json('La cantidad debe ser mayor a 0', 409);
            }
        }
        try {
            DB::beginTransaction();
            $cliente = Cliente::where('email',$request->email)->first();
            if ($cliente) {
                $cliente->razon_social = $request->razon_social;
                $cliente->nombre_completo = $request->nombre_completo;
                $cliente->nit = $request->nit;
                $cliente->celular = $request->celular;
                $cliente->direccion = $request->direccion;
                $cliente->habilitado = true;
                $cliente->save();
            } else {
                $cliente = new Cliente;
                $cliente->razon_social = $request->razon_social;
                $cliente->nombre_completo = $request->nombre_completo;
                $cliente->nit = $request->nit;
                $cliente->celular = $request->celular;
                $cliente->direccion = $request->direccion;
                $cliente->email = $request->email;
                $cliente->tipo_cliente_id = 3; // Predeterminado
                $cliente->habilitado = true;
                $cliente->save();
            }
            $newData = new Proforma;
            $newData->nro = $this->getNro();
            $newData->razon_social = $cliente->razon_social;
            $newData->nit = $cliente->nit;
            $newData->tipo_pago = $request->tipo_pago;
            $newData->fecha_registro = date('Y-m-d H:i:s');
            $newData->fecha_vigencia = $fecha->addDay(30)->format('Y-m-d H:i:s');
            $newData->factura = false;
            $newData->activo = true;
            if ($request->factura == 'true' || $request->factura == '1') {
                $newData->factura = true;
            }
            $newData->cliente_id = $cliente->id;
            // sucursal definida
            $newData->sucursal_id = 1;
            // $newData->sucursal_id = $reg_sucursal;
            $newData->user_id = 1;
            $newData->visible = true;
            $newData->web = true;
            // $newData->tipo_pago_id = $request->tipo_pago_id;
            $newData->save();
            if ($request->detalle) {
                foreach ($request->detalle as $lista) {
                    $lote = Lote::where('producto_id',$lista['producto_id'])
                    ->where('disponible',true)->first();
                    $newItem = new DetalleProforma;
                    $newItem->cantidad = $lista['cantidad'];
                    $newItem->precio = $lote->producto->precio_unit;
                    $newItem->descuento = $lote->producto->descuento;
                    if ($lote->producto->descuento) {
                        $newItem->precio = $lote->producto->precio_desc;
                    }
                    $newItem->producto_id = $lote->producto_id;
                    $newItem->lote_id = $lote->id;
                    $newItem->proforma_id = $newData->id;
                    $newItem->save();
                }
            }
            $newData->total = $total;
            $newData->save();
            // $this->enviarEmail(1,$newData->id);
            DB::commit();
            $detail = Proforma::with(['detalle','usuario','cliente'])->find($newData->id);
            $data = [
                'id' => $newData->id,
                'data' => $detail,
                'success' => 'Se ha realizado un nuevo registro.'
            ];
            return response()->json($data, 201);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            return response()->json($th, 409);
        }
    }
    function enviarEmail($flag,$id) {
        if ($flag) {
            $proforma = Proforma::find($id);
            // $proforma = Proforma::find(9);
            if ($proforma) {
                $code = 84054;
                Mail::to('nelson1008zm@gmail.com')->send(new MessageReceived($proforma));
                // return response()->json('$data', 200);
            }
        }
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
    public function pedidobuscar(Request $request) {
        $nro = $request->get('numero');
        $celular = $request->get('celular');
        $data = Proforma::from('proformas as p')
        ->join('clientes as c','c.id','p.cliente_id')
        ->where('c.celular',$celular)
        ->where('p.nro',$nro)
        ->select('p.*')
        ->with(['detalle','usuario','cliente','comprobante'])
        ->first();
        return response()->json($data, 200);
    }
    public function subida(Request $request) {
        $query = Proforma::find($request->id);
        if (!$query) {
            return response()->json('Registro no encontrado', 409);
        }
        $name = null;
        $dir_folder = 'images/client';
        if($request->hasFile('file')){
            if (!is_dir(public_path().'/'.$dir_folder)) {
                $crearDir = mkdir(public_path().'/'.$dir_folder, 0777, true);
            }
            $file = $request->file('file');
            //concatena la hora y el nombre del archivo
            $name = $file->getClientOriginalName();
            $file->move(public_path().'/'.$dir_folder, $name);
            $ruta = public_path().'/'.$dir_folder.'/'. $name;
        } else {
            return response()->json('Debe subir una imagen de la transferencia', 409);
        }
        foreach ($query->comprobantes as $key => $value) {
            $value->activo = false;
            $value->save();
        }
        $newItem = new Deposito;
        if ($name) {
            $newItem->img_url = $dir_folder.'/'.$name;
        }
        $newItem->revisado = false;
        $newItem->activo = true;
        $newItem->proforma_id = $query->id;
        $newItem->save();
        $data = [
            'success' => 'Registrado con éxito'
        ];
        return response()->json($data, 200);
    }
}
