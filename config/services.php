<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'mailchimp' => [
    'api_key'        => env('MAILCHIMP_API_KEY'),
    'server_prefix'  => env('MAILCHIMP_SERVER_PREFIX'),
    'list_id'        => env('MAILCHIMP_LIST_ID'),
    'webhook_token'  => env('MAILCHIMP_WEBHOOK_TOKEN'),
    ],

    'google' => [
        'sheets' => [
            'spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID'),
            'worksheet_name' => env('GOOGLE_SHEETS_WORKSHEET_NAME', 'Contacts'),
            'credentials'    => env('GOOGLE_APPLICATION_CREDENTIALS'),
            'account_cred' => [
                    "type"=> "service_account",
                    "project_id"=> "mailchimp-sync-469519",
                    "private_key_id"=> "6bf88f9dad3726bd3cd933e441b4f9aad7be9589",
                    "private_key"=> "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDf3yDpm67OVouU\n/7Z1sOjVqwGJu3O8QPi5iZzp2RsvrXNzT/8Z4DRU7GZX60I0OOUVfXbUb/3OkqG9\n0agAfWxjZWJres/xdifQZ1pPYjaaFKzRUO6Lprgjw2rdOTLmvy9UbGeJCkmnFINI\nV+4loljGXlNRQ0oh0bQXgoCC4vNR4NvmnfoA66lhDuCfCpysqzhxWDwIt1TKQtOj\nx1PpSQH2DvH13NU+HmJEl3FlMKlPchrYzXNQKvUxfql9c90uPPIKmJeSx9Ae8Cac\n+cOuukHIlZKd8A/xBQVJYhG4OgQhoYSMG2IUmH77V2TW6B66nfIitKW71TUbURIK\nq/GuK+1/AgMBAAECggEAVQSfMqhZb+nlE14zNCnNmotZR62lC7qe2pM8wIAfN3GH\nFSUtq21+Yjvut/fTihKLSSh/DrlDKYZ9HyG2TA1Vsr+na0rWGox1HUuKu/f/5W7x\nnIJAPU/AwXAurrnQr1mux0Bt51i9VNLQ8pHWEnu/fAIJYSSNTB+f5Ye9dzdqEU7+\nRVnAQ5Falnhz6OpQowqxdzbXCTZPVz/b5c43216P7W1Dq476eNmfDFaadtSeRBQr\njMejBtEsefaHi2PcXgOp0rjG3bAPo46HHUddcwJKwKEBeduLnyw7qDD2XDMwmKAq\n3x4IB6PvIfQsxJTO3kTRah3J/6NFcWTiNv/qw3ij4QKBgQD0XDI6oaIHlms79Aal\naJ4Sa4YhXBF3EZicA89I4gxf8utanxWn0YhVOUmFQcdxB1sS0CShVk8IeAAu0snZ\nZVnBCbC1Rj8tUs9tBA9F/KWumqKmhFI6XJpo8zC0JvEZPLo2E1iWdmvpzNCcIrxt\nkKCo0cOlYSdub7CUA9tnuzyIIQKBgQDqiRaxRatgh4yYBIvsjfen9xPyV8vSB+Ub\niE9qLpdoZWNCfhwU9gJ1wTlVBimLTVDbdJJg4M5HCH1RGNzhhMOIfcE1S8nsEp7Q\nCGZPXTwfFWjIKaGlOo9/YfquHAE40mQymWYk01On7cyp+NvlDjM/1VGLHdcZRkrR\n80UkWKNBnwKBgGvCgLAUeWTJM67ztT6afSL4YeUyOc19aARdNbsZXIWvlZSYFS+1\nml6i7cYjNPWmXcZVHeI8Mow0hzge4r7JECStxkyalt/A5hJYOPgPK21tag7uyWhN\nUBa6MB8yER6lBiU0sNthi8NF/DTuqJPUtNTd9F5pEr+D56XOcaI0EsShAoGAQj6s\ncS/bgy1mMb3/A7bpv8CTGr1frZhbRxaOT9CPJYaVrb9PEUCXW0lTd0eBTn5tGz9m\n5QQ6X2fvdYuPQAeu7wuOGg6pHwv3XY0x0LLl+2Whrf/MT44l3/X9x1XTpogOkxgu\n+LHUDDenuk63VZBYO2x2VtP74ICxO3H6EpUSr8MCgYEAsw2jJQIbMwSLaQ+ZFDLe\nfSgxVeeqm6TxavQNgyw5YTWzRgRf4edqMSnkHx04Hxez5jhibt8xDTZw6CNLTpse\nSqkuTKFeTRq2P6ZDFoLqDX3Yg2TAma1a7Y2UHPg1OPyppy9plqLNv/Cq2AE4A5TA\nbG3M9dJHGtjbXI5P226TXyY=\n-----END PRIVATE KEY-----\n",
                    "client_email"=> "laravel-sheets-sa@mailchimp-sync-469519.iam.gserviceaccount.com",
                    "client_id"=> "111632524225574024779",
                    "auth_uri"=> "https://accounts.google.com/o/oauth2/auth",
                    "token_uri"=> "https://oauth2.googleapis.com/token",
                    "auth_provider_x509_cert_url"=> "https://www.googleapis.com/oauth2/v1/certs",
                    "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/laravel-sheets-sa%40mailchimp-sync-469519.iam.gserviceaccount.com",
                    "universe_domain"=> "googleapis.com" 

            ]
        ],
    ],

];
