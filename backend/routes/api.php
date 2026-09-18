<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Api\ProspectController;
use App\Http\Controllers\Api\ProspectContactController;
use App\Http\Controllers\Api\ProspectOpportunityController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ProspectFollowupController;
use App\Http\Controllers\Api\FollowupController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProspectIntegrationController;
use App\Http\Controllers\Api\ProspectImportItemController;

// Routes
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json([
        'data' => $request->user(),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    // Prospectos
    Route::get('/prospects', [ProspectController::class, 'index']);
    Route::post('/prospects', [ProspectController::class, 'store']);
    Route::get('/prospects/trash', [ProspectController::class, 'trash']);
    Route::patch('/prospects/{id}/restore', [ProspectController::class, 'restore']);
    Route::get('/prospects/{prospect}', [ProspectController::class, 'show']);
    Route::patch('/prospects/{prospect}', [ProspectController::class, 'update']);
    Route::delete('/prospects/{prospect}', [ProspectController::class, 'destroy']);

    // Contactos
    Route::get('/prospects/{prospect}/contacts',[ProspectContactController::class, 'index']);
    Route::post('/prospects/{prospect}/contacts',[ProspectContactController::class, 'store']);
    Route::patch('/prospects/{prospect}/contacts/{contactId}',[ProspectContactController::class, 'update']);
    Route::delete('/prospects/{prospect}/contacts/{contactId}',[ProspectContactController::class, 'destroy']);

    // Oportunidades
    Route::get('/prospects/{prospect}/opportunities',[ProspectOpportunityController::class, 'index']);
    Route::post('/prospects/{prospect}/opportunities',[ProspectOpportunityController::class, 'store']);
    Route::patch('/prospects/{prospect}/opportunities/{opportunityId}',[ProspectOpportunityController::class, 'update']);
    Route::delete('/prospects/{prospect}/opportunities/{opportunityId}',[ProspectOpportunityController::class, 'destroy']);

    // Servicios
    Route::get('/services',[ServiceController::class, 'index']);

    // Seguimientos
    Route::get('/prospects/{prospect}/followups',[ProspectFollowupController::class, 'index']);
    Route::post('/prospects/{prospect}/followups',[ProspectFollowupController::class, 'store']);
    Route::patch('/prospects/{prospect}/followups/{followupId}',[ProspectFollowupController::class, 'update']);
    Route::delete('/prospects/{prospect}/followups/{followupId}',[ProspectFollowupController::class, 'destroy']);

    // Seguimientos de hoy
    Route::get('/followups/today',[FollowupController::class, 'today']);
    Route::get('/followups/overdue',[FollowupController::class, 'overdue']);
    Route::get('/followups/upcoming',[FollowupController::class, 'upcoming']);

    // Dashboard
    Route::get('/dashboard',[DashboardController::class, 'index']);

    // Importación
    Route::get('/prospect-import-items/pending',[ProspectImportItemController::class,'pending']);
    Route::post('/prospect-import-items/{prospectImportItem}/approve',[ProspectImportItemController::class,'approve']);
    Route::post('/prospect-import-items/{prospectImportItem}/reject',[ProspectImportItemController::class,'reject']
);

});

Route::prefix('integrations')
     ->middleware([
        'integration.token',
        'throttle:30,1',
    ])
    ->group(function () {

    Route::post(
        '/prospects',
        [
            ProspectIntegrationController::class,
            'store',
        ]
    );
});
