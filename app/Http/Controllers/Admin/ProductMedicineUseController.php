<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicineUse;
use App\Models\ProductMedicineuse;
use Illuminate\Http\Request;

class ProductMedicineUseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $Meduse = MedicineUse::select('medicine_uses.*');
        if ($request->has('search_keyword') && $request->has('search_keyword') != '') {
            $Meduse->where('name', 'LIKE', '%' . $request->search_keyword . '%');
        }
        $MedicineUse = $Meduse->orderBy('medicine_uses.name', 'ASC')->paginate(30)->appends(request()->except('page'));
        return view('admin.medicineuse.index', compact('MedicineUse'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function create()
    {
        return view('admin.medicineuse.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:medicine_uses,name',

        ]);

        MedicineUse::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.medicineUse')->with('success', 'Medicine Use added successfully.');
    }

    public function edit($id)
    {
        $MedicineUse = MedicineUse::find($id);
        if ($MedicineUse) {
            return view('admin.medicineuse.edit', compact('MedicineUse'));
        } else {
            return redirect()->back()->withErrors('Sorry.. Medicine Use not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $MedicineUse = MedicineUse::find($id);

        if ($MedicineUse) {
            $request->validate([
                'name' => 'required|unique:medicine_uses,name,' . $id,
            ]);

            MedicineUse::find($id)->update([
                'name' => $request->name,
            ]);

            return redirect()->route('admin.medicineUse')->with('success', 'Medicine Use updated successfully.');
        } else {
            return redirect()->back()->withErrors('Sorry... Updation failed. Supplier not found.');
        }
    }

    public function destroy($id)
    {
        $MedicineUse = MedicineUse::find($id);
        if ($MedicineUse) {
            $MedicineUseexist = ProductMedicineuse::join('products', 'products.id', 'product_medicineuses.product_id')
                ->where('product_medicineuses.medicine_use', $id)
                ->where('products.status', 'active')->exists();

            if (!$MedicineUseexist) {
                MedicineUse::find($id)->delete();
                return redirect()->route('admin.medicineUse')->with('success', 'Medicine Use deleted successfully');
            } else {
                return redirect()->back()->withErrors('Sorry... Delete failed. Medicine Use is existing in products.');
            }
        } else {
            return redirect()->back()->withErrors('Sorry... Delete failed. Medicine Use not found.');
        }
    }
}
