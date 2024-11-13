<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class IdentityService
{

    private $baseUrl = "https://haveibeenpwned.com/api/";

    public function breachedEmail(string $email)
    {

        try {
            $response = Http::retry(3)->withHeader('hibp-api-key', env('PWNED_API_KEY'))
                ->get($this->baseUrl . "v3/breachedaccount/{$email}");
            return $response;
        } catch (\Exception $exception) {
            return null;
        }


    }

}
