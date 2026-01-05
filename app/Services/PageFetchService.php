<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                if (empty($body)) {
                    throw new Exception('Empty response body from ' . $url);
                }
                return $body;
            }

            throw new Exception('HTTP request failed with status ' . $response->status() . ' for URL: ' . $url);
        } catch (Exception $e) {
            Log::error('PageFetchService error: ' . $e->getMessage());
            throw $e;
        } catch (\Throwable $e) {
            Log::error('PageFetchService error: ' . $e->getMessage());
            throw new Exception('Failed to fetch page: ' . $e->getMessage());
        }
    }
}