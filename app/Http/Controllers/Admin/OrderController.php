<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BulkExport;
use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PaymentDetail;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Seshac\Shiprocket\Shiprocket;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function orderlist(Request $request)
    {

        $productname = '';
        $orders = Order::select('orders.*', 'user_addresses.name','payment_details.payment_gateway')
            ->join('user_addresses', 'orders.address_id', 'user_addresses.id')
            ->leftjoin('payment_details','payment_details.order_id','orders.id');

        if ($request->has('filter_status') && $request->filter_status != '') {
            $orders->where('orders.status', $request->filter_status);
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

        $ordersList = $orders->latest()->paginate(30);

        return view('admin.orders.orders_list', compact('ordersList', 'productname'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function orderDetails(Request $request, $orderId = null)
    {
        if ($orderId) {
            $order_master = Order::select('orders.*', 'user_addresses.email', 'user_addresses.name', 'user_addresses.phone', 'user_addresses.pin', 'user_addresses.location',
                'user_addresses.address', 'user_addresses.city', 'user_addresses.landmark', 'states.name as state_name', 'countries.name as country_name','payment_details.payment_gateway',
                 'stores.name as store_name', 'stores.location as store_location', 'stores.address as store_address', 'stores.contact_number as store_contact_number', 'stores.map_location_code as store_location_map')
                ->join('user_addresses', 'orders.address_id', 'user_addresses.id')
                ->join('countries', 'user_addresses.country_id', 'countries.id')
                ->join('states', 'user_addresses.state_id', 'states.id')
                ->leftjoin('stores', 'stores.id', 'orders.store_id')

                ->leftjoin('payment_details','payment_details.order_id','orders.id')

                ->where('orders.id', $orderId)->first();

            if ($order_master) {
                $order_details = OrderDetails::select('order_details.id', 'order_details.quantity', 'order_details.quantity', 'order_details.total_tax', 'order_details.price', 'order_details.amount',
                    'order_details.status', 'order_details.status_on', 'products.product_name', 'product_images.product_image')
                    ->join('products', 'order_details.product_id', 'products.id')
                    ->leftjoin('product_images', 'products.thumbnail', 'product_images.id')
                    ->where('order_details.order_id', $order_master->id)
                    ->get();
                $prescription_ids = array();
                if ($order_master->prescription_ids) {
                    $prescription_ids = explode(',', $order_master->prescription_ids);

                }
                $prescription_data = Prescription::select('prescriptions.*', 'users.name as customername', 'products.product_name', 'products.product_url', 'admins.name as approved_by')
                    ->join('users', 'users.id', 'prescriptions.user_id')->leftjoin('products', 'products.id', 'prescriptions.product_id')
                    ->leftjoin('admins', 'admins.id', 'prescriptions.approved_by');
                if ($request->status == 'rejected') {
                    $prescription_data->where('prescriptions.status', 0);
                } elseif ($request->status == 'approved') {
                    $prescription_data->where('prescriptions.status', 2);
                } elseif ($request->status == 'completed') {
                    $prescription_data->where('prescriptions.status', 3);
                } else {
                    $prescription_data->where('prescriptions.status', 1);
                }

                $prescription_data = $prescription_data->whereIn('prescriptions.id', $prescription_ids)->latest()->paginate(30);

                $orderArray['order'] = $order_master;
                $orderArray['details'] = $order_details;

                return view('admin.orders.order_details', compact('orderArray', 'prescription_data'));
            } else {
                return view('admin.notfound_admin')->withErrors('Order details not found. Please go back and choose order again.');
            }
        } else {
            return view('admin.notfound_admin')->withErrors('Invalid id.');
        }
    }

    public function changeOrderStatus(int $id, Request $request)
    {

        $order = Order::findOrFail($id);

        if ($order) {
            $prescription_not_approved=Prescription::where('order_id',$id)->whereIn('status',[0,1])->exists();
            if($prescription_not_approved&& !in_array($request->status,['ordered','cancelled'])){
             return redirect()->route('admin.order.details',$id)->with('errors', 'Please approve added prescriptions before'.' '.$request->status);

            }else {
            $order->status = $request->status;
            $order->save();
            // $prescription_array=array();
            // if($order->prescription_ids!=''){
            // $prescription_array=explode(',',$order->prescription_ids);
            // }

            // if(!empty($prescription_array)){
            //     Prescription::whereIn('id',$prescription_array)->where('status',1);
            // }

            OrderDetails::where('order_id', $id)->where('status', '<>', 'cancelled')
                ->update(['status' => $request->status, 'status_on' => date('Y-m-d H:i:s')]);
            $user_id = $order->user_id;

            $settings = Generalsetting::where('item', '=', 'notification_email')->first();
            if ($settings) {

                $UserDetails = User::where('id', $user_id)->first();

                // Order Cancelled notification mail to customer--
                if ($UserDetails) {
                    Mail::send('email.order_cancellationMail',
                        array(
                            'mode' => 'Admin_Manageorder',
                            'usertype' => 'Customer',
                            'customername' => $UserDetails->name,
                            'status' => $request->status,
                            // 'productname' => $ProductDetails->product_name,
                            'storename' => '',
                            'orderid' => $order->id,
                            'subject' => 'Your order successfully' . ' ' . $request->status . '.' . ' Order ID : ' . $id,
                        ), function ($message) use ($settings, $UserDetails, $request) {
                            $message->from($settings->value, 'Expressmed');
                            $message->to($UserDetails->email);
                            $message->subject('You order successfully' . ' ' . $request->status);
                        });
                }

                // Mail::send('email.mail_notification',
                // array(
                //     'mode' => 'Customer_Manageorder',
                //     'usertype' => 'Customer',
                //     'customername' => $UserDetails->name,
                //     'status' => $request->status,
                //     'productname' => $ProductDetails->product_name,
                //     'storename' => '',
                //     'orderid' => $order->id,
                //     'subject' => 'Your order successfully ' . $request->status . ' Order ID : ' . $id,
                // ), function ($message) use ($settings, $UserDetails) {
                //     $message->from($settings->value, 'Expressmed');
                //     $message->to($UserDetails->email);
                //     $message->subject('You successfully cancelled the product ' . $ProductDetails->product_name);
                // });

                //     // Order Cancelled notification mail to admin--
                //     Mail::send('email.mail_notification',
                //     array(
                //         'mode' => 'Customer_Manageorder',
                //         'usertype' => 'Vendor',
                //         'customername' => $UserDetails->name,
                //         'status' => $status,
                //         'productname' => $ProductDetails->product_name,
                //         'storename' => $ProductDetails->storename,
                //         'orderid' => $request->order_id,
                //         'subject' => 'Customer successfully '.$statusrequest.' Order ID : '.$request->order_id,
                //     ), function($message) use ($settings, $ProductDetails) {
                //         $message->from($settings->value, 'ShopeOn');
                //         $message->to($ProductDetails->seller_mail);
                //         $message->subject('Notification Mail: Customer successfully cancelled a product '.$ProductDetails->product_name);
                //     });
                //     //Notification mail to admin /--
                // }
            }

            return back()->with('success', 'Order Status successfully changed');
        }
    }
    }

    public function changeOrderdetailStatus(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'orderDetailsId' => 'required|numeric',
            'orderId' => 'required|numeric',
        ], [
            'orderDetailsId.required' => 'Order details id is required.',
            'orderDetailsId.numeric' => 'Order details id must be numeric.',
            'orderId.required' => 'Order id is required.',
            'orderId.numeric' => 'Order id must be numeric.',
        ]);

        if ($validate->fails()) {
            return response()->json(['result' => false, 'message' => $validate->errors()->first()]);
        } else {
            $orderDetails = OrderDetails::where('id', $request->orderDetailsId)->where('order_id', $request->orderId)->where('status', 'ordered')->first();
            if ($orderDetails) {
                OrderDetails::where('id', $request->orderDetailsId)->where('order_id', $request->orderId)->where('status', 'ordered')
                    ->update(['status' => 'cancelled', 'status_on' => date('Y-m-d H:i:s')]);

                Order::where('id', $request->orderId)->update([
                    'total_amount' => DB::raw('total_amount -' . $orderDetails->amount),
                    'grand_total' => DB::raw('grand_total -' . $orderDetails->amount),
                ]);

                $ExistOrderDetails = OrderDetails::where('order_id', $request->orderId)->where('status', '!=', 'cancelled')->exists();

                if (!$ExistOrderDetails) {
                    Order::find($request->orderId)->update(['status' => 'cancelled']);
                }

                // Notification mail to customer and admin--
                // pending...

                return response()->json(['result' => true, 'message' => 'The order item cancelled successfully.']);
            } else {
                return response()->json(['result' => false, 'message' => 'You cannot cancel the order item.']);
            }
        }
    }

    public function print_invoiceOrder($orderID = null)
    {
        $orders = array();
        $userType = "customer";
        if ($orderID) {

            //Get order details coDe--
            $order_Master = Order::join('user_addresses', 'orders.address_id', 'user_addresses.id')
                ->join('countries', 'user_addresses.country_id', 'countries.id')
                ->join('states', 'user_addresses.state_id', 'states.id')
                ->leftjoin('stores', 'stores.id', 'orders.store_id')

                ->where('orders.id', $orderID)
                ->select('orders.*', 'user_addresses.name', 'user_addresses.address', 'user_addresses.phone', 'user_addresses.pin', 'user_addresses.location', 'user_addresses.city',
                'user_addresses.landmark', 'user_addresses.email', 'states.name as state_name', 'countries.name as country_name', 'stores.name as store_name', 'stores.location as store_location', 'stores.address as store_address', 'stores.contact_number as store_contact_number', 'stores.map_location_code as store_location_map')
                ->first();
            if ($order_Master) {
                $order_details = OrderDetails::join('products', 'products.id', 'order_details.product_id')
                    ->where('order_details.order_id', $order_Master->id)
                    ->select('order_details.*', 'products.product_name')
                    ->get();

                if ($order_details) {
                    $orders = new \stdClass();
                    $orders->order_id = $order_Master->id;
                    $orders->order_date = $order_Master->date;
                    $orders->store_name = $order_Master->store_name;
                    $orders->store_location = $order_Master->store_location;
                    $orders->store_address = $order_Master->store_address;
                    $orders->store_contact_number = $order_Master->store_contact_number;
                    $orders->store_location_map = $order_Master->store_location_map;
                    $orders->name = $order_Master->name;
                    $orders->address = $order_Master->address;
                    $orders->phone = $order_Master->phone;
                    $orders->email = $order_Master->email;
                    $orders->pin = $order_Master->pin;
                    $orders->city = $order_Master->city;
                    $orders->location = $order_Master->location;
                    $orders->landmark = $order_Master->landmark;
                    $orders->state_name = $order_Master->state_name;
                    $orders->country_name = $order_Master->country_name;

                    $orders->status = $order_Master->status;
                    $orders->total_amount = $order_Master->total_amount;
                    $orders->grand_total = $order_Master->grand_total;
                    $orders->delivery_type = $order_Master->delivery_type;
                    $orders->total_tax_amount = $order_Master->total_tax_amount;
                    $orders->shipping_charge = $order_Master->shipping_charge;
                    $orders->order_details = $order_details;

                    return view('invoice_print', compact('orders', 'userType'));
                } else {
                    return view('invoice_print', compact('orders', 'userType'))->with('error', 'Order details not found.');
                }
            } else {
                return view('invoice_print', compact('orders', 'userType'))->with('error', 'Error: Order details not found.');
            }
        } else {
            return view('invoice_print', compact('orders', 'userType'))->with('error', 'Invalid request.');
        }
    }

    public function tracking_details($orderID = null)
    {
        $token = Shiprocket::getToken();

        if ($orderID) {
            $orders = Order::join('order_details', 'order_details.order_id', 'orders.id')
                ->select('orders.*')->where('orders.id', '=', $orderID)->first();

            if ($orders) {
                if ($orders->shipment_id != '') {

                    $track_details = Shiprocket::track($token)->throwShipmentId($orders->shipment_id);

                    return view('track_order', compact('track_details'));

                } else {
                    return view('track_order')->with('error', 'Error: Shipment details not found.');
                }
            } else {
                return view('track_order')->with('error', 'Error: Order details not found.');
            }
        } else {
            return view('track_order')->with('error', 'Invalid request.');
        }

    }
    public function get_student_data()
    {
        $productname = '';
        $orders = Order::select('orders.*', 'user_addresses.name')
            ->join('user_addresses', 'orders.address_id', 'user_addresses.id');

        if ($request->has('filter_status') && $request->filter_status != '') {
            $orders->where('orders.status', $request->filter_status);
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

        $ordersList = $orders->latest()->get();
        return Excel::download(new BulkExport($ordersList), 'students.xlsx');
    }
}
