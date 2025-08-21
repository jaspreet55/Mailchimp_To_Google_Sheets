<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MailchimpMarketing\ApiClient;
use App\Models\MailchimpContact;
class MailchimpContactController extends Controller
{
    public function add(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|unique:mailchimp_contacts,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'tags' => 'nullable|string', // accept comma separated
        ]);

        $tags = [];
        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
        }
        // Save locally (signup_date = now())
        $contact = MailchimpContact::updateOrCreate(
            ['email' => strtolower($validated['email'])],
            [
                'first_name' => $validated['first_name'] ?? '',
                'last_name' => $validated['last_name'] ?? '',
                'signup_date' => now(),
                'tags' => $tags,
                'last_synced_at' => now(),
            ]
        );
        $mailchimp = new \MailchimpMarketing\ApiClient();
        $mailchimp->setConfig([
            'apiKey' => config('services.mailchimp.api_key'),
            'server' => config('services.mailchimp.server_prefix'),
        ]);
        
        $listId = config('services.mailchimp.list_id');
        

        try {
            $hash = md5(strtolower($validated['email']));
            $mailchimp->lists->setListMember($listId, $hash, [
                'email_address' => $validated['email'],
                'status_if_new' => 'subscribed',
                'status' => 'subscribed',
                'merge_fields' => [
                    'FNAME' => $validated['first_name'] ?? '',
                    'LNAME' => $validated['last_name'] ?? '',
                ],
                'tags' => $tags,
            ]);

            return redirect('/mailchimp/list')->with('success', 'Contact added successfully!');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function list()
    {
        $contacts = MailchimpContact::latest()->get();
        return view('mailchimp.list', compact('contacts'));
    }

}
