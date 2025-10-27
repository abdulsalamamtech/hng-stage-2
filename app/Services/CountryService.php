<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountryService
{

    public function getAllCountries()
    {
        // Logic to retrieve all countries
        $countries = Country::all();
        return $countries;
    }

    public function getAllCountriesImage()
    {
        // Logic to retrieve all countries
        $countries = Country::get(['flag_url']);
        return $countries;
    }


    // Update or Insert new record
    public function upsertCountriesToDatabase()
    {
        info('started execution to database!');
        // Logic to add a new country
        $countriesAndExchangeRatesData = $this->fetchCountriesAndExchangeRatesData();
        if (!$countriesAndExchangeRatesData) {
            return [];
        }

        // Add multiple items to data base
        // Docs: https://laravel.com/docs/12.x/eloquent#upserts
        $countries = Country::upsert($countriesAndExchangeRatesData, [
            'name'
        ], [
            'name',
            'capital',
            'region',
            'population',
            'currency_code',
            'exchange_rate',
            'estimated_gdp',
            'flag_url',
            'last_refreshed_at',
        ]);
        if (!$countries) {
            Log::error('Error storing data to database!');
            return [];
        }
        info('Countries data store to database');
        // info($countries);
        return $countries;
    }


    public function deleteCountry($code)
    {
        // Logic to delete a country by its code
    }

    // Get countries and Exchange rate
    public function fetchCountriesAndExchangeRatesData()
    {
        $countries = $this->fetchCountriesFromApi();
        $exchangeRate = $this->fetchExchangeRatesFromApi();

        $countries_data = [];
        if ($countries && $exchangeRate) {
            foreach ($countries as $country) {

                $current_country = [
                    'name' => $country['name'] ?? null,
                    'capital' => $country['capital'] ?? null,
                    'region' => $country['region'] ?? null,
                    'population' => $country['population'] ?? null,
                    'currency_code' => $country['currencies'][0]['code'] ?? null,
                    'exchange_rate' => null,
                    'estimated_gdp' => $country['estimated_gdp'] ?? 0,
                    'flag_url' => $country['flag'] ?? null,
                    'last_refreshed_at' => now(),
                ];

                // compute estimated_gdp
                if (isset($country['name']) && isset($country['currencies'][0]['code'])) {
                    $countyCode = $country['currencies'][0]['code'] ?? null;
                    if (($country['currencies'][0]['code']) && isset($exchangeRate[$countyCode])) {
                        $randomFactor = rand(1000, 2000);
                        $current_country['currency_code'] = $countyCode;
                        $current_country['exchange_rate'] = $exchangeRate[$countyCode];
                        $current_country['estimated_gdp'] = ($country['population'] * $randomFactor) / $exchangeRate[$countyCode] ?? 0;
                    }
                }

                $countries_data[] = $current_country;
            }
        }
        return $countries_data;
    }


    // fetch countries from external API
    private function fetchCountriesFromApi()
    {
        try {
            //code...
            // Logic to fetch countries from an external API
            $res = Http::timeout(10)->get('https://restcountries.com/v2/all?fields=name,capital,region,population,flag,currencies');
            // check for successful response
            if ($res->successful()) {
                return $res->json();
            }
            return [];
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error fetching countries: ' . $th->getMessage());
            return [];
        }
    }

    // fetch exchange rates from external API
    private function fetchExchangeRatesFromApi()
    {
        try {
            //code...
            // Logic to fetch exchange rates from an external API increase timeout

            $res = Http::timeout(10)->get('https://open.er-api.com/v6/latest/USD');
            // check for successful response
            if ($res->successful()) {
                return $res->json()['rates'];
            }
            return [];
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error fetching exchange rates: ' . $th->getMessage());
            return [];
        }
    }
}
