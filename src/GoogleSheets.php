<?php

namespace App;

use Exception;
use Google\Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;

class GoogleSheets
{
    public static function getService(){
        return new Google_Service_Sheets(self::getClient());
    }

    public static function getClient()
    {
        $client = new Client();
        $client->setApplicationName('YandexTaxi');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        $client->setAuthConfig(__DIR__ . '/../storage/google-docs/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $tokenPath = __DIR__ . '/../storage/google-docs/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        $client->setAccessToken($accessToken);

        return $client;
    }

    public static function makeValueRange($range, $values)
    {
        return new Google_Service_Sheets_ValueRange(
            [
                'range' => $range,
                'majorDimension' => 'ROWS',
                'values' => ['values' => $values],
            ]
        );
    }
}