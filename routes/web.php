<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'API Multi-Gateway Payment System',
        'version' => '1.0.0',
    ]);
});

