<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    
    public function habilitados() {
        $data = Lote::where('cantidad_actual','>',0)
        ->with(['producto'])->get();
        foreach ($data as $prod) {
            // $st = 0;
            // $query = Producto::find($prod->id);
            // $st = $query->disponible->sum('cantidad');
            // $prod->stock = $st;
            $prod->txt_detalle = $prod->lote . ' [' . $prod->producto->codigo . '] ' . $prod->producto->nombre . ' - ' . $prod->producto->unidad->sigla;
        }
        return response()->json($data, 200);
    }
}
