<?php

namespace App\Services;

use Google_Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Cache;

class GoogleSheetsService
{
    protected Sheets $service;
    protected string $spreadsheetId;
    protected string $worksheet;

    public function __construct()
    {
        $client = new Google_Client();
        $client->setAuthConfig(config('services.google.sheets.credentials'));
        $client->setScopes([Sheets::SPREADSHEETS]);

        $this->service = new Sheets($client);
        $this->spreadsheetId = config('services.google.sheets.spreadsheet_id');
        $this->worksheet = config('services.google.sheets.worksheet_name', 'Contacts');
    }

    protected function headerRow(): array
    {
        return ['Email', 'First Name', 'Last Name', 'Signup Date', 'Tags'];
    }

    /** Ensure sheet exists and header row is present. Idempotent. */
    public function ensureHeader(): void
    {
        $range = "{$this->worksheet}!A1:E1";
        $current = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $current->getValues();

        if (empty($values)) {
            $body = new ValueRange([
                'values' => [$this->headerRow()],
            ]);
            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                $range,
                $body,
                ['valueInputOption' => 'RAW']
            );
        }
    }

    /** Build email->rowIndex map (1-based row index). Cached 60s to reduce reads. */
    public function emailRowMap(): array
    {
        return Cache::remember('gsheet_email_row_map', 60, function () {
            $range = "{$this->worksheet}!A2:A"; // Email column
            $data = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            $rows = $data->getValues() ?? [];
            $map = [];
            foreach ($rows as $i => $row) {
                $email = strtolower(trim($row[0] ?? ''));
                if ($email !== '') {
                    $map[$email] = $i + 2; // A2 is row 2
                }
            }
            return $map;
        });
    }

    /** Upsert a single contact by email (update if exists, else append). */
    public function upsertContact(array $contact): void
    {
        $this->ensureHeader();

        $email = strtolower(trim($contact['email']));
        $first = $contact['first_name'] ?? '';
        $last  = $contact['last_name'] ?? '';
        $date  = $contact['signup_date'] ?? '';
        $tags  = $contact['tags'] ?? [];
        if (is_array($tags)) {
            $tags = implode(', ', array_filter($tags));
        }

        $rowValues = [ $email, $first, $last, $date, $tags ];

        $map = $this->emailRowMap();
        if (isset($map[$email])) {
            $rowIdx = $map[$email];
            $range = "{$this->worksheet}!A{$rowIdx}:E{$rowIdx}";
            $body = new ValueRange(['values' => [ $rowValues ]]);
            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                $range,
                $body,
                ['valueInputOption' => 'RAW']
            );
        } else {
            $range = "{$this->worksheet}!A:E";
            $body = new ValueRange(['values' => [ $rowValues ]]);
            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                $range,
                $body,
                ['valueInputOption' => 'RAW', 'insertDataOption' => 'INSERT_ROWS']
            );
            // Bust cache so the new email will be discoverable immediately in next call
            Cache::forget('gsheet_email_row_map');
        }
    }
}
