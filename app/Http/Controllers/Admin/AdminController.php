<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Generalsetting;
use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\SocialMedia;
use App\Rules\MatchOldPassword;
use App\Rules\IsValidPassword;
use Auth;
use File;
use DB;
use UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(request $request)
    {
        $GraphDataArray = array();

        $activeOrders=Order::Where('status','ordered')->count();
        $cancelOrders=Order::Where('status','cancelled')->count();
        $completedOrders=Order::Where('status','delivered')->count();
        $totalOrders=Order::count();
        $totalOrders_amount = Order::select( DB::raw("SUM(orders.grand_total) as amount"))->first();
        $total_medicines = Product::join('categories','categories.id','products.producttypeid')
        ->whereIn('products.status',['active','review'])
        ->where('categories.name','All Medicines')->count();

        $data['activeorders'] = $activeOrders;
        $data['cancelorders'] = $cancelOrders;
        $data['completedorders'] = $completedOrders;
        $data['totalorders'] = $totalOrders;
        $data['total_medicines'] = $total_medicines;
        $data['totalordersamount'] = $totalOrders_amount->amount;


          //--Get Latest 10 orders coDe--
        $latest_orders = Order::join('user_addresses','orders.address_id','user_addresses.id')
        ->where('status', 'ordered')
        ->orderBy('orders.date', 'desc')
        ->select('orders.*','user_addresses.name')->limit(10)->get();

         //////////////////////Get Orders graph records coDe--
         $dateperiod = date('Y-m-d',strtotime("-6 days"));
         $dateGroup = '%Y-%m-%d';

         if($request->has('GraphType') && $request->GraphType == 'daily'){
             $dateperiod = date('Y-m-d',strtotime("-6 days"));
             $dateGroup = '%Y-%m-%d';
         } elseif($request->has('GraphType') && $request->GraphType == 'weekly'){
             $dateperiod = date('Y-m-d',strtotime("-28 days"));
             $dateGroup = '%V';
         } elseif($request->has('GraphType') && $request->GraphType == 'monthly'){
             $dateperiod = date('Y-m-d',strtotime("-365 days"));
             $dateGroup = '%M';
         }

         $OrdersGraph = Order::select(DB::raw("DATE_FORMAT(orders.date, '$dateGroup') as date"),DB::raw("SUM(orders.grand_total) as amount"))
                             ->where('orders.date', '>=' , $dateperiod)
                             ->groupBy(DB::raw("DATE_FORMAT(orders.date, '$dateGroup')"))
                             ->get();

         $OrdersGraphArray = [];
         foreach($OrdersGraph as $value){
             $OrdersGraphArray[$value->date] = $value->amount;
         }
         $GraphDataArray = array();
         for($i = 0; $i >= -6; $i--){
             $GraphDataArray[date('Y-m-d',strtotime($i." days"))] = ((isset($OrdersGraphArray[date('Y-m-d',strtotime($i." days"))])) ? $OrdersGraphArray[date('Y-m-d',strtotime($i." days"))] : 0 );
         }

        if($request->has('GraphType') && $request->GraphType == 'daily'){
            $GraphDataArray=[];
            for($i = 0; $i >= -6; $i--){
                // for($i = 0; $i <= 7; $i++){
                $GraphDataArray[date('Y-m-d',strtotime($i."days"))] = ((isset($OrdersGraphArray[date('Y-m-d',strtotime($i." days"))])) ? $OrdersGraphArray[date('Y-m-d',strtotime($i." days"))] : 0 );
            }
        } elseif($request->has('GraphType') && $request->GraphType == 'weekly'){
            $GraphDataArray=[];
            for($i = 0; $i >= -3; $i--){
                // for ($i = 1; $i <= 4; $i--) {
                $GraphDataArray['Week '.(abs($i)+1)] = ((isset($OrdersGraphArray[date('W',strtotime($i." weeks"))])) ? $OrdersGraphArray[date('W',strtotime($i." weeks"))] : 0 );
            }
        } elseif($request->has('GraphType') && $request->GraphType == 'monthly'){
            $GraphDataArray=[];
            // for($i = 0; $i > -12; $i--){
            //     $GraphDataArray[date('F',strtotime($i." month"))] = ((isset($OrdersGraphArray[date('F',strtotime($i." month"))])) ? $OrdersGraphArray[date('F',strtotime($i." month"))] : 0 );
            // }
            for ($i = 1; $i < 13; $i++) {
                   $GraphDataArray[date('F',strtotime($i . "month"))] = ((isset($OrdersGraphArray[date('F', strtotime($i . " month"))])) ? $OrdersGraphArray[date('F', strtotime($i . " month"))] : 0);

            }
        }
        $data['GraphDataArray'] = $GraphDataArray;

        return view('admin.index', $data,compact('latest_orders'));
    }

    public function profile()
    {
        if (Auth::guard('admin')->user()) {
            $user_id = Auth::guard('admin')->user()->id;
            $admin = Admin::find($user_id);
            if ($admin) {
                return view('admin.profile.profile', compact('admin'));
            } else {
                return view('admin.notfound_admin')->withErrors('Admin profile details not found.');
            }

        } else {
            return view('admin.notfound_admin')->withErrors('Account is not logged. Please login your account.');
        }
    }

    public function updateprofile(Request $request)
    {
        if (Auth::guard('admin')->user()) {
            $user_id = Auth::guard('admin')->user()->id;

            $Admin = Admin::find($user_id);
            if ($Admin) {
                $this->validate($request, [
                    'name' => 'required',
                    'admin_email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:admins,email,' . $user_id,
                ]);

                $file = $request->file('profile_pic');
                if ($file) {
                    $this->validate($request, [
                        'profile_pic' => 'required|mimes:jpeg,jpg,png,svg|max:2048',
                    ]);
                    if ($Admin->profile_pic != '') {
                        $image_path = public_path('/assets/uploads/admin_profile/') . '/' . $Admin->profile_pic;
                        File::delete($image_path);
                    }
                    $file = $request->file('profile_pic');

                    $fileName = time() . '.' . $request->profile_pic->extension();

                    $request->profile_pic->move(public_path('/assets/uploads/admin_profile/'), $fileName);

                    Admin::find($user_id)->update([
                        'profile_pic' => $fileName,
                        'name' => $request->name,
                        'email' => $request->admin_email,
                        'phone' => $request->phone,
                        'job_title' => $request->job_title,
                        'bio' => $request->bio,
                    ]);
                } else {
                    Admin::find($user_id)->update([
                        'name' => $request->name,
                        'email' => $request->admin_email,
                        'phone' => $request->phone,
                        'job_title' => $request->job_title,
                        'bio' => $request->bio,
                    ]);
                }

                return redirect()->route('admin.profile')->with('success', 'Account updated successfully');
            } else {
                return redirect()->back()->with('error', 'Update failed: Admin profile details not found.');
            }

        } else {
            return redirect()->back()->with('error', 'Update failed: Account is not logged. Please login your account.');
        }
    }

    public function changePassword(Request $request)
    {

        if (Auth::guard('admin')->user()) {
            $userID = Auth::guard('admin')->user()->id;

            $Admin = Admin::find($userID);
            if ($Admin) {
                $request->validate([
                    'current_password' => ['required', new MatchOldPassword],
                    'new_password' => ['required', new IsValidPassword],
                    'confirm_new_password' => ['same:new_password'],
                ]);

                Admin::find($userID)->update(['password' => Hash::make($request->new_password)]);

                return redirect()->route('admin.profile')->with('success', 'Succesfully updated your password');
            } else {
                return redirect()->back()->with('error', 'Update failed: Admin profile details not found.');
            }
        } else {
            return redirect()->back()->with('error', 'Update failed: Account is not logged. Please login your account.');
        }
    }

    public function listAdmin(Request $request)
    {
        $admin = Admin::select('admins.*', 'roles.name as role')
            ->leftjoin('roles', 'roles.id', 'admins.role_id')
            ->where('is_super', '!=', 1)
            ->latest()->paginate(10);

        return view('admin.administrators.list', compact('admin'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $roles = Role::get()->all();
        return view('admin.administrators.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'admin_email' => 'required|unique:admins,email|regex:/(.+)@(.+)\.(.+)/i',
            'password' => ['required',new IsValidPassword]
,            'role' => 'required',
            'licence' => 'nullable|mimes:jpeg,jpg,png,txt,xlx,xls,pdf,,webp|max:2048',
        ]);

        $file = $request->file('profile_pic');
        $licencefile = $request->file('licence');
        $fileName = '';
        $licencefileName = '';

        if ($file) {
            $this->validate($request, [
                'profile_pic' => 'mimes:jpeg,jpg,png,svg|max:2048',
            ]);
            $fileName = time() . '.' . $request->profile_pic->extension();

            $request->profile_pic->move(public_path('assets/uploads/admin_profile/'), $fileName);

        }
        if ($licencefile) {

            $licencefileName = time() . '.' . $request->licence->extension();

            $request->licence->move(public_path('assets/uploads/admin_licence/'), $licencefileName);
        }

        Admin::create([
            'profile_pic' => $fileName,
            'name' => $request->name,
            'email' => $request->admin_email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'job_title' => $request->job_title,
            'bio' => $request->bio,
            'role_id' => $request->role,
            'licence' => $licencefileName,
        ]);

        return redirect()->route('admin.list')->with('success', 'Account updated successfully');
    }

    public function show($id = null)
    {
        $admin = Admin::select('admins.*', 'roles.name as role')->leftJoin('roles', 'admins.role_id', '=', 'roles.id')->where('admins.id', '=', $id)->first();
        return view('admin.administrators.show', compact('admin'));
    }

    public function edit($id = null)
    {
        $admin = Admin::find($id);
        $roles = Role::get()->all();
        return view('admin.administrators.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, $id = null)
    {
        $this->validate($request, [
            'name' => 'required',
            'admin_email' => 'required|unique:admins,email,' . $id . ',|regex:/(.+)@(.+)\.(.+)/i',
            'role' => 'required',
            'password'=>['nullable',new IsValidPassword]
        ]);

        $Admin = Admin::find($id);
        if ($Admin) {
            $file = $request->file('profile_pic');
            $licencefile = $request->file('licence');

            if ($file && $licencefile) {

                $this->validate($request, [
                    'profile_pic' => 'mimes:jpeg,jpg,png,svg|max:2048',
                    'licence' => 'mimes:jpeg,jpg,png,txt,xlx,xls,pdf,webp|max:2048',

                ]);
                if ($Admin->profile_pic != '') {
                    $image_path = public_path('/assets/uploads/admin_profile/') . '/' . $Admin->profile_pic;
                    File::delete($image_path);
                }

                $fileName = time() . '.' . $request->profile_pic->extension();

                $request->profile_pic->move(public_path('/assets/uploads/admin_profile/'), $fileName);

                if ($Admin->licence != '') {
                    $file_path = public_path('/assets/uploads/admin_licence/') . '/' . $Admin->licence;
                    File::delete($file_path);
                }

                $licencefileName = time() . '.' . $request->licence->extension();

                $request->licence->move(public_path('/assets/uploads/admin_profile/'), $licencefileName);

                if ($request->password) {
                    Admin::find($id)->update([
                        'profile_pic' => $fileName,
                        'licence' => $licencefileName,
                        'name' => $request->name,
                        'email' => $request->admin_email,
                        'phone' => $request->phone,
                        'password' => Hash::make($request->password),
                        'job_title' => $request->job_title,
                        'bio' => $request->bio,
                        'role_id' => $request->role,
                    ]);
                } else {
                    Admin::find($id)->update([
                        'profile_pic' => $fileName,
                        'licence' => $licencefileName,
                        'name' => $request->name,
                        'email' => $request->admin_email,
                        'phone' => $request->phone,
                        'job_title' => $request->job_title,
                        'bio' => $request->bio,
                        'role_id' => $request->role,
                    ]);
                }

            } else {

                if ($file) {
                    $this->validate($request, [
                        'profile_pic' => 'mimes:jpeg,jpg,png,svg|max:2048',
                    ]);
                    if ($Admin->profile_pic != '') {
                        $image_path = public_path('/assets/uploads/admin_profile/') . '/' . $Admin->profile_pic;
                        File::delete($image_path);
                    }

                    $file = $request->file('profile_pic');

                    $fileName = time() . '.' . $request->profile_pic->extension();

                    $request->profile_pic->move(public_path('/assets/uploads/admin_profile/'), $fileName);
                    if ($request->password) {
                        Admin::find($id)->update([
                            'profile_pic' => $fileName,
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'password' => Hash::make($request->password),
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    } else {
                        Admin::find($id)->update([
                            'profile_pic' => $fileName,
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    }

                } elseif ($licencefile) {

                    $this->validate($request, [
                        'licence' => 'mimes:jpeg,jpg,png,txt,xlx,xls,pdf,webp|max:2048',
                    ]);
                    if ($Admin->licence != '') {
                        $file_path = public_path('/assets/uploads/admin_licence/') . '/' . $Admin->licence;
                        File::delete($file_path);
                    }

                    $licencefileName = time() . '.' . $request->licence->extension();

                    $request->licence->move(public_path('/assets/uploads/admin_licence/'), $licencefileName);

                    if ($request->password) {
                        Admin::find($id)->update([
                            'licence' => $licencefileName,
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'password' => Hash::make($request->password),
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    } else {
                        Admin::find($id)->update([
                            'licence' => $licencefileName,
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    }

                } else {
                    if ($request->password) {
                        Admin::find($id)->update([
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'password' => Hash::make($request->password),
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    } else {
                        Admin::find($id)->update([
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    }
                }
            }

            return redirect()->route('admin.list')->with('success', 'Account updated successfully');
        } else {
            return redirect()->back()->with('error', 'Update failed: Admin profile details not found. Please back to Admin list page and try to edit again.');
        }
    }

    public function destroy($id = null)
    {
        $Admin = Admin::find($id);
        if ($Admin) {
            if ($Admin->profile_pic != '') {
                $image_path = public_path('/assets/uploads/admin_profile/') . '/' . $Admin->profile_pic;
                File::delete($image_path);
            }
            if ($Admin->licence != '') {
                $licence_path = public_path('/assets/uploads/admin_licence/') . '/' . $Admin->licence;
                File::delete($licence_path);
            }
            Admin::find($id)->delete();

            return redirect()->route('admin.list')->with('success', 'Account deleted successfully');
        } else {
            return redirect()->back()->withErrors('Delete failed: Admin profile details not found.');
        }
    }

    public function settings()
    {
        $settings = Generalsetting::get();

        return view('admin.settings_socialmedia.settings', compact('settings'));
    }

    public function storesettings(Request $request)
    {
        $settings = Generalsetting::all();

        $cnt = 1;
        if ($settings) {
            // 'phone_number' => 'nullable|regex:/[0-9]{9}/',

            $this->validate($request, [
                'notification_email' => 'nullable|regex:/(.+)@(.+)\.(.+)/i',
                'website_url' => 'nullable',
                'company_email' => 'nullable|regex:/(.+)@(.+)\.(.+)/i',
                // 'hotline' => 'nullable|regex:/[0-9]{9}/',
                'site_currency' => 'nullable',
                'site_currency_icon' => 'nullable',
                'compony_address' => 'nullable',
                'shipping_charge_149' => 'required|integer|min:0',
                'shipping_charge_499' => 'nullable|integer|min:0',
            ]);
            foreach ($settings as $items) {
                $fieldname = $items->item;

                $file = $request->file($fieldname);
                if ($file) {
                    //Delete existing image coDe--
                    $gensettings = Generalsetting::find($items->id);
                    if (!empty($gensettings) && $gensettings->value != '') {
                        $file_path = public_path('/assets/uploads/logo/') . $gensettings->value;
                        File::delete($file_path);
                    }

                    $this->validate($request, [
                        $fieldname => 'mimes:jpeg,jpg,png,svg|max:2048',
                    ]);

                    $fileName = 'c_logo_' . $cnt . time() . '.' . $request->$fieldname->extension();
                    $request->$fieldname->move(public_path('/assets/uploads/logo/'), $fileName);

                    Generalsetting::find($items->id)->update(['value' => $fileName]);
                } else if ($items->item != 'company_logo' && $items->item != 'footer_logo') {
                    switch ($items->item) {
                        case $items->item:
                            Generalsetting::find($items->id)->update(['value' => $request->$fieldname]);
                            break;
                    }
                }
                $cnt++;
            }
            return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
        } else {
            return redirect()->route('admin.settings')->withErrors('General settings details not found.');
        }
    }

    public function remove_image(Request $request)
    {
        if ($request->id != '') {
            $Generalsetting = Generalsetting::find($request->id);
            if ($Generalsetting) {
                if ($Generalsetting->value != '') {
                    $imagefile = public_path('/assets/uploads/logo/') . $Generalsetting->value;
                    File::delete($imagefile);
                    Generalsetting::find($request->id)->update(['value' => '']);

                    $returnArray['result'] = true;
                    $returnArray['message'] = 'Image removed successfully.';
                } else {
                    $returnArray['result'] = false;
                    $returnArray['message'] = 'Failed. Image not found.';
                }
            } else {
                $returnArray['result'] = false;
                $returnArray['message'] = 'Failed. Details not found.';
            }
        } else {
            $returnArray['result'] = false;
            $returnArray['message'] = 'Failed. Something went wrong id not found.';
        }
        return response()->json($returnArray);
    }

    public function socialmediaSetting()
    {
        $social_media = SocialMedia::all();
        return view('admin.settings_socialmedia.socialmedia', compact('social_media'));
    }

    public function socialmediaSettingCreate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:social_media,name',
            'link' => 'required|url',
        ]);
        $file = $request->file('icon');
        $icon = '';
        if ($file) {
            $this->validate($request, [
                'icon' => 'mimes:jpeg,jpg,png,webp',
            ]);
            $icon = $fileName = time() . $request->file('icon')->getClientOriginalName();
            $request->icon->move(public_path('assets\uploads\socialmedia'), $fileName);
        } else {
            $icon = $request->icon;
        }
        SocialMedia::create(['name' => $request->name,
            'icon' => $icon,
            'link' => $request->link,
            'type' => $request->file_type,
        ]);
        return redirect('/admin/socialmedia')->with('success', 'Social media created successfully.');
    }

    public function socialmediaSettingUpdate(Request $request)
    {
        if ($request->table_id) {
            $data = SocialMedia::find($request->table_id);
            $this->validate($request, [
                'name' => 'required|unique:social_media,name,' . $request->table_id,
                'link' => 'required|url',

            ]);

            $file = $request->file('icon');
            if ($file) {
                if ($data->icon != '' && $data->type == 'image') {
                    $file_path = public_path('assets/uploads/socialmedia/') . $data->icon;
                    File::delete($file_path);
                }

                $this->validate($request, [
                    'icon' => 'mimes:jpeg,jpg,png,webp',
                ]);
                $fileName = time() . $request->file('icon')->getClientOriginalName();
                $request->icon->move(public_path('assets/uploads/socialmedia/'), $fileName);
            }

            if ($request->file_type == 'image') {
                SocialMedia::where('id', $request->table_id)
                    ->update(['name' => $request->name,
                        'icon' => isset($fileName) ? $fileName : $data->icon,
                        'link' => $request->link,
                        'type' => $request->file_type,
                    ]);
            } else {
                if ($request->icon != null) {
                    if ($data->icon != '' && $data->type == 'image') {
                        $file_path = public_path('assets/uploads/socialmedia/') . $data->icon;
                        File::delete($file_path);
                    }

                    $icon = isset($fileName) ? $fileName : $request->icon;
                } else {
                    $icon = isset($fileName) ? $fileName : $data->icon;
                }

                SocialMedia::where('id', $request->table_id)
                    ->update(['name' => $request->name,
                        'icon' => $icon,
                        'link' => $request->link,
                        'type' => $request->file_type,
                    ]);
            }
            return redirect()->route('admin.socialmedia')->with('success', 'Social media updated successfully');
        } else {
            return redirect()->route('admin.socialmedia')->with('success', 'Social media updated successfully');
        }
    }

    public function socialmediadestroy($id)
    {
        $data = SocialMedia::find($id);
        if ($data) {
            if ($data->icon != '' && $data->type == 'image') {
                $file_path = public_path('assets/uploads/socialmedia/') . $data->icon;
                File::delete($file_path);
            }
            $data->delete();
        }
        return redirect('/admin/socialmedia')->with('success', 'Social media deleted successfully.');
    }

    public function access_restricted()
    {
        return view('admin.notfound_admin')->withErrors('Sorry... You have no permission to accessed.');
    }

}
