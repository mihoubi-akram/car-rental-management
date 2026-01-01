<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\RentalContract;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class RentalContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $vehicles = Vehicle::all();

        RentalContract::factory(20)
            ->recycle($clients)
            ->recycle($vehicles)
            ->create();
    }
}
