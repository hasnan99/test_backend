<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/api/register',[Controller::class,'api_register']);
Route::post('/api/login',[Controller::class,'api_login']);
Route::post('/api/create_checklist',[Controller::class,'create_checklist']);
Route::delete('/api/delete_checklist/{checklist_id}',[Controller::class,'delete_checklist']);
Route::get('/api/show_checklist',[Controller::class,'show_checklist']);
Route::get('/api/detail_checklist/{detail_id}',[Controller::class,'detail_checklist']);
Route::post('/api/create_item/{checklist_id}/item}',[Controller::class,'create_item']);
Route::get('/api/detail_item/{checklist_id}/item/{item_id}',[Controller::class,'detail_item']);
Route::put('/api/checklist/{checklist_id}/item/rename/{item_id}',[Controller::class,'rename_item']);
Route::put('api/checklist/{checklist_id}/item/{item_id}',[Controller::class,'update_status']);
Route::delete('api/checklist/{checklist_id}/item/{item_id}',[Controller::class,'delete_item']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
