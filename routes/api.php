<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'seamless'
], function () {
    Route::post('cancel', function() {
        \Log::info('Seamless API - Cancel Request', [
            'request' => request()->all(),
            'timestamp' => now()
        ]);
        
        $response = [
            'code' => 0,
            'data' => [
                'balance' => 1000,
            ]
        ];
        
        \Log::info('Seamless API - Cancel Response', [
            'response' => $response,
            'timestamp' => now()
        ]);
        
        return response()->json($response);
    });

    Route::get('getBalance', function() {
        \Log::info('Seamless API - GetBalance Request', [
            'request' => request()->all(),
            'timestamp' => now()
        ]);
        
        $response = [
            'code' => 0,
            'data' => [
                'balance' => 1000,
            ]
        ];
        
        \Log::info('Seamless API - GetBalance Response', [
            'response' => $response,
            'timestamp' => now()
        ]);
        
        return response()->json($response);
    });

    Route::post('bet', function() {
        \Log::info('Seamless API - Bet Request', [
            'request' => request()->all(),
            'timestamp' => now()
        ]);
        
        $response = [
            'code' => 0,
            'data' => [
                'balance' => 1000,
            ]
        ];
        
        \Log::info('Seamless API - Bet Response', [
            'response' => $response,
            'timestamp' => now()
        ]);
        
        return response()->json($response);
    });

    Route::post('settlement', function() {
        \Log::info('Seamless API - Settlement Request', [
            'request' => request()->all(),
            'timestamp' => now()
        ]);
        
        $response = [
            'code' => 0,
            'data' => [
                'balance' => 1000,
            ]
        ];
        
        \Log::info('Seamless API - Settlement Response', [
            'response' => $response,
            'timestamp' => now()
        ]);
        
        return response()->json($response);
    });
});