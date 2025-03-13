<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Barcode;

class GuestController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $query = Product::with('barcode');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('sku_code', 'like', '%' . $search . '%')
                      ->orWhereHas('barcode', function ($q) use ($search) {
                          $q->where('barcode_number', 'like', '%' . $search . '%');
                      });
            }

            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $products = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $products->total(),
                'recordsFiltered' => $products->total(),
                'data' => $products->items(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ]);
        }

        $products = Product::with('barcode')->get();
        return view('dashboard', compact('products'));
    }

    public function view($id) {
        $product = Product::with('barcode')->findOrFail($id);
        return view('view', compact('product'));
    }
}
