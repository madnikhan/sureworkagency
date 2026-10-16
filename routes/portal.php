<?php

use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/portal', PortalController::class)->name('portal');
