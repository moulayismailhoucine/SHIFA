<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\FraudAttemptController;
use App\Http\Controllers\GoogleAIController;
use App\Http\Controllers\OpenAIController;

Route::get('/', fn() => redirect('/login'));
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/public-booking', fn() => view('appointments.public-booking'));
Route::get('/test-google-ai', [OpenAIController::class, 'testGoogleAI']);
Route::get('/lang/{lang}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,1')->name('contact.send');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Nurse management routes
    Route::get('/nurses/interface', [App\Http\Controllers\Admin\NurseController::class, 'interface'])->name('nurses.interface');
    Route::get('/nurses', [App\Http\Controllers\Admin\NurseController::class, 'index'])->name('nurses.index');
    Route::get('/nurses/create', [App\Http\Controllers\Admin\NurseController::class, 'create'])->name('nurses.create');
    Route::post('/nurses', [App\Http\Controllers\Admin\NurseController::class, 'store'])->name('nurses.store');
    Route::get('/nurses/{nurse}', [App\Http\Controllers\Admin\NurseController::class, 'show'])->name('nurses.show');
    Route::get('/nurses/{nurse}/edit', [App\Http\Controllers\Admin\NurseController::class, 'edit'])->name('nurses.edit');
    Route::put('/nurses/{nurse}', [App\Http\Controllers\Admin\NurseController::class, 'update'])->name('nurses.update');
    Route::delete('/nurses/{nurse}', [App\Http\Controllers\Admin\NurseController::class, 'destroy'])->name('nurses.destroy');
    
    // Contact messages routes
    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('/contact-messages/{id}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::post('/contact-messages/{id}/mark-read', [ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-read');
    Route::post('/contact-messages/{id}/mark-unread', [ContactMessageController::class, 'markAsUnread'])->name('contact-messages.mark-unread');
    Route::delete('/contact-messages/{id}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    
    // Fraud attempts routes
    Route::get('/fraud-attempts', [FraudAttemptController::class, 'index'])->name('fraud-attempts.index');
    Route::get('/fraud-attempts/{id}', [FraudAttemptController::class, 'show'])->name('fraud-attempts.show');
    Route::delete('/fraud-attempts/{id}', [FraudAttemptController::class, 'destroy'])->name('fraud-attempts.destroy');
});

// Staff / portal view routes — no auth middleware needed here.
// Real security is enforced by the API (Sanctum token) that each page calls.
// If JS has no valid token, the API returns 401 and the page shows nothing.
Route::get('/dashboard',           fn() => view('dashboard'))->name('dashboard');
Route::get('/patients',            fn() => view('patients.index'));
Route::get('/doctors',             fn() => view('doctors.index'));
Route::get('/medical-records',     fn() => view('medical-records.index'));
Route::get('/ordonnances',         fn() => view('ordonnances.index'));
Route::get('/pharmacies',          fn() => view('pharmacies.index'));
Route::get('/laboratories',        fn() => view('laboratories.index'));
Route::get('/appointments',        fn() => view('appointments.index'));
Route::get('/patient-profile',     fn() => view('patient.profile'));
Route::get('/doctor-patient-view', fn() => view('doctor.patient-view'));
Route::get('/pharmacy-dashboard',  fn() => view('pharmacy.dashboard'));
Route::get('/lab-dashboard',       fn() => view('lab.dashboard'));
Route::get('/medical-chat',        [GoogleAIController::class, 'index'])->name('medical-chat.index');
Route::get('/doctor/nursing-tasks', fn() => view('nurse.doctor-tasks'))->name('doctor.nursing-tasks');
Route::get('/doctor/ai-diagnosis', [App\Http\Controllers\Doctor\AiDiagnosisController::class, 'index'])->name('doctor.ai-diagnosis');

// Web interface for Skin Cancer AI testing
Route::get('/skin-cancer-test', [\App\Http\Controllers\SkinCancerWebController::class, 'index'])->name('skin-cancer.test');
Route::post('/skin-cancer-test', [\App\Http\Controllers\SkinCancerWebController::class, 'analyze'])->name('skin-cancer.analyze');

// Web interface for X-Ray Fracture Detection
Route::get('/fracture-analysis', [\App\Http\Controllers\FractureAnalysisController::class, 'index'])->name('fracture.index');
Route::post('/fracture-analysis', [\App\Http\Controllers\FractureAnalysisController::class, 'analyze'])->name('fracture.analyze');

// AI Medication History Summarizer
Route::get('/medication-summary', [\App\Http\Controllers\MedicationSummaryController::class, 'index'])->name('medication-summary.index');
Route::post('/medication-summary', [\App\Http\Controllers\MedicationSummaryController::class, 'generate'])->name('medication-summary.generate');
Route::post('/medication-summary/api', [\App\Http\Controllers\MedicationSummaryController::class, 'generateJson'])->name('medication-summary.api');


// Nurse portal routes
Route::middleware(['auth', 'nurse'])->prefix('nurse')->name('nurse.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\NurseController::class, 'dashboard'])->name('dashboard');
    Route::get('/patients', [App\Http\Controllers\NurseController::class, 'patients'])->name('patients');
    Route::get('/orders', [App\Http\Controllers\NurseController::class, 'orders'])->name('orders');
    Route::post('/vitals/{patient}', [App\Http\Controllers\NurseController::class, 'storeVitals'])->name('vitals.store');
    Route::post('/notes/{patient}', [App\Http\Controllers\NurseController::class, 'storeNote'])->name('notes.store');
});

// Vitals routes
Route::middleware(['auth'])->group(function () {
    Route::get('/vitals/create/{patient}', [App\Http\Controllers\VitalController::class, 'create'])->name('vitals.create');
    Route::post('/vitals/{patient}', [App\Http\Controllers\VitalController::class, 'store'])->name('vitals.store');
    Route::get('/vitals/{patient}', [App\Http\Controllers\VitalController::class, 'index'])->name('vitals.index');
    Route::get('/patients/{patient}', [App\Http\Controllers\VitalController::class, 'showPatient'])->name('patients.show');
});

// Nurse Notes routes
Route::middleware(['auth'])->group(function () {
    Route::post('/nurse-notes/{patient}', [App\Http\Controllers\NurseNoteController::class, 'store'])->name('nurse-notes.store');
    Route::get('/nurse-notes/{patient}', [App\Http\Controllers\NurseNoteController::class, 'index'])->name('nurse-notes.index');
});

// Alerts API
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/alerts/recent', [App\Http\Controllers\AlertController::class, 'recent'])->name('api.alerts.recent');
    Route::post('/alerts/{alert}/read', [App\Http\Controllers\AlertController::class, 'markRead'])->name('api.alerts.read');
});


Route::get('/logout', function () {
    // Clear any PHP web session (stale sessions from old Auth::login() calls)
    if (request()->hasSession()) {
        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect('/login');
});
