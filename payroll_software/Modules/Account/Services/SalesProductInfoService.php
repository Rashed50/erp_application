<?php

namespace Modules\Account\Services;
use App\Models\Product;
use Modules\Account\Models\SalesProductInfo;

class SalesProductInfoService
{
    // get Product for Sale add or edit panel
    public function getProductsForSales($branch_office_id)
    {
        // return SalesProductInfo::where('spi_status', 1)
        // ->get('spi_auto_id','spi_name_en','spi_name_en','spi_name_ab','spi_code');
        return SalesProductInfo::latest()
            ->where(function ($query) use ($branch_office_id) {
                $query->where('branch_office_id', $branch_office_id)
                    ->orWhereNull('branch_office_id');
            })
            //->with('unit')
            ->get();
    }

    function create($name, $unit_id, $price, $branch_office_id,$created_by_id)
    {
        return SalesProductInfo::create([
            'spi_name_en' => $name,
            'spi_unit' => $unit_id,
            'spi_code' => random_int(1000, 9999), // Assuming this is a random code
           // 'price' => $price,
            'branch_office_id' => $branch_office_id,
            'created_by_id' => $created_by_id

        ]);
    }

    function update($spi_auto_id, $name, $unit_id, $updated_by_id)
    {
       
       SalesProductInfo::where('spi_auto_id',$spi_auto_id)->update([
            'spi_name_en' => $name,
            'spi_unit' => $unit_id,
            'updated_by_id' => $updated_by_id
           // 'price' => $price
        ]);
       return $product = SalesProductInfo::where('spi_auto_id',$spi_auto_id)->first();
    }
}
