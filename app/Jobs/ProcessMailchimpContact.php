<?php

namespace App\Jobs;

use App\Models\MailchimpContact;
use App\Services\MailchimpService;
use App\Services\GoogleSheetsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessMailchimpContact implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 30; // seconds between retries

    public function __construct(
        public string $email,
        public string $source = 'webhook',
        public ?array $webhookData = null
    ) {}

    /** Prevent racing on same email if many webhooks arrive */
    public function middleware(): array
    {
        return [new WithoutOverlapping('mc:'.$this->email)];
    }

    public function handle(MailchimpService $mc, GoogleSheetsService $sheets): void
    {
        // Try to get the contact from database
        $row = MailchimpContact::where('email', strtolower($this->email))->first();

        $info = $mc->getMemberByEmail($this->email);
        if ($row == null) {
            // Fallback to Mailchimp API if not in local DB

            if (!$info) {
                Log::warning('Contact not found in DB or Mailchimp', ['email' => $this->email]);
                return;
            }

            // Create only if missing
            $row = MailchimpContact::create([
                'email' => strtolower($info['email']),
                'first_name' => $info['first_name'] ?? '',
                'last_name' => $info['last_name'] ?? '',
                'signup_date' => $info['signup_date'] ?? now(),
                'tags' => $info['tags'] ?? [],
                'last_synced_at' => now(),
            ]);
        }
        $email = $row->email ?? strtolower($info['email']);
        $firstName = $row->first_name ?? $info['first_name'] ?? '';
        $lastName = $row->last_name ?? $info['last_name'] ?? '';
        $signupDate = ($row->signup_date ?? now())->toDateTimeString();
        $tags = $row->tags ?? $info['tags'] ?? [];
        try {
            // Upsert Google Sheet
            $sheets->upsertContact([
                'email'       => $email,
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'signup_date' => $signupDate,
                'tags'        => $tags,
            ]);
        } catch (\Throwable $e) {
            Log::error('Google Sheets sync failed', ['email' => $email, 'error' => $e->getMessage()]);
            throw $e; // to retry
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error('ProcessMailchimpContact failed', [
            'email'  => $this->email,
            'source' => $this->source,
            'error'  => $e->getMessage(),
        ]);
    }
}
