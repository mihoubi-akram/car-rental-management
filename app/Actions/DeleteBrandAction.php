<?php

namespace App\Actions;

use App\Exceptions\BrandHasVehiclesException;
use App\Models\Brand;

class DeleteBrandAction
{
    public function execute(Brand $brand): void
    {
        // Prevent deletion if brand has any vehicles
        if ($brand->vehicles()->exists()) {
            throw new BrandHasVehiclesException;
        }

        $brand->delete();
    }
}
