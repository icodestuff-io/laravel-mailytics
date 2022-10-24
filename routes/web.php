<?php

use Icodestuff\Mailytics\Http\Controllers\MailyticsController;
use Illuminate\Support\Facades\Route;

Route::get('mailytics/{pixel}/viewed', [MailyticsController::class, 'trackView'])->name('mailytics.viewed');
Route::get('mailytics', [MailyticsController::class, 'dashboard'])->name('mailytics.dashboard');
Route::get('mailytics/{pixel}/clicked', [MailyticsController::class, 'trackClick'])->name('mailytics.clicked');
