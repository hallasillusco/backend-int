<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Marca;
use App\Models\Unidad;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\SubCategoria;
use Illuminate\Http\Request;
use App\Models\TipoCategoria;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportProductoController extends Controller
{
    public function importProducto(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();

        $rowIterator = $worksheet->getRowIterator(2);

        foreach ($rowIterator as $row) {
            
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            $existingRows = [];
            $allFieldsEmpty = true;

            foreach ($cellIterator as $cell) {
                $value = $cell->getValue();
                $data[] = $value ?? '';

                if (!empty($value)) {
                    $allFieldsEmpty = false;
                }
               
            }   
    
            if ($allFieldsEmpty) {
                continue; // Saltar esta iteración si todos los campos están vacíos
            }
            $existingProduct = Producto::where('codigo', $data[0])->orWhere('nombre', $data[1])->first();
            if ($existingProduct) {
                $existingRows[] = "Producto con código {$data[0]} o nombre {$data[1]} ya existe.";
            }
            
            $nombreTipoCat = \DB::table('tipo_categorias')->where('nombre', $data[3])->first(); 
            if(!$nombreTipoCat){
                $nombreTipoCat = $this->crearTipo($data[3]);
                
                // $errors[] = 'El nombre de tipo_Categorias no existe.';
            }
            $nombreCategorias = \DB::table('categorias')->where('nombre', $data[4])->first(); 
            if(!$nombreCategorias){
                $nombreCategorias=$this->crearCategoria($data[4], $nombreTipoCat);
                // $errors[] = 'El nombre de Categorias no existe.';
            }
            $nombreUnidads = \DB::table('unidads')->where('nombre', $data[2])->first(); 
            if(!$nombreUnidads){
                $nombreUnidads=$this->crearUnidad($data[2]);
                // $errors[] = 'El nombre de Unidad no existe.';
            }
            $nombreMarcas = \DB::table('marcas')->where('nombre', $data[6])->first(); 
            if(!$nombreMarcas){
                $nombreMarcas=$this->crearMarcas($data[6]);
                // $errors[] = 'El nombre de Marcas no existe.';
            }
            $nombreSubCategoria = \DB::table('sub_categorias')->where('nombre', $data[5])->where('categoria_id',$nombreCategorias->id)->first(); 
            if(!$nombreSubCategoria){
                $nombreSubCategoria=$this->crearSubCategoria($data[5], $nombreCategorias);
                // $errors[] = 'El nombre de sub_Categorias no existe.';
            }
            
                $validRows[] = [$data, $nombreTipoCat, $nombreCategorias, $nombreUnidads, $nombreMarcas, $nombreSubCategoria];
            
        }
        if (!empty($errors)) {
            return response()->json(['message' => 'Hubo errores al importar el archivo.', 'errors' => $errors], 400);
        }

        foreach ($validRows as $rowData) {
            $this->crearProducto(...$rowData);
        }
        return response()->json([
            'message' => 'Archivo Produto Excel se importo correctamente.',
            'Datos Existentes' => $existingRows,
        ]);

    }
    public function crearProducto($data, $nombreTipoCat, $nombreCategorias, $nombreUnidads, $nombreMarcas, $nombreSubCategoria){

        Producto::create([
            'codigo' => $data[0],
            'nombre' => $data[1],

            // 'detalle' => 'mUNDOOOO',
            // 'descripcion' => '$data[3]',
            'precio_desc' => 0, 
            'descuento' => 0,
            'habilitado' => 0,
            'destacado' => 0,
            
            'tipo_id' => $nombreTipoCat->id,
            'categoria_id' =>$nombreCategorias->id,
            'unidad_id' =>$nombreUnidads->id,
            'marca_id' =>$nombreMarcas->id,
            'sub_categoria_id' =>$nombreSubCategoria->id,
            'precio_unit' => $data[7],
        
        ]);   
        }
    public function crearCategoria($nombreCategorias, $nombreTipoCat){
        $data = [
            'nombre' =>$nombreCategorias,
            'tipo_id' => $nombreTipoCat->id,
            'habilitado' => 0,
        ];
        $categoria = Categoria::create($data);
        return $categoria;
    }
    public function crearTipo($nombreTipoCat){
        $data = [
            'nombre' =>$nombreTipoCat,
            'habilitado' => 0,
            'menu'=> 0,
        ];
        $categoria = TipoCategoria::create($data);
        return $categoria;
    }
    public function crearUnidad($nombreUnidads){
        $sigla = substr($nombreUnidads, 0, 2);
        $data = [
            'nombre' =>$nombreUnidads,
            'sigla' => $sigla,
            'habilitado' => 0,
        ];
        $categoria = Unidad::create($data);
        return $categoria;
    }
    public function crearMarcas($nombreMarcas){
        $data = [
            'nombre' => $nombreMarcas,
            'habilitado' => 0,
        ];
        $categoria = Marca::create($data);
        return $categoria;
    }
    public function crearSubCategoria($nombreSubCategoria, $nombreCategorias){
        $data = [
            'nombre' => $nombreSubCategoria,
            'habilitado' => 0,
            'categoria_id' => $nombreCategorias->id,
        ];
        $categoria = SubCategoria::create($data);
        return $categoria;
    }

}
