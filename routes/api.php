<?php

use App\Http\Middleware\CheckHostIsSameHost;
use App\Http\Middleware\CheckQueryHeaders;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')
    ->get('/user', function (Request $request) {
        return $request->user();
    });

Route::post('/web', 'Api\QueryController@index')
    ->middleware(CheckHostIsSameHost::class);

Route::post('/', 'Api\QueryController@index')
    ->middleware('auth:api', CheckQueryHeaders::class);
