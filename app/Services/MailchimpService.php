<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;
use Illuminate\Support\Facades\Log;

class MailchimpService
{
    protected ApiClient $client;
    protected string $server;
    protected string $listId;

    public function __construct()
    {
        $apiKey = config('services.mailchimp.api_key');
        $this->server = config('services.mailchimp.server_prefix');
        $this->listId = config('services.mailchimp.list_id');

        $this->client = new ApiClient();
        $this->client->setConfig([
            'apiKey' => $apiKey,
            'server' => $this->server,
        ]);
    }

    /** Return associative array for a member, or null if not found. */
    public function getMemberByEmail(string $email): ?array
    {
        $hash = md5(strtolower($email));
        try {
            $member = $this->client->lists->getListMember($this->listId, $hash);
            $member = json_decode(json_encode($member), true);
             Log::warning('Mailchimp member failed', ['member' => $member]);
            // Normalize
            return [
                'email'       => $member['email_address'] ?? $email,
                'first_name'  => $member['merge_fields']['FNAME'] ?? null,
                'last_name'   => $member['merge_fields']['LNAME'] ?? null,
                'signup_date' => !empty($member['timestamp_signup'])
                                ? $member['timestamp_signup']
                                : ($member['last_changed'] ?? now()->toDateTimeString()),
                'tags'        => array_column($member['tags'] ?? [], 'name'),
            ];
        } catch (\Throwable $e) {
            // 404 if not found; others are errors
            Log::warning('Mailchimp getMemberByEmail failed', ['email' => $email, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Stream all Mailchimp members in pages (generator-based).
     *
     * @param  int  $pageSize  Number of contacts to fetch per API call (default: 1000)
     * @return \Generator<array{
     *     email:string|null,
     *     first_name:string|null,
     *     last_name:string|null,
     *     signup_date:string|null,
     *     tags:array
     * }>
     */
    public function allMembers(int $pageSize = 1000): \Generator
    {
        $offset = 0;
        $backoff = 1; // exponential backoff for 429 errors

        while (true) {
            try {
                $resp = $this->client->lists->getListMembersInfo($this->listId, [
                    'count'  => $pageSize,
                    'offset' => $offset,
                    'fields' => 'members.email_address,members.status,members.merge_fields,members.timestamp_signup,members.last_changed,members.tags,total_items',
                    'status' => 'all',
                ]);

                // FIX: access as object instead of array
                $members = $resp->members ?? [];
                $total   = $resp->total_items ?? 0;
                if (empty($members)) {
                    break; // no more contacts
                }

                foreach ($members as $m) {
                    yield [
                        'email'       => $m->email_address ?? null,
                        'first_name'  => $m->merge_fields->FNAME ?? null,
                        'last_name'   => $m->merge_fields->LNAME ?? null,
                        'signup_date' => !empty($m->timestamp_signup)
                                        ? $m->timestamp_signup : $m->last_changed
                                        ?? now()->toDateTimeString(),
                        'tags'        => array_values(array_map(
                            fn($t) => $t->name ?? null,
                            $m->tags ?? []
                        )),
                    ];
                }

                $offset += count($members);
                
                 // SAFETY: Stop if offset reaches total items
            if ($offset >= $total) {
                break;
            }

            $backoff = 1; // reset on success
            } catch (\MailchimpMarketing\ApiException $e) {
                $status = $e->getCode();

                if ($status === 429) {
                    sleep($backoff);
                    $backoff = min($backoff * 2, 60); // exponential backoff max 60s
                    continue; // retry
                }

                Log::error('Mailchimp allMembers error', [
                    'offset' => $offset,
                    'error'  => $e->getMessage()
                ]);
                throw $e;
            }
        }
    }


}


    
