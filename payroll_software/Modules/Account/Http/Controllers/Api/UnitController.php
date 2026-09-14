<?php

namespace Modules\Account\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $units = Unit::current_branch();

        if ($request->search) {
            $units->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sort') && $request->has('order')) {
            $request->validate([
                'sort' => 'in:name',
                'order' => 'in:asc,desc'
            ]);

            $units->orderBy($request->sort, $request->order);
        }

        $units = $units->paginate(10);

        return response()->json($units);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $unit = new Unit();
        $unit->name = $request->name;
        $unit->branch_office_id = auth()->user()->branch_office_id;
        $unit->save();

        return response()->json($unit);
    }

    public function update(Unit $unit, Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
        abort_if($unit->branch_office_id && $unit->branch_office_id != auth()->user()->branch_office_id, 403);
        $unit->name = $request->name;
        $unit->save();

        return response()->json($unit);
    }

    public function delete(Request $request)
    {
        $unit = Unit::find($request->id);

        # check if product exits
        if (Product::where('unit_id', $unit->id)->exists()) {
            return response()->json(['message' => 'Unit is in use'], 400);
        }
        abort_if($unit->branch_office_id && $unit->branch_office_id != auth()->user()->branch_office_id, 403);

        $unit->delete();
    }
    public function destroy(Unit $unit,Request $request)
    {
        $unit->delete();
    }

}
