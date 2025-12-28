<?php

namespace App\Services;
use Exception;
use Illuminate\Support\Facades\Http;

class PageFetchService
{
    public function fetchPageContent($url)
    {
        try {
            $response = Http::timeout(120)->withOptions([
                'verify' => false,
            ])->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])->get($url);

            if ($response->successful()) {
                $body = $response->body();
                return !empty($body) ? $body : null;
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}