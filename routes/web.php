<?php

use Icodestuff\Mailytics\Http\Controllers\MailyticsController;
use Illuminate\Support\Facades\Route;

Route::get('mailytics/{imageSignature}', [MailyticsController::class, 'showImageSignature'])->name('mailytics.signature');
Route::get('mailytics', [MailyticsController::class, 'dashboard'])->name('mailytics.dashboard');
