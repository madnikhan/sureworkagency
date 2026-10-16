<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\CommerceController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\OperationsController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::post('/leads/{lead}/status', [LeadController::class, 'status'])->name('leads.status');
    Route::post('/leads/{lead}/notes', [LeadController::class, 'note'])->name('leads.note');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects/{project}/status', [ProjectController::class, 'status'])->name('projects.status');
    Route::post('/projects/{project}/tasks', [ProjectController::class, 'task'])->name('projects.tasks');
    Route::post('/projects/{project}/milestones', [ProjectController::class, 'milestone'])->name('projects.milestones');
    Route::post('/projects/{project}/members', [ProjectController::class, 'member'])->name('projects.members');

    Route::get('/quotes', [CommerceController::class, 'quotes'])->name('quotes.index');
    Route::post('/quotes', [CommerceController::class, 'storeQuote'])->name('quotes.store');
    Route::get('/quotes/{quote}', [CommerceController::class, 'showQuote'])->name('quotes.show');
    Route::post('/quotes/{quote}/status', [CommerceController::class, 'quoteStatus'])->name('quotes.status');
    Route::get('/quotes/{quote}/pdf', [CommerceController::class, 'quotePdf'])->name('quotes.pdf');

    Route::get('/invoices', [CommerceController::class, 'invoices'])->name('invoices.index');
    Route::post('/invoices', [CommerceController::class, 'storeInvoice'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [CommerceController::class, 'showInvoice'])->name('invoices.show');
    Route::post('/invoices/{invoice}/status', [CommerceController::class, 'invoiceStatus'])->name('invoices.status');
    Route::post('/invoices/{invoice}/payments', [CommerceController::class, 'payment'])->name('invoices.payments');
    Route::get('/invoices/{invoice}/pdf', [CommerceController::class, 'invoicePdf'])->name('invoices.pdf');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');

    Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing.index');
    Route::post('/marketing/campaigns', [MarketingController::class, 'campaign'])->name('marketing.campaigns');
    Route::post('/marketing/seo', [MarketingController::class, 'seo'])->name('marketing.seo');
    Route::post('/marketing/calendar', [MarketingController::class, 'calendar'])->name('marketing.calendar');
    Route::post('/marketing/gbp', [MarketingController::class, 'gbp'])->name('marketing.gbp');
    Route::post('/marketing/utm', [MarketingController::class, 'utm'])->name('marketing.utm');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/messages', [TicketController::class, 'message'])->name('tickets.messages');
    Route::post('/tickets/{ticket}/status', [TicketController::class, 'status'])->name('tickets.status');

    Route::get('/services', [OperationsController::class, 'services'])->name('services.index');
    Route::put('/services/{service}', [OperationsController::class, 'updateService'])->name('services.update');
    Route::get('/files', [OperationsController::class, 'files'])->name('files.index');
    Route::post('/files', [OperationsController::class, 'upload'])->name('files.store');
    Route::get('/files/{file}', [OperationsController::class, 'download'])->name('files.download');
    Route::get('/insights', [OperationsController::class, 'insights'])->name('insights.index');
    Route::post('/insights', [OperationsController::class, 'saveInsight'])->name('insights.store');
    Route::get('/users', [OperationsController::class, 'users'])->name('users.index');
    Route::post('/users', [OperationsController::class, 'storeUser'])->name('users.store');
    Route::get('/settings', [OperationsController::class, 'settings'])->name('settings.edit');
    Route::put('/settings', [OperationsController::class, 'updateSettings'])->name('settings.update');
    Route::get('/audit', [OperationsController::class, 'audit'])->name('audit.index');
});
