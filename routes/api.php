<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ReporteController,
    WebController,
    ProductoController,
    AyudaController,
    ClienteController,
    BannerController,
    UserController,
    ChatbotController,
    CategoriaController,
    UnidadController,
    TipoCategoriaController,
    SubCategoriaController,
    MarcaController,
    ProveedorController,
    SucursalController,
    IngresoController,
    ProformaController,
    VentaController,
    BlogController,
    VideoController,
    ConfigController,
    TipoClienteController,
    AccesoController
};
use App\Http\Controllers\Api\AuthController;


Route::middleware(['auth:api', 'rol:Administrador'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    // Agrega más rutas exclusivas
});

Route::middleware(['auth:api', 'rol:Almacén'])->group(function () {
    Route::get('/productos', [ProductoController::class, 'index']);
});

Route::middleware(['auth:api', 'rol:Vendedor'])->group(function () {
    Route::get('/ventas', [VentaController::class, 'index']);
});





Route::post('token', [AuthController::class, 'login'])->name('api.login');

Route::get('/reportes/getDatosPrediccion', [ReporteController::class, 'getDatosPrediccion']);
Route::get('reportes/datos', [ReporteController::class, 'datos']);
Route::get('/reportes/getDatosMasVendidos', [ReporteController::class, 'getDatosMasVendidos']);

Route::get('/reportes/getDatosComparativaMensual', [ReporteController::class, 'getDatosComparativaMensual']); // ✅ NUEVA RUTA


Route::get('reportes/agotados', [ReporteController::class, 'agotados']);
Route::get('reportes/graficaventas', [ReporteController::class, 'graficaventas']);
Route::get('reportes/graficacategorias', [ReporteController::class, 'graficacategorias']);
Route::get('reportes/existencias', [ProductoController::class, 'index']);
Route::get('reportes/existenciaspdf', [ProductoController::class, 'existenciasPdf']);
Route::get('reportes/ventaproductos', [ReporteController::class, 'ventaproductos']);
Route::get('reportes/ventaproductospdf', [ReporteController::class, 'ventaproductospdf']);
Route::get('ingresos/pdf/{id}', [ReporteController::class, 'ingresoPdf']);
Route::get('proformas/pdf/{id}', [ReporteController::class, 'proformaPdf']);
Route::get('ventas/pdf/{id}', [ReporteController::class, 'ventaPdf']);

Route::post('web/pedido', [WebController::class, 'pedido']);
Route::post('web/pedido/subida', [WebController::class, 'subida']);
Route::get('web/pedido', [WebController::class, 'pedidobuscar']);
Route::get('web/videos', [WebController::class, 'videos']);
Route::get('web/tipoblogs/{id}', [WebController::class, 'tipoblogs']);
Route::get('web/blogs/{id}', [WebController::class, 'blogs']);
Route::get('web/informaciones', [AyudaController::class, 'index']);
Route::get('web/informaciones/{id}', [AyudaController::class, 'show']);
Route::get('web/banners', [WebController::class, 'banners']);
Route::get('web/tipos', [WebController::class, 'tipos']);
Route::get('web/productos/top', [WebController::class, 'top']);
Route::get('web/productos/destacados', [WebController::class, 'destacados']);
Route::get('web/productos', [WebController::class, 'indexweb']);
Route::get('web/productos/{id}', [ProductoController::class, 'show']);
Route::get('web/categorias/destacados', [WebController::class, 'catdestacados']);
Route::get('web/categorias', [WebController::class, 'categorias']);
Route::get('web/marcas', [WebController::class, 'marcas']);

Route::get('borrar', [ReporteController::class, 'borrar']);
Route::post('/excel/producto', [\App\Http\Controllers\ExcelImportProductoController::class, 'importProducto']);

Route::get('clientes/buscar', [ClienteController::class, 'buscar']);
Route::get('users/roles', [UserController::class, 'roles']);
Route::post('chatbot', [ChatbotController::class, 'handleRequest']);

Route::get('banners/habilitados', [BannerController::class, 'habilitados']);
Route::get('banners/habilitar/{id}', [BannerController::class, 'habilitar']);

