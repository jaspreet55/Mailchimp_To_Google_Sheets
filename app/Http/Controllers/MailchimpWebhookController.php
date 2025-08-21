<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\ProcessMailchimpContact;

class MailchimpWebhookController extends Controller
{
    public function handle(string $token, Request $request)
    {
        Log::info('Webhook hit', ['payload' => $request->all()]);
        if ($token !== config('services.mailchimp.webhook_token')) {
            abort(403, 'Invalid token');
        }
        // If it's a GET request (Mailchimp verification), just return OK
        if ($request->isMethod('get')) {
            return response()->json(['ok' => true]);
        }
        // Mailchimp sends application/x-www-form-urlencoded
        $type = $request->input('type');               // e.g. 'subscribe', 'profile', etc.
        $listId = $request->input('data.list_id') ?? $request->input('list_id');
        $email  = $request->input('data.email') 
        ?? $request->input('data.merges.EMAIL') 
        ?? $request->input('email');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Webhook missing/invalid email', ['payload' => $request->all()]);
            return response()->json(['ok' => true]); // Don't expose details to caller
        }

        if ($listId !== config('services.mailchimp.list_id')) {
            Log::warning('Webhook list mismatch', ['expected' => config('services.mailchimp.list_id'), 'got' => $listId]);
            return response()->json(['ok' => true]);
        }

        // We handle: subscribe (new), profile (updated), upemail (changed email)
        if (! in_array(Str::lower($type), ['subscribe','profile','upemail'], true)) {
            Log::info('Ignoring webhook type', ['type' => $type]);
            return response()->json(['ok' => true]);
        }

        // Queue the processing (fetch full contact via API, upsert DB + Sheet)
        // ProcessMailchimpContact::dispatch($email, source: 'webhook');
        dispatch(new ProcessMailchimpContact($email, source: 'webhook', webhookData: $request->all())); // @phpstan-ignore-line (
        return response()->json(['queued' => true]);
    }
}
