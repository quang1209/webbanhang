<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Export;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminInventoryController extends Controller
{
	/**
	 * Nhập kho
	 */
    public function getWarehousing()
	{
        $warehouses =  DB::table('products')
        ->join('warehouses', 'products.id','=','warehouses.w_product_id')
        ->paginate(10);
        $viewData = [
			    'warehouses' => $warehouses,

            ];

		return view('admin.inventory.import', $viewData);
	}

	public function add()
    {
        $products = Product::all();
        return view('admin.inventory.import_add', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        DB::beginTransaction();
        try {
            $product = Product::find($request->w_product_id);
            $product->pro_number = $product->pro_number + $request->w_qty;
            $product->save();
            DB::commit();
            Warehouse::create($data);
            return redirect()->route('admin.inventory.warehousing')->with('success', 'Thêm mới thành công');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi lưu dữ liệu');
        }
    }

    public function edit($id)
    {
        $warehouse = Warehouse::find($id);

        $products = Product::all();
        return view('admin.inventory.import_update', compact('products','warehouse'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->except('_token');
        DB::beginTransaction();
        try {
            $warehouse = Warehouse::find($id);
            $product = Product::find($request->w_product_id);
            $product->pro_number = $product->pro_number - $warehouse['w_qty'] + $request->w_qty;
            $product->save();
            $warehouse->fill($data)->save();
            DB::commit();
            return redirect()->route('admin.inventory.warehousing')->with('success', 'Thêm mới thành công');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi lưu dữ liệu');
        }
    }

    public function delete(Request $request,$id)
    {
        Warehouse::find($id)->delete();
        return redirect()->route('admin.inventory.warehousing');
    }

	/**
	 * Xuất kho
	 */
	public function getOutOfStock(Request $request)
	{
        $inventoryExport = Order::with('product');

        if ($request->time) {
            $time = $this->getStartEndTime($request->time,[]);
            $inventoryExport->whereBetween('created_at', $time);
        }

        $inventoryExport = $inventoryExport->orderByDesc('id')
            ->paginate(20);

        $viewData = [
            'inventoryExport' => $inventoryExport,
            'query' => $request->query()
        ];

        return view('admin.inventory.export', $viewData);
	}


}
