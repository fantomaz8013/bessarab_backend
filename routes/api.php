<?php

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades;

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

/**
 * Получить данные текущего пользователя
 * @authenticated
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/webhook', [\App\Http\Controllers\TelegramUserController::class, 'webhook']);


Route::get('/telegramuser', [\App\Http\Controllers\TelegramUserController::class, 'index']);
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index']);
Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy']);
Route::get('/products/{Product}', [\App\Http\Controllers\ProductController::class, 'show']);
Route::get('/categories', [\App\Http\Controllers\ProductCategoryController::class, 'index']);
Route::get('/lines', [\App\Http\Controllers\ProductLinesController::class, 'index']);
Route::post('/lines', [\App\Http\Controllers\ProductLinesController::class, 'store']);
Route::put('/lines/{productLines}', [\App\Http\Controllers\ProductLinesController::class, 'update']);
Route::post('/categories', [\App\Http\Controllers\ProductCategoryController::class, 'store']);
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store']);
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'index']);
Route::put('/categories/{ProductCategory}', [\App\Http\Controllers\ProductCategoryController::class, 'update']);

Route::get('/brands', [\App\Http\Controllers\BrandController::class, 'index']);
Route::post('/brands', [\App\Http\Controllers\BrandController::class, 'store']);
Route::put('/brands/{brand}', [\App\Http\Controllers\BrandController::class, 'update']);

Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index']);
Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store']);
Route::put('/orders/{Order}/status', [\App\Http\Controllers\OrderController::class, 'status']);

Route::post('/products/{product}/hide', [\App\Http\Controllers\ProductController::class, 'hide']);
Route::post('/products/{product}/show', [\App\Http\Controllers\ProductController::class, 'showing']);


Route::get('/times', [\App\Http\Controllers\TimeController::class, 'index']);
Route::post('/times', [\App\Http\Controllers\TimeController::class, 'store']);
Route::put('/times/{time}', [\App\Http\Controllers\TimeController::class, 'update']);
Route::delete('/times/{time}', [\App\Http\Controllers\TimeController::class, 'destroy']);

Route::post('/payment/cancel', [\App\Http\Controllers\PaymentController::class, 'cancel']);
Route::post('/payment/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook']);

Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store']);
Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update']);

/**
 * Получить токен доступа ( для админки )
 */
Route::post('/tokens/create', function (\App\Http\Requests\TokenRequest $request) {
    $data = $request->validated();
    $user = \App\Models\User::where("name", $data['login'])->first();

    if (!$user || !Facades\Hash::check($request->password, $user->password)) {
        return ['token' => false];
    }

    return response()->json(["token"=> $user->createToken($user->email)->plainTextToken]);

});
