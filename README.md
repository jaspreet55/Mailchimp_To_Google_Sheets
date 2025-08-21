# Mailchimp_To_Google_Sheets
Build an integration using Laravel that syncs contact data from Mailchimp to a Google Sheet in two scenarios: 1. Real-time sync – when a new contact is created in Mailchimp. 2. Historical sync – import all existing contacts from Mailchimp.


## Features
- Add contacts via form → Mailchimp → Auto-sync to DB & Google Sheets
- Real-time updates via Mailchimp webhook
- Historical sync for existing Mailchimp contacts
- Queue-based processing for scalability
- Idempotent (no duplicate entries on re-sync)
- Ngrok support for local webhook testing

## Requirements
- PHP 8.1+ (Laravel 12)
- Composer
- Mailchimp account with:
  - API Key
  - Audience (List ID)
- Google Cloud project with Sheets API enabled
- Ngrok (for local testing of webhooks)  
Here I am using ngrok for https url that i added in mailchimp webhook url
- MySQL database


## Installation & Setup

### 1. Clone & Install

## https://github.com/jaspreet55/Mailchimp_To_Google_Sheets.git
cd mailchimp-google-sheets-sync
composer install

Here i am usimng ngrok for local testing
so install ngrok on system then
hit-> ngrok config add-authtoken 2eSeZP38q5VsHeiMTuGEf2LU8ve_7PkNtALaamU9kxJYDaqKA  (replace token with your create token)
ngrok http 8000

This will generate a URL like: https://abc123.ngrok-free.app. 
 Use this for your Mailchimp webhook.


1. Get Mailchimp API Key

Log in to Mailchimp ->Go to https://login.mailchimp.com. Navigate to Your Profile → Extras → API Keys
- Click your profile icon (bottom-left corner).
- Go to Account & billing → Extras → API Keys.
- Create a New API Key
- Click "Create A Key".
- Copy the API key generated.
- Save It in .env File

MAILCHIMP_API_KEY=your-mailchimp-api-key
MAILCHIMP_SERVER_PREFIX=usXX


usXX is your data center prefix, shown in your API key (e.g., us14).

2. Get Audience (List) ID
-Go to Audience → Audience dashboard.
-Select the audience you want to sync.
-Click Settings → Audience name and defaults.
-Copy the Audience ID (List ID).
-It will look like: e838122685.
-Add it to .env:
-MAILCHIMP_LIST_ID=e838122685

3. Set Up Webhook

In Mailchimp, go to:
-Audience → Settings → Webhooks

-Click Create New Webhook.

-Enter your callback URL (for local development, use Ngrok):

-https://<ngrok-id>.ngrok-free.app/mailchimp/webhook/superSecretContacts

Select Trigger Events:Subscribes,Profile updates,Email changes,Unsubscribes (optional if you want to track removals)
Set Format: JSON or URL-encoded (use URL-encoded if following your current controller).
Save.

4. Add Webhook Token

Add the secret token you used in the URL to .env:

MAILCHIMP_WEBHOOK_TOKEN=superSecretContacts

create mailchimp credential and google sheet credential and keep in environment file

MAILCHIMP_API_KEY=your-mailchimp-api-key
MAILCHIMP_SERVER_PREFIX=us14
MAILCHIMP_LIST_ID=e838122685
MAILCHIMP_WEBHOOK_TOKEN=superSecretContacts

GOOGLE_SHEETS_CREDENTIALS=storage/app/google-credentials.json
GOOGLE_SHEETS_SPREADSHEET_ID=your-spreadsheet-id
GOOGLE_SHEETS_WORKSHEET_NAME=Contacts

QUEUE_CONNECTION=database


Start Laravel development server:

-php artisan serve

Start Ngrok:

-ngrok http 8000


Copy the Ngrok HTTPS URL (e.g., https://abc123.ngrok-free.app).

Add Mailchimp webhook:

https://abc123.ngrok-free.app/mailchimp/webhook/superSecretContacts

Start Queue Worker
--php artisan queue:work --tries=3
hit this command to run queue



1. Add a Contact
Open / (Add Contact form)

Fill in Email, First Name, Last Name, Tags

Submit → Stored in Mailchimp → Webhook triggers → Synced to DB & Google Sheets

2. View Contacts

Open /mailchimp/list to see all contacts

3. Real-Time Sync

Mailchimp sends a webhook whenever a contact is added/updated

Laravel processes the webhook via ProcessMailchimpContact job

Job:

Fetches contact details via Mailchimp API

Upserts into DB

Upserts into Google Sheets

4. Historical Sync

Backfill all existing Mailchimp contacts:   php artisan mailchimp:sync-all --chunk=500  
run this commandd to sync historial contact on google sheet