Route::options('/{any}', function () {
    return response()->json([], 204);
})->where('any', '.*');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('users/habilitar/{id}', [UserController::class, 'habilitar']);
    Route::get('categorias/habilitados', [CategoriaController::class, 'habilitados']);
    Route::get('categorias/habilitar/{id}', [CategoriaController::class, 'habilitar']);
    Route::get('categorias/tipos/{id}', [CategoriaController::class, 'showByTipo']);
    Route::get('unidades/habilitados', [UnidadController::class, 'habilitados']);
    Route::get('unidades/habilitar/{id}', [UnidadController::class, 'habilitar']);
    Route::get('tipos/habilitados', [TipoCategoriaController::class, 'habilitados']);
    Route::get('tipos/habilitar/{id}', [TipoCategoriaController::class, 'habilitar']);
    Route::get('subcategorias/habilitados', [SubCategoriaController::class, 'habilitados']);
    Route::get('subcategorias/habilitar/{id}', [SubCategoriaController::class, 'habilitar']);
    Route::get('subcategorias/categorias/{id}', [SubCategoriaController::class, 'showByCategoria']);
    Route::get('marcas/habilitados', [MarcaController::class, 'habilitados']);
    Route::get('marcas/habilitar/{id}', [MarcaController::class, 'habilitar']);
    Route::get('productos/habilitados', [ProductoController::class, 'habilitados']);
    Route::get('productos/habilitar/{id}', [ProductoController::class, 'habilitar']);
    Route::get('productos/destacar/{id}', [ProductoController::class, 'destacar']);
    Route::delete('productos/color/{id}', [ProductoController::class, 'color']);
    Route::get('proveedores/habilitados', [ProveedorController::class, 'habilitados']);
    Route::get('proveedores/habilitar/{id}', [ProveedorController::class, 'habilitar']);
    Route::get('sucursales/habilitados', [SucursalController::class, 'habilitados']);
    Route::get('sucursales/habilitar/{id}', [SucursalController::class, 'habilitar']);
    Route::get('clientes/habilitados', [ClienteController::class, 'habilitados']);
    Route::get('clientes/habilitar/{id}', [ClienteController::class, 'habilitar']);
    Route::get('informaciones/habilitados', [AyudaController::class, 'habilitados']);
    Route::get('informaciones/habilitar/{id}', [AyudaController::class, 'habilitar']);
    Route::post('ventas/proformas/{id}', [ProformaController::class, 'ventaproforma']);
    Route::post('ventas/webproformas', [ProformaController::class, 'ventawebproforma']);
    Route::delete('ingresos/detalles/{id}', [IngresoController::class, 'eliminardetalle']);
    Route::get('lotes/habilitados', [\App\Http\Controllers\LoteController::class, 'habilitados']);
    Route::get('blogs/tipos', [BlogController::class, 'tipos']);
    Route::get('videos/habilitados', [VideoController::class, 'habilitados']);
    Route::get('videos/habilitar/{id}', [VideoController::class, 'habilitar']);
    Route::get('blogs/habilitados', [BlogController::class, 'habilitados']);
    Route::get('blogs/habilitar/{id}', [BlogController::class, 'habilitar']);
    Route::get('tipoclientes/habilitados', [TipoClienteController::class, 'habilitados']);
    Route::get('tipoclientes/habilitar/{id}', [TipoClienteController::class, 'habilitar']);

    Route::apiResources([
        'users' => UserController::class,
        'banners' => BannerController::class,
        'tipos' => TipoCategoriaController::class,
        'categorias' => CategoriaController::class,
        'subcategorias' => SubCategoriaController::class,
        'unidades' => UnidadController::class,
        'marcas' => MarcaController::class,
        'productos' => ProductoController::class,
        'proveedores' => ProveedorController::class,
        'sucursales' => SucursalController::class,
        'ingresos' => IngresoController::class,
        'clientes' => ClienteController::class,
        'proformas' => ProformaController::class,
        'ventas' => VentaController::class,
        'informaciones' => AyudaController::class,
        'blogs' => BlogController::class,
        'videos' => VideoController::class,
        'configuracion' => ConfigController::class,
        'tipoclientes' => TipoClienteController::class,
        'accesos' => AccesoController::class
    ]);
});
