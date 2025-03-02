<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function getData()
    {
        $products = Product::select(['id', 'nama_produk', 'created_at', 'updated_at']);
        return DataTables::of($products)
            ->addColumn('action', function ($products) {
                return '<a href="#" class="btn btn-sm btn-warning edit" data-id="' . $products->id . '"><i class="fa fa-edit"></i> Edit</a> 
                        <a href="#" class="btn btn-sm btn-danger delete" data-id="' . $products->id . '"><i class="fa fa-trash"></i> Delete</a>';
            })
            ->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $products = new Product();
        $products->nama_produk = $request->name;
        $products->save();

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $products = Product::findOrFail($id);
        return response()->json($products);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $products = Product::findOrFail($id);
        $products->nama_produk = $request->name;
        $products->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $products = Product::findOrFail($id);
        $products->delete();

        return response()->json(['success' => true]);
    }
}
