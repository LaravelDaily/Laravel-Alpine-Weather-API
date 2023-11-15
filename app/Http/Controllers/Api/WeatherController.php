<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;

class WeatherController extends Controller
{
    public function __invoke(string $city): mixed
    {
        return Cache::remember('city' . $city, 60 * 5, function () use ($city) {
            $response = Http::withHeaders([
                'X-Api-Key' => config('services.api_ninjas.key'),
            ])
                ->get('https://api.api-ninjas.com/v1/city?name=' . $city);

            if ($response->successful() && ! empty($response->json())) {
                $city = $response->json(0);

                $weather = Http::get('https://api.open-meteo.com/v1/forecast?latitude=' . $city['latitude'] . '&longitude=' . $city['longitude'] . '&daily=temperature_2m_max,temperature_2m_min&timezone=UTC');

                if ($weather->successful()) {
                    return $weather->json('daily');
                }

                return response()->json([]);
            }

            return response()->json([]);
        });
    }
}
