<?php

namespace Modules\Account\Http\Controllers\Api;


use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    function index(Request $request)
    {
        # list of product with search, sort and pagination
        $products = Product::with('unit');
        if ($request->search) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sort') && $request->has('order')) {
            $request->validate([
                'sort' => 'in:name,price',
                'order' => 'in:asc,desc'
            ]);

            $products->orderBy($request->sort, $request->order);
        }

        $products = $products->paginate(50);
        return response()->json($products);
    }

    function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|float',
            'unit_id' => 'required|exists:units,id'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->unit_id = $request->unit_id;
        $product->save();

        return response()->json($product);
    }


    function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|float',
            'unit_id' => 'required|exists:units,id'
        ]);

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->price = $request->price;
        $product->unit_id = $request->unit_id;
        $product->save();

        return response()->json($product);
    }


    function delete(Request $request)
    {
        $product = Product::find($request->id);
        $product->delete();
    }

    function show(Request $request)
    {
        $product = Product::with('unit')->find($request->id);
        return response()->json($product);
    }
}
