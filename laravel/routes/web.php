<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\QuotationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SettingsController;

Route::get('/', fn() => redirect()->route('quotations.index'));
Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
Route::resource('quotations', QuotationController::class);
Route::resource('customers', CustomerController::class);
Route::resource('plans', PlanController::class);
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
