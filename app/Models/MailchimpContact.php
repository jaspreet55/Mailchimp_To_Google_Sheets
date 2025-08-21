<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailchimpContact extends Model
{
    protected $table = 'mailchimp_contacts';
    protected $fillable = [
        'email', 'first_name', 'last_name', 'signup_date', 'tags', 'last_synced_at',
    ];

    protected $casts = [
        'signup_date' => 'datetime',
        'tags'        => 'array',
        'last_synced_at' => 'datetime',
    ];
}
