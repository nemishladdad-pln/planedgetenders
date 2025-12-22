<?php

namespace App\Services\Google;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetService
{
    public $client, $service, $documentId, $range;
    public function __construct() {
        $this->client = $this->getClient();
        $this->service = new Sheets($this->client);
        $this->documentId = env('POST_SPREADSHEET_ID', '1Lbwx811Wz50JmL0UyqiNQJ3e4uRq6p1BXTWYuJ9d0Q8');
        $this->range = "A:Z";
    }

    public function getClient()
    {
        $client = new Client();
        $client->setApplicationName('Google Sheets Demo');
        $client->setRedirectUri('http://localhost:8000/api/v1/googleSheet');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setAccessType('offline');

        return $client;
    }

    public function readSheet()
    {
        $doc = $this->service->spreadsheets_values->get($this->documentId, $this->range);

        return $doc;
    }

    public function writeSheet($data)
    {
        $body = new ValueRange(['values' => $data]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $doc = $this->service->spreadsheets_values->update($this->documentId, $this->range, $body, $params);

        return $doc;
    }
    public function appendSheet($data)
    {
        $body = new ValueRange(['values' => $data]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $doc = $this->service->spreadsheets_values->append($this->documentId, $this->range, $body, $params);

        return $doc;
    }
}
