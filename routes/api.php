<?php

use App\Http\Controllers\FormationController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
 Route::get('/formations/{formation}/inscriptions-count', [FormationController::class, 'getActiveInscriptionsCount']);

 Route::get('/monthly-revenue', function (Request $request) {
    if ($request->header('X-API-KEY') !== 'S3CR3T_K3Y') {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    // On calcule la somme totale directement ici
    $total = Payment::where('status', 'paid')
        ->whereBetween('paid_date', [$request->query('date_from'), $request->query('date_to')])
        ->sum('amount');

    return response()->json(['total_sum' => $total]);
});



