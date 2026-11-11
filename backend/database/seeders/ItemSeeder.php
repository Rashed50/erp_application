<?php

namespace Database\Seeders;

use App\Models\InventoryUnit;
use App\Models\ItemCategory;
use App\Models\ItemName;
use App\Models\ItemSubCategory;
use App\Models\User;
use App\Models\Unit;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Use the first user as creator/updater (fallback to 1 if none)
        $userId = User::query()->value('id') ?? 1;

        // Safety: wipe existing seeded rows (dev only)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ItemName::query()->delete();
        ItemSubCategory::query()->delete();
        ItemCategory::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');



        // ---------------------------------------------------------------
        // Item data: category -> subcategory -> 1 item (4 items total)
        // ---------------------------------------------------------------
        $items = [
            [
                'category' => [
                    'icatg_name' => 'Electronics',
                    'icatg_code' => 'ELEC',
                ],
                'sub_category' => [
                    'iscatg_name' => 'Computers',
                    'iscatg_code' => 'COMP',
                ],
                'item' => [
                    'item_name'  => 'Laptop',
                    'item_title' => 'Dell Latitude 5440 Laptop',
                    'item_code'  => 'LAP001',
                    'itype_id'   => 1, // Asset
                    'unit'       => ['name' => 'Piece', 'short_name' => 'PCS'],
                    'track_type' => 'serial',
                    'minimum_stock' => 2.000,
                ],
            ],
            [
                'category' => [
                    'icatg_name' => 'Office Supplies',
                    'icatg_code' => 'OFFS',
                ],
                'sub_category' => [
                    'iscatg_name' => 'Stationery',
                    'iscatg_code' => 'STAT',
                ],
                'item' => [
                    'item_name'  => 'A4 Paper',
                    'item_title' => 'A4 Copier Paper 80 GSM (Ream)',
                    'item_code'  => 'PAP001',
                    'itype_id'   => 2, // Non-Asset
                    'unit'       => 1,
                    'track_type' => 'quantity',
                    'minimum_stock' => 50.000,
                ],
            ],
            [
                'category' => [
                    'icatg_name' => 'Tools',
                    'icatg_code' => 'TOOL',
                ],
                'sub_category' => [
                    'iscatg_name' => 'Hand Tools',
                    'iscatg_code' => 'HAND',
                ],
                'item' => [
                    'item_name'  => 'Hammer',
                    'item_title' => 'Claw Hammer 500g',
                    'item_code'  => 'HAM001',
                    'itype_id'   => 1, // Asset
                    'unit'       => ['name' => 'Piece', 'code' => 'PCS'],
                    'track_type' => 'quantity',
                    'minimum_stock' => 5.000,
                ],
            ],
            [
                'category' => [
                    'icatg_name' => 'Safety',
                    'icatg_code' => 'SAFE',
                ],
                'sub_category' => [
                    'iscatg_name' => 'PPE',
                    'iscatg_code' => 'PPE',
                ],
                'item' => [
                    'item_name'  => 'Safety Helmet',
                    'item_title' => 'Industrial Safety Helmet – Yellow',
                    'item_code'  => 'HEL001',
                    'itype_id'   => 2, // Non-Asset
                    'unit'       => ['name' => 'Piece', 'code' => 'PCS'],
                    'track_type' => 'quantity',
                    'minimum_stock' => 10.000,
                ],
            ],
        ];

        foreach ($items as $row) {
            // 1. Category (reuse if same code already exists)
            $category = ItemCategory::firstOrCreate(
                ['icatg_code' => $row['category']['icatg_code']],
                [
                    'icatg_name'    => $row['category']['icatg_name'],
                    'status'        => 1,
                    'create_by_id'  => $userId,
                    'update_by_id'  => null,
                ]
            );

            // 2. Sub-category under that category
            $subCategory = ItemSubCategory::firstOrCreate(
                [
                    'icatg_id'     => $category->icatg_id,
                    'iscatg_code'  => $row['sub_category']['iscatg_code'],
                ],
                [
                    'iscatg_name'   => $row['sub_category']['iscatg_name'],
                    'status'        => 1,
                    'create_by_id'  => $userId,
                    'update_by_id'  => null,
                ]
            );

            // 3. Unit (reuse if same code exists)
            // $unit = InventoryUnit::firstOrCreate(
            //     ['id' => $row['item']['unit']['code']],
            //     [
            //         'unit_name'     => $row['item']['unit']['name'],
            //         'short_name' => 'PCS',
            //         'status'        => 1,
            //         'created_by'  => $userId,
            //         'updated_by'  => null,
            //     ]
            // );

            
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

            // 4. Item
            ItemName::firstOrCreate(
                ['item_code' => $row['item']['item_code']],
                [
                    'icatg_id'      => $category->icatg_id,
                    'iscatg_id'     => $subCategory->iscatg_id,
                    'itype_id'      => $row['item']['itype_id'],
                    'item_name'     => $row['item']['item_name'],
                    'item_title'    => $row['item']['item_title'],
                    'item_status'   => 1,
                    'create_by_id'  => $userId,
                    'update_by_id'  => null,
                    'unit_id'       =>  $unit->unit_id,
                    'track_type'    => $row['item']['track_type'],
                    'minimum_stock' => $row['item']['minimum_stock'],
                ]
            );
        }

        $this->command->info('Seeded 4 items with their categories, sub-categories, and units.');
    }
}