<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryUnitSeeder extends Seeder
{
    /**
     * Seeds the common measuring units used in the Unit dropdowns.
     *
     * Safe to run more than once: a unit is added only when its name AND
     * short name are both still free, so units you edited or switched off
     * are never overwritten.
     *
     * Run:  php artisan db:seed --class=InventoryUnitSeeder
     */
    public function run(): void
    {
        // [name, short_name]
        $units = [
            // Count
            ['Piece', 'pcs'],
            ['Pair', 'pair'],
            ['Set', 'set'],
            ['Dozen', 'dz'],
            ['Lot', 'lot'],
            ['Number', 'nos'],

            // Packing
            ['Box', 'box'],
            ['Carton', 'ctn'],
            ['Packet', 'pkt'],
            ['Bag', 'bag'],
            ['Roll', 'roll'],
            ['Bundle', 'bdl'],

            // Weight
            ['Gram', 'g'],
            ['Kilogram', 'kg'],
            ['Ton', 'ton'],

            // Volume
            ['Milliliter', 'ml'],
            ['Liter', 'ltr'],

            // Length
            ['Millimeter', 'mm'],
            ['Centimeter', 'cm'],
            ['Inch', 'in'],
            ['Feet', 'ft'],
            ['Meter', 'm'],
            ['Running Feet', 'rft'],

            // Area / volume of space
            ['Square Feet', 'sqft'],
            ['Square Meter', 'sqm'],
            ['Cubic Feet', 'cft'],
            ['Cubic Meter', 'cum'],

            // Service / time
            ['Hour', 'hr'],
            ['Day', 'day'],
            ['Month', 'month'],
            ['Job', 'job'],
        ];

        $now = now();

        foreach ($units as [$name, $shortName]) {
            $taken = DB::table('inventory_units')
                ->where('name', $name)
                ->orWhere('short_name', $shortName)
                ->exists();

            if ($taken) {
                continue;
            }

            DB::table('inventory_units')->insert([
                'name' => $name,
                'short_name' => $shortName,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
