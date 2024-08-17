<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ChoiceController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\GradeController;
use App\Http\Controllers\API\LessonController;
use App\Http\Controllers\API\MaterialController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\UserController;
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
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum', 'check.session')->group(function () {
    // for users table
    Route::get('/users', [UserController::class, 'index']); // List all users
    Route::get('/users/{id}', [UserController::class, 'show']); // Show user by ID
    Route::put('/users/{id}', [UserController::class, 'update']); // Update user
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Delete user
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);// Restore user
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete']);// Force delete user
    Route::get('/users/trashed', [UserController::class, 'trash']);// get softdeleted users



    // for categories table
    Route::apiResource('categories', CategoryController::class); // this route give me the CRUD Routes run the php artisan route:list to see the routes
    Route::get('/categories/trashed', [CategoryController::class, 'trash']);
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('categories/{id}/force-delete', [CategoryController::class, 'forceDelete']);

    // for cources table
    Route::apiResource('courses', CourseController::class);// this route give me the CRUD Routes
    Route::get('/courses/trashed', [CourseController::class, 'trash']);
    Route::post('courses/{id}/restore', [CourseController::class, 'restore']);
    Route::delete('courses/{id}/force-delete', [CourseController::class, 'forceDelete']);

        // Lesson routes
        Route::apiResource('courses.lessons', LessonController::class);
        Route::get('/lessons/trashed', [LessonController::class, 'trash']);
        Route::post('lessons/{id}/restore', [LessonController::class, 'restore']);
        Route::delete('lessons/{id}/force-delete', [LessonController::class, 'forceDelete']);
    
        // Material routes
        Route::apiResource('lessons.materials', MaterialController::class);
        Route::get('/materials/trashed', [MaterialController::class, 'trash']);
        Route::post('materials/{id}/restore', [MaterialController::class, 'restore']);
        Route::delete('materials/{id}/force-delete', [MaterialController::class, 'forceDelete']);

        // quize routes (quitions , answers ,responces , results)
        Route::apiResource('quizzes', QuizController::class);
        Route::apiResource('quizzes.questions', QuestionController::class);
        Route::apiResource('questions.choices', ChoiceController::class);
        Route::apiResource('grades', GradeController::class);
});