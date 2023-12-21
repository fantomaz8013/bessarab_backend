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

/**
 * Вебхук для телеграм бота
 */
Route::post('/webhook', function (Request $request) {
    $data = $request->all();
    $chatId = $data['message']['chat']['id'];
    $text = $data['message']['chat'];

    if ($text == '/start')
    {
        $name = $data['from']['first_name'] . " " . $data['from']['last_name'];
        $text = "Привет $name. Теперь вы будете получать заказы с сайта";
        $data = http_build_query([
            'chat_id' => $chatId,
            'text' => $text
        ]);
        file_get_contents("https://api.telegram.org/bot6720731238:AAGcZ4QSSFRVWYrL8BzuRbGYiMRoWQR8oAA/sendMessage?$data");
    }

    return response('Ok', 200);
});

Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index']);
Route::get('/products/{Product}', [\App\Http\Controllers\ProductController::class, 'show']);
Route::get('/categories', [\App\Http\Controllers\ProductCategoryController::class, 'index']);
Route::post('/categories', [\App\Http\Controllers\ProductCategoryController::class, 'store']);
Route::put('/categories/{ProductCategory}', [\App\Http\Controllers\ProductCategoryController::class, 'update']);

Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index']);
Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store']);
Route::put('/orders/{Order}/status', [\App\Http\Controllers\OrderController::class, 'status']);



Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store']);

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
