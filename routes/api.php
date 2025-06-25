<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Helpers for simple balance persistence using SQLite located at database/balance.sqlite
if (!function_exists('balance_pdo')) {
    /**
     * Get a PDO instance connected to the SQLite file that stores balance.
     */
    function balance_pdo(): \PDO
    {
        $path = database_path('balance.sqlite');

        // Ensure the sqlite file exists
        if (!file_exists($path)) {
            touch($path);
        }

        $pdo = new \PDO('sqlite:' . $path, null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);

        // Prepare table and initial record
        $pdo->exec('CREATE TABLE IF NOT EXISTS balances (id INTEGER PRIMARY KEY, balance INTEGER NOT NULL)');
        $pdo->exec('INSERT OR IGNORE INTO balances (id, balance) VALUES (1, 1000000)');

        return $pdo;
    }
}

if (!function_exists('current_balance')) {
    /**
     * Retrieve the current balance value.
     */
    function current_balance(): int
    {
        $pdo = balance_pdo();
        return (int) $pdo->query('SELECT balance FROM balances WHERE id = 1')->fetchColumn();
    }
}

if (!function_exists('set_balance')) {
    /**
     * Persist a new balance value.
     */
    function set_balance(int $amount): void
    {
        $pdo = balance_pdo();
        $stmt = $pdo->prepare('UPDATE balances SET balance = ? WHERE id = 1');
        $stmt->execute([$amount]);
    }
}

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

        $balance = current_balance();

        $response = [
            'code' => 0,
            'data' => [
                'balance' => $balance,
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

        $balance = current_balance();

        $response = [
            'code' => 0,
            'data' => [
                'balance' => $balance,
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

        $amount = (int) request('amount', 0);
        $balance = current_balance();

        if ($amount <= 0 || $amount > $balance) {
            $response = [
                'code' => 1, // error code for insufficient balance or invalid amount
                'message' => 'Invalid bet amount or insufficient balance',
                'data' => [
                    'balance' => $balance,
                ]
            ];
        } else {
            $newBalance = $balance - $amount;
            set_balance($newBalance);
            $response = [
                'code' => 0,
                'data' => [
                    'balance' => $newBalance,
                ]
            ];
        }

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

        $amount = (int) request('amount', 0);
        $balance = current_balance();

        if ($amount <= 0) {
            $response = [
                'code' => 1,
                'message' => 'Invalid settlement amount',
                'data' => [
                    'balance' => $balance,
                ]
            ];
        } else {
            $newBalance = $balance + $amount;
            set_balance($newBalance);
            $response = [
                'code' => 0,
                'data' => [
                    'balance' => $newBalance,
                ]
            ];
        }

        \Log::info('Seamless API - Settlement Response', [
            'response' => $response,
            'timestamp' => now()
        ]);

        return response()->json($response);
    });
});

Route::any('/recorder', function (\Illuminate\Http\Request $request) {
    \Storage::append('requests.log', json_encode([
        'method' => $request->method(),
        'uri' => $request->getRequestUri(),
        'headers' => $request->headers->all(),
        'body' => $request->getContent(),
        'time' => now()->toIso8601String(),
    ], JSON_PRETTY_PRINT));
    return 'Request recorded.';
});