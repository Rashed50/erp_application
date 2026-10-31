<?php

namespace Database\Seeders;

use App\Models\InventoryUnit;
use App\Models\User;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemUnitSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id') ?? 1;



            $unit = InventoryUnit::firstOrCreate(
                ['id' =>  1],
                [
                    'name'    => 'Piece',
                    'short_name' => 'PCS',
                    'status'        => 1,
                    'created_by'  => $userId,
                    'updated_by'  => null,
                ]
            );
            $unit = InventoryUnit::firstOrCreate(
                ['id' =>  2],
                [
                    'name'    => 'Square Meter',
                    'short_name' => 'SQM',
                    'status'        => 1,
                    'created_by'  => $userId,
                    'updated_by'  => null,
                ]
            );


            $unit = InventoryUnit::firstOrCreate(
                ['id' =>  3],
                [
                    'name'    => 'Cube Meter',
                    'short_name' => 'CBM',
                    'status'        => 1,
                    'created_by'  => $userId,
                    'updated_by'  => null,
                ]
            );
            $unit = InventoryUnit::firstOrCreate(
                ['id' =>  4],
                [
                    'name'    => 'Box',
                    'short_name' => 'BOX',
                    'status'        => 1,
                    'created_by'  => $userId,
                    'updated_by'  => null,
                ]
            );
            $unit = InventoryUnit::firstOrCreate(
                ['id' =>  5],
                [
                    'name'    => 'Lam Sam',
                    'short_name' => 'LS',
                    'status'        => 1,
                    'created_by'  => $userId,
                    'updated_by'  => null,
                ]
            );
    }
}