<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\Product;
use App\Models\Barcode;

class ProductController extends Controller
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
        return view('admin.dashboard', compact('products'));
    }

    public function showCreateForm() {
        return view('admin.create');
    }

    public function create(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku_code' => 'required|string|max:255|unique:products,sku_code',
            'barcode_number' => 'required|string|max:255|unique:barcodes,barcode_number',
        ]);

        if ($imageFile = $request->file('image')) {
            $imageName = time() . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('assets/images'), $imageName);
        } else {
            return redirect()->back()->withErrors(['image' => 'No image uploaded.']);
        }

        $product = Product::create([
            'image' => 'images/' . $imageName,
            'sku_code' => strtoupper($request->sku_code),
        ]);

        // 确保 barcode_number 存在
        if (empty($request->barcode_number)) {
            return redirect()->back()->withErrors(['barcode' => 'Barcode number is required.']);
        }

        // 创建条形码目录（如果不存在）
        $barcodeFolder = public_path('assets/barcodes');
        if (!is_dir($barcodeFolder) && !mkdir($barcodeFolder, 0777, true) && !is_dir($barcodeFolder)) {
            return redirect()->back()->withErrors(['barcode' => 'Failed to create barcode directory.']);
        }

        // 处理 SKU 码，防止特殊字符导致文件名错误
        $sanitizedSkuCode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->sku_code);
        $barcodeImageName = $sanitizedSkuCode . '_' . time() . uniqid() . '.png';
        $barcodePath = $barcodeFolder . '/' . $barcodeImageName;

        // 生成条形码（使用 barcode_number 作为条形码数据）
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($request->barcode_number, $generator::TYPE_CODE_128, 3, 50);

        // 确保文件写入成功
        if (file_put_contents($barcodePath, $barcodeData) === false) {
            return redirect()->back()->withErrors(['barcode' => 'Failed to generate barcode image.']);
        }

        // 存入数据库
        $barcode = Barcode::create([
            'barcode_image' => 'barcodes/' . $barcodeImageName,
            'barcode_number' => $request->barcode_number,
            'product_id' => $product->id,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Product created successfully');
    }

    public function view($id) {
        $product = Product::with('barcode')->findOrFail($id);
        return view('admin.view', compact('product'));
    }

    public function showUpdateForm($id) {
        $product = Product::with('barcode')->findOrFail($id);
        return view('admin.update', compact('product'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku_code' => 'required|string|max:255|unique:products,sku_code,' . $id,
            'barcode_number' => 'required|string|max:255|unique:barcodes,barcode_number,' . $id . ',product_id',
        ]);

        $product = Product::findOrFail($id);
        $oldSkuCode = $product->sku_code;

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path('assets/' . $product->image))) {
                unlink(public_path('assets/' . $product->image));
            }

            $imageName = time() . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('assets/images'), $imageName);
            $product->image = 'images/' . $imageName;
        }

        $product->sku_code = strtoupper($request->sku_code);
        $product->save();

        $barcode = Barcode::where('product_id', $id)->first();
        $barcodeFolder = public_path('assets/barcodes');

       // 确保条形码目录存在
        if (!is_dir($barcodeFolder) && !mkdir($barcodeFolder, 0777, true) && !is_dir($barcodeFolder)) {
            return redirect()->back()->withErrors(['barcode' => 'Failed to create barcode directory.']);
        }

        // 检查 `sku_code` 或 `barcode_number` 是否变更
        $shouldGenerateNewBarcode = !$barcode || $barcode->barcode_number !== $request->barcode_number || $oldSkuCode !== $request->sku_code;

        if (!$barcode) {
            $barcode = new Barcode();
            $barcode->product_id = $id;
        } elseif ($shouldGenerateNewBarcode) {
            // 删除旧的条形码图片
            if ($barcode->barcode_image && file_exists(public_path('assets/' . $barcode->barcode_image))) {
                unlink(public_path('assets/' . $barcode->barcode_image));
            }

            // 生成新条形码
            $sanitizedSkuCode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->sku_code);
            $barcodeImageName = $sanitizedSkuCode . '_' . time() . uniqid() . '.png';
            $barcodePath = $barcodeFolder . '/' . $barcodeImageName;

            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode($request->barcode_number, $generator::TYPE_CODE_128, 3, 50);
            file_put_contents($barcodePath, $barcodeData);

            $barcode->barcode_image = 'barcodes/' . $barcodeImageName;
        }

        // ✅ 更新 `barcode_number`
        $barcode->barcode_number = $request->barcode_number;
        $barcode->save();

        return redirect()->route('product.view', $id)->with('success', 'Product updated successfully.');
    }

    public function showUploadForm($id) {
        $product = Product::with('barcode')->findOrFail($id);
        return view('admin.upload', compact('product'));
    }

    public function upload(Request $request, $id) {
        $request->validate([
            'barcode_number' => 'required|string|max:255|unique:barcodes,barcode_number,' . $id,
        ]);

        $product = Product::findOrFail($id);

        $sku_code = strtoupper($product->sku_code);

        // 查找条形码数据
        $barcode = Barcode::where('product_id', $id)->first();

        // 如果已有条形码数据，则删除旧的条形码图片
        if ($barcode && $barcode->barcode_image) {
            $oldBarcodePath = public_path('assets/' . $barcode->barcode_image);
            if (file_exists($oldBarcodePath)) {
                unlink($oldBarcodePath);
            }
        } else {
            // 如果条形码不存在，创建一个新的
            $barcode = new Barcode();
            $barcode->product_id = $id;
        }

        // 生成新的条形码
        $barcodeFolder = public_path('assets/barcodes');
        if (!is_dir($barcodeFolder)) {
            mkdir($barcodeFolder, 0777, true);
        }

        // 处理新条码文件名
        $sanitizedSkuCode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $sku_code);
        $barcodeImageName = $sanitizedSkuCode . '_' . time() . uniqid() . '.png';
        $barcodePath = $barcodeFolder . '/' . $barcodeImageName;

        // 生成新的条形码图片
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($request->barcode_number, $generator::TYPE_CODE_128, 3, 50);
        file_put_contents($barcodePath, $barcodeData);

        // 更新数据库
        $barcode->barcode_number = $request->barcode_number;
        $barcode->barcode_image = 'barcodes/' . $barcodeImageName;
        $barcode->save();

        return redirect()->route('admin.dashboard')->with('success', 'Barcode updated successfully');
    }

    public function destroy($id) {
        $product = Product::findOrFail($id);

        // 删除产品图片
        if ($product->image && file_exists(public_path('assets/' . $product->image))) {
            unlink(public_path('assets/' . $product->image));
        }

        // 查找关联的条形码
        $barcode = Barcode::where('product_id', $product->id)->first();

        if ($barcode) {
            // 删除条形码图片（修正 empty() 逻辑）
            if (!empty($barcode->barcode_image) && file_exists(public_path('assets/' . $barcode->barcode_image))) {
                unlink(public_path('assets/' . $barcode->barcode_image));
            }

            // 删除条形码记录
            $barcode->delete();
        }

        // 删除产品记录
        $product->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Product and its barcode deleted successfully.');
    }
}
