<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// POST /countries/refresh → Fetch all countries and exchange rates, then cache them in the database
Route::post('/countries/refresh', [CountryController::class, 'store']);

// GET /countries → Get all countries from the DB (support filters and sorting) - ?region=Africa | ?currency=NGN | ?sort=gdp_desc
Route::get('/countries', [CountryController::class, 'index']);

// GET /countries/:name → Get one country by name
// Route::get('/countries/{country:name}', [CountryController::class, 'show']);
Route::get('/countries/{country}', [CountryController::class, 'show']);


// DELETE /countries/:name → Delete a country record
Route::delete('/countries/{country:name}', [CountryController::class, 'destroy']);

// GET /status → Show total countries and last refresh timestamp
Route::get('/status', [CountryController::class, 'status']);

// GET /countries/image → serve summary image
Route::get('/countries/image ', [CountryController::class, 'image']);
    