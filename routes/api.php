<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

// Rota que o Asaas vai chamar https://beta.liberi-ead.com/api/webhook/asaas
Route::post('/webhook/asaas', [WebhookController::class, 'handle']);
