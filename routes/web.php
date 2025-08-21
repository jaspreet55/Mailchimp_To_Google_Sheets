<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailchimpWebhookController;
use App\Http\Controllers\MailchimpContactController;
Route::get('/', function () {
    return view('mailchimp.add');
});

Route::post('/mailchimp/add', [MailchimpContactController::class, 'add'])->name('mailchimp.store');
Route::get('/mailchimp/list', [MailchimpContactController::class, 'list'])->name('mailchimp.list');

Route::match(['get', 'post'],'/mailchimp/webhook/{token}', [MailchimpWebhookController::class, 'handle'])
    ->name('mailchimp.webhook');