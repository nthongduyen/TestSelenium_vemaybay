<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BaiVietController; // Import Controller

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Định nghĩa các route API cho Bài Viết
// Sử dụng apiResource để định nghĩa các route CRUD chuẩn:
// POST   /api/bai-viet       -> store
// GET    /api/bai-viet       -> index (Dùng cho tìm kiếm)
// PUT    /api/bai-viet/{id}  -> update
// DELETE /api/bai-viet/{id}  -> destroy
Route::apiResource('bai-viet', BaiVietController::class);
