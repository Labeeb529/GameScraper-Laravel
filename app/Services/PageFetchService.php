<?php

namespace App\Services;
use Exception;
use Illuminate\Support\Facades\Http;

class PageFetchService
{
    public function fetchPageContent($url)
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])->get($url);

        if ($response->successful()) {
            return $response->body();
        }

        throw new Exception('Failed to fetch page content. HTTP ' . $response->status());
    }
}