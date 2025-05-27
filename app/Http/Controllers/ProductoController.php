<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Color;
use App\Models\Galeria;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function importar(Request $request) {
        $data = [
            'success' => 'Metodo de importacion excel'
        ];
        return response()->json($data, 200);
    }
    public function index(Request $request) {
        $query = Producto::take(1)->get();
        $query = Producto::get();
        $cod = 0;
        // foreach ($query as $value) {
        //     $rand = rand(-5,10);
        //     // $value->codigo = ++$cod;
        //     // if (!$value->img_url) {
        //     //     // $value->img_url = 'images/headphone-1.png';
        //     // }
        //     // // $value->precio_ant = $value->precio_ant?$value->precio_ant + $rand:$value->precio_ant;
        //     // $value->precio_unit = $value->precio_unit + $rand;
        //     // $value->descripcion = '<div _ngcontent-ng-c4203954032="" class="row"><div _ngcontent-ng-c4203954032="" class="col-lg-12"><div _ngcontent-ng-c4203954032="" class="tp-product-details-desc-content pt-25"><span _ngcontent-ng-c4203954032="">Headphones</span><h3 _ngcontent-ng-c4203954032="" class="tp-product-details-desc-title">Gaming Headphone</h3><p _ngcontent-ng-c4203954032="">Jabra Evolve2 75 USB-A MS Teams Stereo Headset The Jabra Evolve2 75 USB-A MS Teams Stereo Headset has replaced previous hybrid working standards. Industry-leading call quality thanks to top-notch audio engineering. With this intelligent headset, you can stay connected and productive from the first call of the day to the last train home. With an ergonomic earcup design, this headset invented a brand-new dual-foam technology. You will be comfortable from the first call to the last thanks to the re-engineered leatherette ear cushion design that allows for better airflow. We can provide exceptional noise isolation and the best all-day comfort by mixing firm foam for the outer with soft foam for the interior of the ear cushions. So that you may receive Active Noise-Cancellation (ANC) performance that is even greater in a headset that you can wear for whatever length you wish. The headset also offers MS Teams Certifications and other features like Busylight, Calls controls, Voice guiding, and Wireless range (ft): Up to 100 feet. Best-in-class. Boom The most recent Jabra Evolve2 75 USB-A MS Teams Stereo Headset offers professional-grade call performance that leads the industry, yet Evolve2 75 wins best-in-class. Additionally, this includes a redesigned microphone boom arm that is 33 percent shorter than the Evolve 75 and offers the industry-leading call performance for which Jabra headsets are known. It complies with Microsoft Open Office criteria and is specially tuned for outstanding conversations in open-plan workplaces and other loud environments when the microphone boom arm is lowered in Performance Mode.</p></div></div></div>';
        //     // $value->slug = $this->urlSlug($value->nombre);
        //     $value->tipo_id = $value->categoria->tipo_id;
        //     $value->save();
        //     // $imagenes = [
        //     //     'headphone-1.png',
        //     //     'headphone-2.png',
        //     //     'headphone-3.png',
        //     //     'headphone-4.png',
        //     // ];
        //     // $dir_folder = 'images';
        //     // foreach ($imagenes as $img) {
        //     //     // $newDataArchi = new Galeria;
        //     //     // $newDataArchi->portada = false;
        //     //     // $newDataArchi->habilitado = true;
        //     //     // // $newDataArchi->texto = $details[$i];
        //     //     // if ($img) {
        //     //     //     $newDataArchi->img_url = $dir_folder.'/'.$img;
        //     //     // }
        //     //     // $newDataArchi->producto_id = $value->id;
        //     //     // // $newDataArchi->save();
        //     // }
        // }
        $term = $request->input('term');
        $categoria = $request->get('categoria_id');
        // $this->ponerPortada();
        $data = Producto::sterm($term,'')
        ->scategoria($categoria,'')
        // ->orderBy('nombre')
        ->latest()
        ->with(['tipo','categoria','sub_categoria','marca','unidad','imagenes','colores'])
        ->get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Producto::with(['tipo','categoria','sub_categoria','marca','unidad','imagenes','colores'])->find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        // no Productos repetidos
        $valid = Producto::where('nombre',$request->nombre)->first();
        if ($valid) {
            return response()->json('El nombre ya fue registrado.', 409);
        }
        if (!$request->codigo) {
            return response()->json('Código es requerido.', 409);
        } else {
            $val_cod = Producto::where('codigo',$request->codigo)->first();
            if ($val_cod) {
                return response()->json('Elija otro código.', 409);
            }
        }
        $name = null;
        $dir_folder = 'images/productos';
        if($request->hasFile('file')){
            if (!is_dir(public_path().'/'.$dir_folder)) {
                $crearDir = mkdir(public_path().'/'.$dir_folder, 0777, true);
            }
            $file = $request->file('file');
            //concatena la hora y el nombre del archivo
            $name = $file->getClientOriginalName();
            $file->move(public_path().'/'.$dir_folder, $name);
            $ruta = public_path().'/'.$dir_folder.'/'. $name;
        }
        $categoria = Categoria::find($request->categoria_id);
        $newData = new Producto;
        $newData->nombre = $request->nombre;
        $newData->codigo = $request->codigo;
        $newData->slug = $this->urlSlug($request->nombre);
        $newData->descripcion = $request->descripcion;
        $newData->detalle = $request->detalle;
        if ($request->descuento && $request->descuento != 'null') {
            $newData->descuento = $request->descuento;
            $newData->precio_desc = $request->precio_unit - ($request->precio_unit*$request->descuento/100);
        }
        $newData->precio_unit = $request->precio_unit;
        $newData->stock = 0;
        $newData->habilitado = true;
        $newData->destacado = false;
        $newData->unidad_id = $request->unidad_id;
        $newData->tipo_id = $categoria->tipo_id;
        $newData->categoria_id = $request->categoria_id;
        $newData->sub_categoria_id = $request->sub_categoria_id;
        $newData->marca_id = $request->marca_id;
        if ($name) {
            $newData->img_url = $dir_folder.'/'.$name;
        }
        $newData->save();
        if ($name) {
            $newImg = new Galeria;
            $newImg->img_url = $dir_folder.'/'.$name;
            $newImg->portada = true;
            $newImg->habilitado = true;
            $newImg->producto_id = $newData->id;
            $newImg->save();
        }
        $colores = json_decode($request->colores);
        foreach ($colores as $color) {
            $newColor = new Color;
            $newColor->nombre = $color->nombre;
            $newColor->codigo = $color->codigo;
            $newColor->producto_id = $newData->id;
            $newColor->save();
        }
        $this->subirImagenes($request,$newData->id);
        $data = [
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    function codigo($nro) {
        $config = Configuracion::first();
        $digitos = $config->cod_p_digitos;
        $kard = '00000000000000000000' . $nro;
        $parte = mb_substr($kard,-$digitos,$digitos);
        $texto = $config->cod_p_prefijo . $parte;
        return $texto;
    }
    function subirImagenes(Request $request,$producto_id) {
        $name = null;
        $dir_folder = 'images/productos';
        $tipo = null;
        
        $uploadFolder = public_path().'/'.$dir_folder . "/";
        if (!is_dir(public_path().'/'.$dir_folder)) {
            $crearDir = mkdir(public_path().'/'.$dir_folder, 0777, true);
        }
        if ($request->archivos) {
            if (count($request->archivos)) {
                $files = $_FILES["archivos"]["name"];
                // $details = $request->get('textos');
                for  ($i =  0; $i < count($files); $i++)  {
                    $filename=$files[$i];
                    $aux = explode(".",$filename);
                    $ext =  end($aux);
                    $original = pathinfo($filename, PATHINFO_FILENAME);
                    $fileurl = $original .  "-"  . date("YmdHis")  .  "."  . $ext;
                    move_uploaded_file($_FILES["archivos"]["tmp_name"][$i], $uploadFolder . $fileurl);
                    $newDataArchi = new Galeria;
                    $newDataArchi->portada = false;
                    $newDataArchi->habilitado = true;
                    // $newDataArchi->texto = $details[$i];
                    if ($fileurl) {
                        $newDataArchi->img_url = $dir_folder.'/'.$fileurl;
                    }
                    $newDataArchi->producto_id = $producto_id;
                    $newDataArchi->save();
                }
            }
        }
    }
    public function update(Request $request, $id) {
        $categoria = Categoria::find($request->categoria_id);
        $editData = Producto::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        // no Productos repetidos
        $valid = Producto::where('nombre',$request->nombre)
        ->where('id','!=',$id)->first();
        if ($valid) {
            return response()->json('El nombre ya fue registrado.', 409);
        }
        $valid = Producto::where('codigo',$request->codigo)
        ->where('id','!=',$id)->first();
        if ($valid) {
            return response()->json('El codigo ya fue registrado.', 409);
        }
        $name = null;
        $dir_folder = 'images/productos';
        if($request->hasFile('file')){
            if (!is_dir(public_path().'/'.$dir_folder)) {
                $crearDir = mkdir(public_path().'/'.$dir_folder, 0777, true);
            }
            $file = $request->file('file');
            //concatena la hora y el nombre del archivo
            $name = $file->getClientOriginalName();
            $file->move(public_path().'/'.$dir_folder, $name);
            $ruta = public_path().'/'.$dir_folder.'/'. $name;
        }
        if ($name) {
            $editData->img_url = $dir_folder.'/'.$name;
            
            $this->quitarPortada($editData->id);

            $newImg = new Galeria;
            $newImg->img_url = $dir_folder.'/'.$name;
            $newImg->portada = true;
            $newImg->habilitado = true;
            $newImg->producto_id = $editData->id;
            $newImg->save();
        }
        $editData->codigo = $request->codigo;
        $editData->nombre = $request->nombre;
        $editData->slug = $this->urlSlug($request->nombre);
        $editData->detalle = $request->detalle;
        $editData->descripcion = $request->descripcion;
        // $editData->precio_desc = $request->precio_desc;
        if ($request->descuento && $request->descuento != 'null') {
            $editData->descuento = $request->descuento;
            $editData->precio_desc = $request->precio_unit - ($request->precio_unit*$request->descuento/100);
        }
        $editData->precio_unit = $request->precio_unit;
        $editData->unidad_id = $request->unidad_id;
        $editData->tipo_id = $categoria->tipo_id;
        $editData->categoria_id = $request->categoria_id;
        $editData->sub_categoria_id = $request->sub_categoria_id;
        $editData->marca_id = $request->marca_id;
        $editData->save();
        $colores = json_decode($request->colores);
        foreach ($colores as $color) {
            $editColor = Color::find($color->id);
            if ($editColor) {
                $editColor->nombre = $color->nombre;
                $editColor->codigo = $color->codigo;
                $editColor->save();
            } else {
                $newColor = new Color;
                $newColor->nombre = $color->nombre;
                $newColor->codigo = $color->codigo;
                $newColor->producto_id = $editData->id;
                $newColor->save();
            }
            
        }
        $this->subirImagenes($request,$editData->id);

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    function urlSlug($nombre){
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($nombre, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    }
    function quitarPortada($id) {
        $producto = Producto::find($id);
        foreach ($producto->imagenes as $key => $value) {
            $value->portada = false;
            $value->save();
        }
    }
    function ponerPortada() {
        $prods = Producto::get();
        foreach ($prods as $key => $value) {
            $newImg = new Galeria;
            $newImg->img_url = $value->img_url;
            $newImg->portada = true;
            $newImg->habilitado = true;
            $newImg->producto_id = $value->id;
            $newImg->save();
            $newImg = new Galeria;
            $newImg->img_url = $value->img_url;
            $newImg->portada = false;
            $newImg->habilitado = true;
            $newImg->producto_id = $value->id;
            $newImg->save();
        }
    }
    public function destroy($id) {
        $data = Producto::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        // $valid = Almacen::where('producto_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        // $valid = DetallePedido::where('producto_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        $data->delete();
        $data = [
            'success' => 'Eliminado'
        ];
        return response()->json($data, 200);
    }
    public function color($id) {
        $data = Color::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        $data->delete();
        $data = [
            'success' => 'Eliminado'
        ];
        return response()->json($data, 200);
    }

    public function destacar($id) {
        $item = Producto::find($id);
        $text = 'destacado.';
        if ($item->destacado) {
            $item->destacado = false;
            $text = 'no destacado.';
        } else {
            $item->destacado = true;
        }
        $item->save();
        return response()->json(['success' => 'Item '.$text], 200);
    }
    public function habilitar($id) {
        $item = Producto::find($id);
        $text = 'habilitado.';
        if ($item->habilitado) {
            $item->habilitado = false;
            $text = 'deshabilitado.';
        } else {
            $item->habilitado = true;
        }
        $item->save();
        return response()->json(['success' => 'Item '.$text], 200);
    }
    public function habilitados() {
        $data = Producto::habilitado()->with(['tipo','categoria','sub_categoria','marca','unidad','colores'])->get();
        foreach ($data as $prod) {
            // // $st = 0;
            // $query = Producto::find($prod->id);
            // $st = $query->disponible->sum('cantidad');
            // $prod->stock = $st;
        }
        return response()->json($data, 200);
    }
    public function showByCategoria($id) {
        $data = Producto::where('categoria_id',$id)->get();
        return response()->json($data, 200);
    }
    public function existenciasPdf() {
        $datos = Producto::get();
        $data = [
            'i' => 0,
            // 'config' => $config,
            'datos' => $datos,
            // 'ini' => $request->get('fecha_inicio'),
            // 'fin' => $request->get('fecha_fin'),
        ];
        // return view('reporte.r-pdf_existencia', $data);

        $pdf = PDF::loadView('reportes.pdf_existencia', $data);
        // ->setPaper('letter','landscape');
        return $pdf->stream('reporte.pdf');
    }
}
