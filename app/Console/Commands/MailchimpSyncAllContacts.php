<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MailchimpService;
use App\Jobs\ProcessMailchimpContact;

class MailchimpSyncAllContacts extends Command
{
    protected $signature = 'mailchimp:sync-all {--chunk=1000 : Number of contacts to request per page}';
    protected $description = 'Sync all existing Mailchimp contacts to database and Google Sheets using jobs';

    public function handle(MailchimpService $mc): int
    {
        $chunk = (int) $this->option('chunk');
        $count = 0;

        $this->info("Starting historical sync (chunk size: {$chunk})...");
        $this->info('Dispatching jobs for each contact. Run: php artisan queue:work to process them.');

        foreach ($mc->allMembers($chunk) as $member) {
            if (empty($member['email'])) {
                continue;
            }

            // Dispatch one job per contact (idempotent)
            dispatch(new ProcessMailchimpContact($member['email'], 'historical'));
            $count++;
        }

        $this->info("Dispatched {$count} contacts to the queue.");
        return self::SUCCESS;
    }
}
