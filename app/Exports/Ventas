<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class VentasPrediccionExport implements FromCollection
{
    public function collection()
    {
        return DB::table('ventas_predicciones')
            ->select('mes', 'ventas_reales', 'ventas_previstas')
            ->orderBy('mes')
            ->get();
    }
}
