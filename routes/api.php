<?php

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
 * @authenticated
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function (Request $request) {
    return "123";
});

Route::post('/tokens/create', function (\App\Http\Requests\TokenRequest $request) {
    $data = $request->validated();
    $user = \App\Models\User::where("name", $data['login'])->first();

    if (!$user || !Facades\Hash::check($request->password, $user->password)) {
        return ['token' => false];
    }

    return response()->json(["token"=> $user->createToken($user->email)->plainTextToken]);

});
