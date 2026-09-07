<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

// المصادقة والتسجيل
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// التصنيفات والوسوم
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/tags', [TagController::class, 'index']);

// قائمة المقالات والتعليقات
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

// ==========================================
// المسارات المحمية
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'me']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/change-password', [ProfileController::class, 'changePassword']);
    });

    // 1. مسار سلة المهملات
    Route::get('/posts/trashed', [PostController::class, 'trashed']);

    // 2. مسارات الاستعادة والحذف النهائي
    Route::patch('/posts/{post}/restore', [PostController::class, 'restore'])->withTrashed();
    Route::delete('/posts/{post}/force-delete', [PostController::class, 'forceDelete'])->withTrashed();

    // 3. إنشاء وتعديل وحذف المقالات
    Route::post('/posts', [PostController::class, 'store']);
    Route::match(['put', 'post'], '/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // التعليقات
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // مسارات المشرف
    Route::middleware('can:admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        Route::post('/tags', [TagController::class, 'store']);

        Route::patch('/comments/{comment}/moderate', [CommentController::class, 'moderate']);
    });
});

// ==========================================
// مسار جلب المقال  Slug
// ==========================================
Route::get('/posts/{post:slug}', [PostController::class, 'show']);
