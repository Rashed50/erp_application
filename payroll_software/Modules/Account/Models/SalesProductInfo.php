<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesProductInfo extends Model
{
    use HasFactory;

    protected $table = "sales_product_infos";
    protected $guarded = [];
}
