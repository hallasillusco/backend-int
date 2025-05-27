<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('token', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('api.login');

Route::group(['middleware' => 'auth:api'], function () {
    // Route::post('users/change-password', [App\Http\Controllers\Api\AuthController::class, 'updatePassword']);
    Route::get('logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::apiResources([
    ]);
});

Route::get('borrar', [App\Http\Controllers\ReporteController::class, 'borrar']);
Route::post('web/pedido', [App\Http\Controllers\WebController::class, 'pedido']);
Route::post('web/pedido/subida', [App\Http\Controllers\WebController::class, 'subida']);
Route::get('web/pedido', [App\Http\Controllers\WebController::class, 'pedidobuscar']);
Route::get('web/videos', [App\Http\Controllers\WebController::class, 'videos']);
// Route::get('web/blogs', [App\Http\Controllers\WebController::class, 'blogs']);
Route::get('web/tipoblogs/{id}', [App\Http\Controllers\WebController::class, 'tipoblogs']);
Route::get('web/blogs/{id}', [App\Http\Controllers\WebController::class, 'blogs']);
Route::get('web/informaciones', [App\Http\Controllers\AyudaController::class, 'index']);
// Route::get('web/informaciones', [App\Http\Controllers\WebController::class, 'informaciones']);
Route::get('web/informaciones/{id}', [App\Http\Controllers\AyudaController::class, 'show']);
Route::get('web/banners', [App\Http\Controllers\WebController::class, 'banners']);
Route::get('web/tipos', [App\Http\Controllers\WebController::class, 'tipos']);
Route::get('web/productos/top', [App\Http\Controllers\WebController::class, 'top']);
Route::get('web/productos/destacados', [App\Http\Controllers\WebController::class, 'destacados']);
Route::get('web/productos', [App\Http\Controllers\WebController::class, 'indexweb']);
Route::get('web/productos/{id}', [App\Http\Controllers\ProductoController::class, 'show']);
Route::get('web/categorias/destacados', [App\Http\Controllers\WebController::class, 'catdestacados']);
Route::get('web/categorias', [App\Http\Controllers\WebController::class, 'categorias']);
Route::get('web/marcas', [App\Http\Controllers\WebController::class, 'marcas']);
Route::get('reportes/datos', [App\Http\Controllers\ReporteController::class, 'datos']);
Route::get('reportes/masvendidos', [App\Http\Controllers\ReporteController::class, 'masvendidos']);
Route::get('reportes/agotados', [App\Http\Controllers\ReporteController::class, 'agotados']);
Route::get('reportes/graficaventas', [App\Http\Controllers\ReporteController::class, 'graficaventas']);
Route::get('reportes/graficacategorias', [App\Http\Controllers\ReporteController::class, 'graficacategorias']);
Route::get('reportes/existencias', [App\Http\Controllers\ProductoController::class, 'index']);
Route::get('reportes/existenciaspdf', [App\Http\Controllers\ProductoController::class, 'existenciasPdf']);
Route::get('reportes/ventaproductos', [App\Http\Controllers\ReporteController::class, 'ventaproductos']);
Route::get('reportes/ventaproductospdf', [App\Http\Controllers\ReporteController::class, 'ventaproductospdf']);
Route::get('ingresos/pdf/{id}', [App\Http\Controllers\ReporteController::class, 'ingresoPdf']);
Route::get('proformas/pdf/{id}', [App\Http\Controllers\ReporteController::class, 'proformaPdf']);
Route::get('ventas/pdf/{id}', [App\Http\Controllers\ReporteController::class, 'ventaPdf']);
// Route::apiResources([
//     'users' => App\Http\Controllers\UserController::class,
// ]);
Route::get('banners/habilitados', [App\Http\Controllers\BannerController::class, 'habilitados']);
Route::get('banners/habilitar/{id}', [App\Http\Controllers\BannerController::class, 'habilitar']);

Route::get('clientes/buscar', [App\Http\Controllers\ClienteController::class, 'buscar']);

Route::get('users/roles', [App\Http\Controllers\UserController::class, 'roles']);

