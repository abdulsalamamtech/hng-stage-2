<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $countries = Country::query();
        // DB (support filters and sorting) - ?region=Africa | ?currency=NGN | ?sort=gdp_desc
        if ($request->filled('region')) {
            $countries->where('region', $request->region);
        }
        if ($request->filled('currency')) {
            $countries->where('currency_code', $request->currency);
        }
        if ($request->filled('sort')) {
            if ($request->input('sort') == 'gdp_desc') {
                $countries->orderBy('estimated_gdp', 'desc');
            } else {
                $countries->orderBy('estimated_gdp', 'asc');
            }
        }
        $countries = $countries->get();

        if (!$countries) {
            return response()->json([
                'error' => 'Country not found'
            ], 400);
        }

        return response()->json($countries, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CountryService $countryService)
    {
        $countries = $countryService->upsertCountriesToDatabase();
        if (!$countries) {
            // error
            return response()->json([], 400);
        }
        $countries = Country::all();
        if (!$countries) {
            // error
            return response()->json([], 400);
        }
        return response()->json($countries, 201);
    }

    /**
     * Display the specified resource.
     */
    // public function show(Country $country)
    // {
    //     return $country;
    // }
    public function show($country)
    {
        // If the request data is image
        if ($country == 'image') {
            return $this->image();
        }

        $country = Country::where('name', $country)->first();
        if (!$country) {
            // error
            return response()->json([], 404);
        }
        return response()->json($country, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();
        return response()->json([], 204);
    }

    /**
     * Display a listing of the resource.
     */
    public function status()
    {
        $countryService = new CountryService();
        $countries = $countryService->getAllCountries();

        // 
        // {
        // "total_countries": 250,
        // "last_refreshed_at": "2025-10-22T18:00:00Z"
        // }
        return response()->json([
            'total_countries' => $countries->count(),
            'last_refreshed_at' => $countries->first()->last_refreshed_at
        ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function image()
    {
        // $countryService = new CountryService();
        // $countries = $countryService->getAllCountriesImage();

        $countries = null;
        if (!$countries) {
            return response()->json([
                'error' => 'Summary image not found'
            ], 200);
        }

        return response()->json($countries, 200);
    }
}
