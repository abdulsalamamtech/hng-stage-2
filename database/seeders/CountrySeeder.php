<?php

namespace Database\Seeders;

use App\Services\CountryService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        info('executing countries seeder!');
        $countryService = new CountryService();
        $data = $countryService->upsertCountriesToDatabase();
        // It's not returning response data
        info('seeder response',  [$data]);
        info('countries seeder executed!');
    }
}
