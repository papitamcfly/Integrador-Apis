<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\cineController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\FuncionController;
use App\Http\Controllers\BoletoController;
use App\Http\Controllers\cartController;
use App\Http\Controllers\CombosController;
use App\Http\Controllers\GenerosController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PeliculasController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\SSEController;
use App\Http\Controllers\usuarioscontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\IngresosController;
use App\Http\Controllers\MesaController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user(); 
});
Route::get('/sse', [SSEController::class ,'sendSSE']);

Route::group([

    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'

], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('mandarcorreo', [AuthController::class, 'mandarcorreo']);
    Route::post('verify-code', 'AuthController@verifyCode')->name('verifyCode');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::get ('/activate/{token}', [AuthController::class ,'activate'])->name('activate');
});


Route::middleware(['auth:api',RoleMiddleware::class . ':2,3'])->group(function () {
    Route::post('/generos', [GenerosController::class, 'store'])->name('creategeneros');
    Route::get('/generos/{genero}', [GenerosController::class, 'show'])->where('genero', '[0-9]+')->name('showgeneros');;
    Route::put('/generos/{genero}', [GenerosController::class, 'update'])->where('genero', '[0-9]+')->name('updategeneros');;
    Route::delete('/generos/{genero}', [GenerosController::class, 'destroy'])->where('genero', '[0-9]+')->name('deletegeneros');;
});

Route::middleware(['auth:api', RoleMiddleware::class . ':1,2,3'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{estado}', [OrderController::class, 'index'])->name('allordenes');
    Route::put('/orders/{id}/{estado}', [OrderController::class, 'changestatus'])->where('id', '[0-9]+')->where('estado', '[a-zA-Z]+');
    Route::post('/orders/sendClient', [OrderController::class, 'mailClient'])->name('sendClient');
    Route::get('/generos', [GenerosController::class, 'index'])->name('allgeneros');
});

Route::middleware(['auth:api',RoleMiddleware::class . ':3'])->group(function () {
    Route::get('/roles',[usuarioscontroller::class,'showroles']);
    Route::get('/logs',[usuarioscontroller::class,'logs']);
    Route::post('activateUser/{id}', [usuarioscontroller::class, 'activateUser'])->where('id', '[0-9]+')->name('activateUser');
    Route::post('deactivateUser/{id}', [usuarioscontroller::class, 'deactivateUser'])->where('id', '[0-9]+')->name('deactivateUser');
});

Route::middleware(['auth:api',RoleMiddleware::class . ':3'])->group(function () {;
});
Route::post('/post', [PostController::class, 'store']);

Route::middleware(['auth:api',RoleMiddleware::class . ':2,3'])->group(function (){
    Route::get('/products/{estado}', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show'])->where('id', '[0-9]+');
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->where('id', '[0-9]+');
    Route::delete('/products/{id}/{estado}', [ProductController::class, 'changestatus'])->where('id', '[0-9]+');

});
Route::middleware(['auth:api',RoleMiddleware::class . ':2,3'])->group(function (){
    Route::get('/ingresos/{type}', [IngresosController::class, 'getIngresos']);
    Route::get('/ingresos/productos/{type}', [IngresosController::class, 'getIngresosProductos']);
});
Route::middleware(['auth:api', RoleMiddleware::class . ':2,3'])->group(function () {
    Route::get('/mesas', [MesaController::class, 'index'])->name('mesas.index');
    Route::post('/mesas', [MesaController::class, 'store'])->name('mesas.store');
    Route::get('/mesas/{mesa}', [MesaController::class, 'show'])->name('mesas.show')->where('mesa', '[0-9]+');
    Route::put('/mesas/{mesa}', [MesaController::class, 'update'])->name('mesas.update')->where('mesa', '[0-9]+');
    Route::delete('/mesas/{mesa}', [MesaController::class, 'destroy'])->name('mesas.destroy')->where('mesa', '[0-9]+');
});
Route::middleware(['auth:api', RoleMiddleware::class . ':2,3'])->group(function () {
Route::get('/usuarios', [usuarioscontroller::class, 'index'])->name('allusuarios');
Route::post('/usuarios', [usuarioscontroller::class, 'store'])->name('createusuarios');
Route::get('/usuarios/{combo}', [usuarioscontroller::class, 'show'])->where('combo', '[0-9]+')->name('showusuarios');
Route::put('/usuarios/{combo}', [usuarioscontroller::class, 'update'])->where('combo', '[0-9]+')->name('updateusuarios');
Route::delete('/usuarios/{combo}', [usuarioscontroller::class, 'destroy'])->where('combo', '[0-9]+')->name('deleteusuarios');
});