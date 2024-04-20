<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrderExport;
use App\Exports\ProductsExport;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Productbrand;
use App\Models\Productcontent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function productreports(request $request)
    {
        $products = Product::select('products.*', 'product_images.product_image', 'type1.name as producttype', 'type2.name as category', 'productbrands.name as brand', 'product_manufacturers.name as manufacturer')
            ->leftJoin('product_images', 'product_images.id', '=', 'products.thumbnail')
            ->leftjoin('producttypes', 'producttypes.id', 'products.producttypeid')
            ->leftJoin('productbrands', 'productbrands.id', '=', 'products.brands')
            ->leftJoin('categories as type1', 'type1.id', '=', 'products.producttypeid')
            ->leftJoin('categories as type2', 'type2.id', '=', 'products.category_id')
            ->leftJoin('medicine_uses', 'medicine_uses.id', '=', 'products.medicine_use')
            ->leftJoin('product_manufacturers', 'product_manufacturers.id', '=', 'products.manufacturer');

        if ($request->has('filter_type') && $request->filter_type != '') {
            $products->where('products.producttypeid', '=', $request->filter_type);
        }
        if ($request->has('filter_category') && $request->filter_category != '') {
            $products->where('products.category_id', '=', $request->filter_category);
        }
        if ($request->has('filter_status') && $request->filter_status != '') {
            $status = $request->filter_status;
            if ($status == 'active' || $status == 'review') {

                $products->where('products.status', '=', $request->filter_status);
            } elseif ($status == 'sold-out') {

                $products->where('products.flag', 1);
            } elseif ($status == 'hidden') {

                $products->where('products.hide_from_site', 1);
            }
        } else {
            $products->whereIn('products.status', ['active', 'review']);
        }

        if ($request->has('price_filter') && $request->price_filter != '') {
            $price_range = $request->price_filter;
            global $first_amount;
            global $second_amt;
            if ($price_range != '') {
                $price_set = (explode("-", $price_range));
                $first_amount = $price_set[0];
                $second_amt = $price_set[1];
            }
            $products->whereBetween('products.price', [$first_amount, $second_amt]);
        }
        if ($request->has('search_term') && $request->search_term != '') {
            $pc_id = '';
            $content_ids = Productcontent::select('productcontents.id')
                ->where('productcontents.name', 'LIKE', '%' . $request->search_term . '%')->get();

            $products->where(function ($query) use ($request, $content_ids, $products) {
                $query->where('products.product_name', 'LIKE', '%' . $request->search_term . '%')
                    ->orWhere('medicine_uses.name', 'LIKE', '%' . $request->search_term . '%')
                    ->orWhere('product_manufacturers.name', 'LIKE', '%' . $request->search_term . '%');
                if (count($content_ids) > 0) {
                    foreach ($content_ids as $content_id) {
                        $products->orWhereRaw("find_in_set('" . $content_id->id . "',products.productcontent_id)");
                    }
                }

            });

        }

        $products->orderBy('products.product_name', 'asc');

        $brand = Productbrand::orderBy('name', 'asc')->get();
        $category = Category::orderBy('name', 'asc')->get();
        // $Producttypes = Producttype::orderBy('type', 'asc')->orderBy('producttype', 'asc')->get();
        $Producttypes = Category::where('parent_id', 0)->orderBy('name', 'asc')->get();

        if ($request->has('export')) {
            $products = $products->get();
            return Excel::download(new ProductsExport($products), 'products_data_export.xlsx');
        } else {
            $products = $products->paginate(30);

            return view('admin.reports.product_reports', compact('products', 'brand', 'category', 'Producttypes'))->with('i', (request()->input('page', 1) - 1) * 30);
        }
    }

    public function order_reports(request $request)
    {

        $productname = '';
        $orders = Order::select('orders.*', 'user_addresses.name')
            ->join('user_addresses', 'orders.address_id', 'user_addresses.id');


        if ($request->has('filter_status') && $request->filter_status != '') {
            $orders->where('orders.status', $request->filter_status);
        }
        if ($request->has('date_from') && $request->date_from != '') {
            $orders->where('orders.date', '>=', date('Y-m-d', strtotime("0 day", strtotime($request->date_from))));
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $orders->where('orders.date', '<=', date('Y-m-d', strtotime("+1 day", strtotime($request->date_to))));
        }

        if ($request->has('productid') && $request->productid != '' && $request->productid != 0) {
            $orders->whereIn('orders.id', function ($query) use ($request) {
                $query->select('order_details.order_id')
                    ->from('order_details')
                    ->where('order_details.product_id', $request->productid)
                    ->get();
            });
            $productname = Product::find($request->productid)->product_name;
        }
        if ($request->has('userid') && $request->userid != '' && $request->userid != 0) {
            $orders->where('orders.user_id', $request->userid);
        }
        if ($request->has('export')) {
            $orders = $orders->latest()->get();
            return Excel::download(new OrderExport($orders), 'Order_report_export.xlsx');
        } else {
            $ordersList = $orders->latest()->paginate(30);

            return view('admin.reports.order_reports ', compact('ordersList', 'productname'))->with('i', (request()->input('page', 1) - 1) * 30);
        }
    }
    public function sales_reports(request $request)
    {

        $productname = '';
        $sales = Order::select('orders.*','products.product_name','user_addresses.name')
            ->join('user_addresses', 'orders.address_id', 'user_addresses.id')
            ->join('order_details','order_details.order_id','orders.id')
            ->join('products','products.id','order_details.product_id');
        if ($request->has('filter_status') && $request->filter_status != '') {
            $sales->where('orders.status', $request->filter_status);
        }
        if ($request->has('date_from') && $request->date_from != '') {
            $sales->where('orders.date', '>=', date('Y-m-d', strtotime("0 day", strtotime($request->date_from))));
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $sales->where('orders.date', '<=', date('Y-m-d', strtotime("+1 day", strtotime($request->date_to))));
        }

        if ($request->has('productid') && $request->productid != '' && $request->productid != 0) {
            $sales->whereIn('orders.id', function ($query) use ($request) {
                $query->select('order_details.order_id')
                    ->from('order_details')
                    ->where('order_details.product_id', $request->productid)
                    ->get();
            });
            $productname = Product::find($request->productid)->product_name;
        }
        if ($request->has('userid') && $request->userid != '' && $request->userid != 0) {
            $sales->where('orders.user_id', $request->userid);
        }
        if ($request->has('export')) {

            $sales = $sales->latest()->groupBy('orders.id')->get();
            return Excel::download(new SalesExport($sales), 'Sales_report_export.xlsx');
        } else {
            $salesList = $sales->latest()->groupBy('orders.id')->paginate(30);

            return view('admin.reports.sales_reports ', compact('salesList', 'productname'))->with('i', (request()->input('page', 1) - 1) * 30);
        }
    }

}
