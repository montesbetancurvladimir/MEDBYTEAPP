<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SurveyController;
use App\Http\Controllers\PageController;

/*
Route::get('/', function () {
    return view('inicio');
})->name('home');
*/
Route::get('/', [PageController::class, 'inicio'])->name('home');


Route::get('/nosotros', [PageController::class, 'nosotros'])->name('page.nosotros');
Route::get('/empresas', [PageController::class, 'empresas'])->name('page.empresas');
Route::get('/blog', [PageController::class, 'blog'])->name('page.blog');
Route::get('/prensa', [PageController::class, 'prensa'])->name('page.prensa');
Route::get('/survey/start', [SurveyController::class, 'start'])->name('survey.start');
Route::get('/survey/inicio', [SurveyController::class, 'inicio'])->name('survey.inicio');
Route::post('/survey/answer', [SurveyController::class, 'answer'])->name('survey.answer');
//Guardado de encuesta por cache
Route::post('/survey/answer_cache', [SurveyController::class, 'answer_cache'])->name('survey.answer_cache');

Route::get('/survey/question/{id}', [SurveyController::class, 'question'])->name('survey.question');
Route::get('/complete', [SurveyController::class, 'complete'])->name('survey.complete_individual');

