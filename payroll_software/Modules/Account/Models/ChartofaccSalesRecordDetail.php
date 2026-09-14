<?php

namespace Modules\Account\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class ChartofaccSalesRecordDetail extends Model
{
    use HasFactory;

    protected $table = "chartofacc_sales_record_details";
    protected $primaryKey = 'srd_id';
    protected $guarded = [];

    protected $fillable = [
        'srd_id',
        'sr_auto_id',
        'spi_auto_id',
        'srd_description',
        'srd_qty',
        'srd_unit_price',
        'srd_inclusive',
        'srd_discount',
        'srd_vat_percent',
        'srd_vat_value',
        'srd_total_amount',
        'srd_product_id',
        'product_name',
    ];

    function product()
    {
        return $this->belongsTo(SalesProductInfo::class, 'srd_product_id', 'spi_auto_id');
    }
}
