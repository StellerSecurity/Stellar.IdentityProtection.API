<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IdentityService
{

    private $baseUrl = "https://haveibeenpwned.com/api/";

    public function breachedEmail(string $email)
    {

        $cache_key = "protection_m_" . $email;
        $breached = Cache::get($cache_key);

        if($breached !== null) {
            return $breached;
        }

        try {
            $response = Http::retry(3)->withHeader('hibp-api-key', env('PWNED_API_KEY'))
                ->get($this->baseUrl . "v3/breachedaccount/{$email}");

            $minutes = 60 * 60 * 72; // 72 hours cache.
            Cache::store('file')->put($cache_key, $response->object(), $minutes);

            return $response;
        } catch (\Exception $exception) {
            return [];
        }


    }

}
