<?php

namespace Modules\Account\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Services\SalesProductInfoService;

class SalesProductController extends Controller
{

    public function index()
    {
        $salesProductInfoService = new SalesProductInfoService();

       //  dd($salesProductInfoService->getProductsForSales(auth()->user()->branch_office_id));
        return view('account::pages.products.index', [
            'products' => $salesProductInfoService->getProductsForSales(auth()->user()->branch_office_id),
            'units' => Unit::current_branch()->get(),
        ]);
    }

    public function getProductsForSales()
    {
        $salesProductInfoService = new SalesProductInfoService();

        $products = $salesProductInfoService->getProductsForSales(auth()->user()->branch_office_id);
    }

    function store(Request $request)
    {
        $salesProductInfoService = new SalesProductInfoService();

        $request->validate([
            'spi_name_en' => 'required|max:255',
            'spi_unit' => 'required|exists:units,id',
            //'price' => 'required|numeric',
        ]);

        $product = $salesProductInfoService->create($request->spi_name_en, $request->spi_unit, $request->price, auth()->user()->branch_office_id,auth()->user()->id);
        return response()->json($product);

    }

    function update(Request $request)
    {
        $salesProductInfoService = new SalesProductInfoService();

         $request->validate([
          // 'spi_auto_id' => 'required|exists:sales_product_infos,spi_auto_id',
            'spi_name_en' => 'required|max:255',
            'spi_unit' => 'required|exists:units,id',
            //'price' => 'required|numeric',
        ]);
        $product = $salesProductInfoService->update($request->spi_auto_id, $request->spi_name_en, $request->spi_unit,auth()->user()->id);
        return response()->json($product);
    }

    function delete(Product $product, Request $request)
    {
        // $product = Product::findOrFail($request->id);
        $product->delete();
    }


    /* ======================== API RESPONSE ======================== */

    function units()
    {
        return view('account::pages.products.units', [
            'units' => Unit::current_branch()->get()
        ]);
    }
}
