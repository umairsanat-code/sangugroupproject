<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlightService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.amadeus.base_url', 'https://test.api.amadeus.com');
        $this->clientId = config('services.amadeus.client_id');
        $this->clientSecret = config('services.amadeus.client_secret');
    }

    /**
     * Search for flights - Demo/Live hybrid
     */
    public function searchFlights($params)
    {
        // If we have API keys, try live search, otherwise return demo data
        if ($this->clientId && $this->clientSecret) {
            try {
                return $this->searchLiveFlights($params);
            } catch (\Exception $e) {
                Log::error("Amadeus API Error: " . $e->getMessage());
                return $this->getDemoFlights($params);
            }
        }

        return $this->getDemoFlights($params);
    }

    protected function searchLiveFlights($params)
    {
        $token = $this->getAccessToken();
        
        $response = Http::withToken($token)
            ->get($this->baseUrl . '/v2/shopping/flight-offers', [
                'originLocationCode' => $params['from_code'] ?? 'LHR',
                'destinationLocationCode' => $params['to_code'] ?? 'DXB',
                'departureDate' => $params['date'] ?? date('Y-m-d', strtotime('+7 days')),
                'adults' => $params['adults'] ?? 1,
                'max' => 10,
            ]);

        if ($response->successful()) {
            return $this->formatAmadeusResponse($response->json());
        }

        throw new \Exception("Failed to fetch flights from Amadeus");
    }

    protected function getAccessToken()
    {
        // Simple token fetch (should be cached in production)
        $response = Http::asForm()->post($this->baseUrl . '/v1/security/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return $response->json()['access_token'];
    }

    protected function formatAmadeusResponse($data)
    {
        // Map Amadeus response to our app's flight structure
        return array_map(function($offer) {
            $itinerary = $offer['itineraries'][0];
            $segment = $itinerary['segments'][0];

            return [
                'id' => $offer['id'],
                'airline' => [
                    'name' => $segment['carrierCode'], // Simplified
                    'code' => $segment['carrierCode'],
                ],
                'flight_number' => $segment['number'],
                'departure_city' => $segment['departure']['iataCode'],
                'arrival_city' => $segment['arrival']['iataCode'],
                'departure_time' => $segment['departure']['at'],
                'arrival_time' => $segment['arrival']['at'],
                'price' => $offer['price']['total'],
                'currency' => $offer['price']['currency'],
                'class' => $offer['travelerPricings'][0]['fareDetailsBySegment'][0]['cabin'],
            ];
        }, $data['data'] ?? []);
    }

    protected function getDemoFlights($params)
    {
        $from = $params['from'] ?? 'London';
        $to = $params['to'] ?? 'Dubai';
        $date = $params['date'] ?? date('Y-m-d', strtotime('+3 days'));

        return [
            [
                'id' => 'demo_1',
                'airline' => ['name' => 'Emirates', 'code' => 'EK'],
                'flight_number' => 'EK77',
                'departure_city' => $from,
                'arrival_city' => $to,
                'departure_time' => $date . ' 10:30:00',
                'arrival_time' => $date . ' 16:45:00',
                'price' => 450.00,
                'currency' => 'USD',
                'class' => 'ECONOMY',
            ],
            [
                'id' => 'demo_2',
                'airline' => ['name' => 'British Airways', 'code' => 'BA'],
                'flight_number' => 'BA112',
                'departure_city' => $from,
                'arrival_city' => $to,
                'departure_time' => $date . ' 14:15:00',
                'arrival_time' => $date . ' 20:30:00',
                'price' => 380.00,
                'currency' => 'USD',
                'class' => 'ECONOMY',
            ],
            [
                'id' => 'demo_3',
                'airline' => ['name' => 'Qatar Airways', 'code' => 'QR'],
                'flight_number' => 'QR90',
                'departure_city' => $from,
                'arrival_city' => $to,
                'departure_time' => $date . ' 08:00:00',
                'arrival_time' => $date . ' 14:50:00',
                'price' => 520.00,
                'currency' => 'USD',
                'class' => 'BUSINESS',
            ],
        ];
    }
}
