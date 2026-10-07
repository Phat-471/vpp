<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Webhook endpoints for SePay & Casso
Route::post('/webhook/sepay', [WebhookController::class, 'sepay'])->name('webhook.sepay');
Route::post('/webhook/casso', [WebhookController::class, 'casso'])->name('webhook.casso');
