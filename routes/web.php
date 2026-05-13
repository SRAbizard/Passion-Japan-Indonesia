<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('about',   fn () => view('pages.about'))->name('about');
Route::get('contact', [ContactController::class, 'show'])->name('contact');
Route::post('contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('blog',          [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{slug}',   [BlogController::class, 'show'])->name('blog.show');
Route::get('event',         [EventController::class, 'index'])->name('event.index');
Route::get('event/{slug}',  [EventController::class, 'show'])->name('event.show');

Route::get('jobs',                [\App\Http\Controllers\JobController::class, 'index'])->name('job.index');
Route::get('jobs/{slug}',         [\App\Http\Controllers\JobController::class, 'show'])->name('job.show');
Route::post('jobs/{slug}/apply',  [\App\Http\Controllers\JobController::class, 'apply'])
    ->middleware('throttle:10,1')
    ->name('job.apply');

Route::get('locale/{locale}', [LocaleController::class, 'switch'])
    ->name('locale.switch')
    ->whereIn('locale', ['id', 'en', 'ja']);

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