Route::post('chatbot', [App\Http\Controllers\ChatbotController::class, 'handleRequest']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('users/habilitar/{id}', [App\Http\Controllers\UserController::class, 'habilitar']);

    Route::get('categorias/habilitados', [App\Http\Controllers\CategoriaController::class, 'habilitados']);
    Route::get('categorias/habilitar/{id}', [App\Http\Controllers\CategoriaController::class, 'habilitar']);
    Route::get('categorias/tipos/{id}', [App\Http\Controllers\CategoriaController::class, 'showByTipo']);
    
    Route::get('unidades/habilitados', [App\Http\Controllers\UnidadController::class, 'habilitados']);
    Route::get('unidades/habilitar/{id}', [App\Http\Controllers\UnidadController::class, 'habilitar']);
    
    Route::get('tipos/habilitados', [App\Http\Controllers\TipoCategoriaController::class, 'habilitados']);
    Route::get('tipos/habilitar/{id}', [App\Http\Controllers\TipoCategoriaController::class, 'habilitar']);

    Route::get('subcategorias/habilitados', [App\Http\Controllers\SubCategoriaController::class, 'habilitados']);
    Route::get('subcategorias/habilitar/{id}', [App\Http\Controllers\SubCategoriaController::class, 'habilitar']);
    Route::get('subcategorias/categorias/{id}', [App\Http\Controllers\SubCategoriaController::class, 'showByCategoria']);
    
    Route::get('marcas/habilitados', [App\Http\Controllers\MarcaController::class, 'habilitados']);
    Route::get('marcas/habilitar/{id}', [App\Http\Controllers\MarcaController::class, 'habilitar']);
    
    Route::get('productos/habilitados', [App\Http\Controllers\ProductoController::class, 'habilitados']);
    Route::get('productos/habilitar/{id}', [App\Http\Controllers\ProductoController::class, 'habilitar']);
    Route::get('productos/destacar/{id}', [App\Http\Controllers\ProductoController::class, 'destacar']);
    Route::delete('productos/color/{id}', [App\Http\Controllers\ProductoController::class, 'color']);
    
    Route::get('proveedores/habilitados', [App\Http\Controllers\ProveedorController::class, 'habilitados']);
    Route::get('proveedores/habilitar/{id}', [App\Http\Controllers\ProveedorController::class, 'habilitar']);
    
    Route::get('sucursales/habilitados', [App\Http\Controllers\SucursalController::class, 'habilitados']);
    Route::get('sucursales/habilitar/{id}', [App\Http\Controllers\SucursalController::class, 'habilitar']);

    Route::get('clientes/habilitados', [App\Http\Controllers\ClienteController::class, 'habilitados']);
    Route::get('clientes/habilitar/{id}', [App\Http\Controllers\ClienteController::class, 'habilitar']);

    Route::get('informaciones/habilitados', [App\Http\Controllers\AyudaController::class, 'habilitados']);
    Route::get('informaciones/habilitar/{id}', [App\Http\Controllers\AyudaController::class, 'habilitar']);

    Route::post('ventas/proformas/{id}', [App\Http\Controllers\ProformaController::class, 'ventaproforma']);
    Route::post('ventas/webproformas', [App\Http\Controllers\ProformaController::class, 'ventawebproforma']);

    Route::delete('ingresos/detalles/{id}', [App\Http\Controllers\IngresoController::class, 'eliminardetalle']);
    Route::get('lotes/habilitados', [App\Http\Controllers\LoteController::class, 'habilitados']);
    Route::get('blogs/tipos', [App\Http\Controllers\BlogController::class, 'tipos']);

    Route::get('videos/habilitados', [App\Http\Controllers\VideoController::class, 'habilitados']);
    Route::get('videos/habilitar/{id}', [App\Http\Controllers\VideoController::class, 'habilitar']);
    
    Route::get('blogs/habilitados', [App\Http\Controllers\BlogController::class, 'habilitados']);
    Route::get('blogs/habilitar/{id}', [App\Http\Controllers\BlogController::class, 'habilitar']);
    
    Route::get('tipoclientes/habilitados', [App\Http\Controllers\TipoClienteController::class, 'habilitados']);
    Route::get('tipoclientes/habilitar/{id}', [App\Http\Controllers\TipoClienteController::class, 'habilitar']);

    Route::apiResources([
        'users' => App\Http\Controllers\UserController::class,
        'banners' => App\Http\Controllers\BannerController::class,
        'tipos' => App\Http\Controllers\TipoCategoriaController::class,
        'categorias' => App\Http\Controllers\CategoriaController::class,
        'subcategorias' => App\Http\Controllers\SubCategoriaController::class,
        'unidades' => App\Http\Controllers\UnidadController::class,
        'marcas' => App\Http\Controllers\MarcaController::class,
        'productos' => App\Http\Controllers\ProductoController::class,
        'proveedores' => App\Http\Controllers\ProveedorController::class,
        'sucursales' => App\Http\Controllers\SucursalController::class,
        'ingresos' => App\Http\Controllers\IngresoController::class,
        'clientes' => App\Http\Controllers\ClienteController::class,
        'proformas' => App\Http\Controllers\ProformaController::class,
        'ventas' => App\Http\Controllers\VentaController::class,
        'informaciones' => App\Http\Controllers\AyudaController::class,
        'blogs' => App\Http\Controllers\BlogController::class,
        'videos' => App\Http\Controllers\VideoController::class,
        'configuracion' => App\Http\Controllers\ConfigController::class,
        'tipoclientes' => App\Http\Controllers\TipoClienteController::class,
        'accesos' => App\Http\Controllers\AccesoController::class,
    ]);
});
// Route::group(['middleware' => 'auth:api'], function () {
//     Route::apiResources([
//         'users' => App\Http\Controllers\UserController::class,
//     ]);
// });
Route::post('/excel/producto', [App\Http\Controllers\ExcelImportProductoController::class, 'importProducto']);
