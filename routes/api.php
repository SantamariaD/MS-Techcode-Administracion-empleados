<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentosController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\AlmacenComprasController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CategoriaCatalogoProductosController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PlantillaDocumentosController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CatalogoProveedorController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\HorariosGeneralesController;
use App\Http\Controllers\ReporteEmpleadoController;
use App\Http\Controllers\NominasController;
use App\Http\Controllers\AreasController;
use App\Http\Controllers\PuestosController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\ProductoOrdenCompraController;
use App\Http\Controllers\ProductosTicketController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\SucursalesController;
use App\Http\Controllers\ProductosAlmacenesController;
use App\Http\Controllers\CatalogoBancoController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CarpetasController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\EmpleadosRolesController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\AlmacenesSucursalesController;
use App\Http\Controllers\AlmacenesProductosEntradasController;
use App\Http\Controllers\AlmacenProductosSalidaController;
use App\Http\Controllers\CaducidadController;
use App\Http\Controllers\CaducidadAlmacenComprasController;
use App\Http\Controllers\TraspasosController;
use App\Http\Controllers\AlmacenComprasEntradasController;
use App\Http\Controllers\AlmacenComprasSalidasController;
use App\Http\Controllers\TraspasoAlmacenesSucursalController;
use App\Http\Controllers\EvaluacionOrdenCompraController;
use App\Http\Controllers\AlmacenesProductosTraspasosEvaluacionController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\AlmacenComprasTraspasoEvaluacionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::group([
    'prefix' => 'empleados',
], function ($router) {
    Route::post('guardar-empleado', [EmpleadoController::class, 'crearEmpleado']);
    Route::post('guardar-empleado-laboral', [EmpleadoController::class, 'crearEmpleadoLaboral']);
    Route::post('actualizar-empleado', [EmpleadoController::class, 'actualizarInformacionGeneralEmpleado']);
    Route::put('actualizar-empleado-laboral', [EmpleadoController::class, 'actualizarInformacionLaboralEmpleado']);
    Route::delete('eliminar-empleado/{id}', [EmpleadoController::class, 'eliminarEmpleado']);
    Route::get('consultar-empleado/{id}/{uuidBD}', [EmpleadoController::class, 'consultarEmpleado']);
    Route::get('traer-fotografia/{uuid}/{extension}/{nombre}', [EmpleadoController::class, 'traerFotografia']);
    Route::get('consultar-empleados', [EmpleadoController::class, 'consultarTodosEmpleados']);
    Route::post('asignar-plantilla-documento', [EmpleadoController::class, 'agregarPlantillaDocumentoEmpleado']);
    Route::post('traer-archivo', [EmpleadoController::class, 'traerArchivo']);
    Route::post('descargar-archivo', [EmpleadoController::class, 'descargarArchivo']);
    Route::get('consultar-plantillas-documentos-empleado/{id}', [EmpleadoController::class, 'consultarPlantillasDocumentosEmpleado']);
    Route::get('traer-documentos-area/{area}', [EmpleadoController::class, 'traerDocumentosArea']);
    Route::post('actualizar-plantilla-documento-empleado', [EmpleadoController::class, 'actualizarPlantillaDocumentoEmpleado']);
    Route::post('desasignar-plantilla-documento-empleado', [EmpleadoController::class, 'desasignarPlantillaDocumento']);
    Route::get('descargar-documento/{uuid}/{extension}/{idEmpleado}', [EmpleadoController::class, 'descargarDocumento']);
    Route::post('guardar-asistencia', [EmpleadoController::class, 'guardarAsistencia']);
    Route::post('actualizar-asistencia', [EmpleadoController::class, 'actualizarAsistencia']);
    Route::get('traer-registro/{id_emp}/{fecha}', [EmpleadoController::class, 'traerRegistroAsistencia']);
});

Route::group([
    'prefix' => 'plantillas',
], function ($router) {
    Route::get('consultar-plantillas', [PlantillaDocumentosController::class, 'consultarPlantillas']);
    Route::post('crear-plantilla', [PlantillaDocumentosController::class, 'crearPlantilla']);
    Route::put('actualizar-plantilla', [PlantillaDocumentosController::class, 'atualizarPlantilla']);
});

Route::group([
    'prefix' => 'horario',
], function ($router) {
    Route::get('consultar-horario/{idEmpleado}', [HorarioController::class, 'consultarHorariosById']);
    Route::get('consultar-horario-fecha/{fecha}/{idEmpleado}', [HorarioController::class, 'consultarHorarioByFecha']);
    Route::get('consultar-horarios', [HorarioController::class, 'consultarTodosHorarios']);
    Route::delete('eliminar-horario/{id}', [HorarioController::class, 'eliminarHorario']);
    Route::post('actualizar-horario', [HorarioController::class, 'actualizarHorario']);
    Route::post('actualizar-varios', [HorarioController::class, 'actualizarVarios']);
    Route::post('crear-horario', [HorarioController::class, 'crearHorario']);
    Route::post('crear-varios', [HorarioController::class, 'crearVarios']);
});


Route::group([
    'prefix' => 'horarios-generales',
], function ($router) {
    Route::get('consultar-horario/{id}', [HorariosGeneralesController::class, 'consultarHorariosById']);
    Route::get('consultar-horarios', [HorariosGeneralesController::class, 'consultarTodosHorarios']);
    Route::delete('eliminar-horario/{id}', [HorariosGeneralesController::class, 'eliminarHorario']);
    Route::post('actualizar-horario', [HorariosGeneralesController::class, 'actualizarHorario']);
    Route::post('crear-horario', [HorariosGeneralesController::class, 'crearHorario']);
});


Route::group([
    'prefix' => 'reportes',
], function ($router) {
    Route::get('consultar-reportes/{idEmpleado}', [ReporteEmpleadoController::class, 'consultarReportesById']);
    Route::get('consultar-reporte-fecha/{fecha}/{idEmpleado}', [ReporteEmpleadoController::class, 'consultarReporteByFecha']);
    Route::get('consultar-reportes', [ReporteEmpleadoController::class, 'consultarTodosReportes']);
    Route::post('consultar-varios-fecha', [ReporteEmpleadoController::class, 'consultarMuchosByFecha']);
    Route::post('consultar-varios-idNomina', [ReporteEmpleadoController::class, 'consultarVariosIdNomina']);
    Route::get('consultar-reportes-idNomina/{idNomina}', [ReporteEmpleadoController::class, 'consultarReportesByIdNomina']);
    Route::delete('eliminar-reporte/{id}', [ReporteEmpleadoController::class, 'eliminarReporte']);
    Route::post('actualizar-reporte', [ReporteEmpleadoController::class, 'actualizarReporte']);
    Route::post('crear-reporte', [ReporteEmpleadoController::class, 'crearReporte']);
});

Route::group([
    'prefix' => 'empleados-roles'
], function ($router) {
    Route::get('consultar-roles', [EmpleadosRolesController::class, 'consultarRoles']);
    Route::get('consultar-empleados-roles', [EmpleadosRolesController::class, 'consultarEmpleadosRoles']);
    Route::get('consultar/{id}', [EmpleadosRolesController::class, 'consultarEmpleadoRoles']);
    Route::post('guardar', [EmpleadosRolesController::class, 'guardarEmpleadosRoles']);
    Route::post('eliminar', [EmpleadosRolesController::class, 'eliminarEmpleadoRoles']);
});