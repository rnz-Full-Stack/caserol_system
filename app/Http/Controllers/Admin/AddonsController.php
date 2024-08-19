<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AddonsController extends Controller
{
    // Render admin/addons/addons.blade.php page in the Admin Panel
    public function addons()
    {
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'addons');


        // Get ONLY the addons that BELONG TO the 'vendor' to show them up in (not ALL addons show up) in addons.blade.php, and also make sure that the 'vendor' account is active/enabled/approved (`status` is 1) before they can access the products page
        $adminType = Auth::guard('admin')->user()->type;      // `type`      is the column in `admins` table    // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances    // Retrieving The Authenticated User and getting their `type`      column in `admins` table    // https://laravel.com/docs/9.x/authentication#retrieving-the-authenticated-user
        $vendor_id = Auth::guard('admin')->user()->vendor_id; // `vendor_id` is the column in `admins` table    // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances    // Retrieving The Authenticated User and getting their `vendor_id` column in `admins` table    // https://laravel.com/docs/9.x/authentication#retrieving-the-authenticated-user

        if ($adminType == 'vendor') { // if the authenticated user (the logged in user) is 'vendor', check his `status`
            $vendorStatus = Auth::guard('admin')->user()->status; // `status` is the column in `admins` table    // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances    // Retrieving The Authenticated User and getting their `status` column in `admins` table    // https://laravel.com/docs/9.x/authentication#retrieving-the-authenticated-user
            // dd($vendorStatus);
            if ($vendorStatus == 0) { // if the 'vendor' is inactive/disabled
                return redirect('admin/update-vendor-details/personal')->with('error_message', 'Your Vendor Account is not approved yet. Please make sure to fill your valid personal, business and bank details'); // the error_message will appear to the vendor in the route: 'admin/update-vendor-details/personal' which is the update_vendor_details.blade.php page
            }

            $addons = \App\Models\Addon::where('vendor_id', $vendor_id)
            ->join('categories', 'addons.categories', '=', 'categories.id')
            ->select('addons.*', 'categories.category_name')
            ->get()->toArray(); // Get ONLY the addons that BELONG TO the vendor

        } else { // if the $adminType is 'admin'
            $addons = \App\Models\Addon::get()->toArray();
            // dd($addons);
        }


        return view('admin.addons.addons')->with(compact('addons'));
    }

    // Update Addon Status (active/inactive) via AJAX in admin/addons/addons.blade.php, check admin/js/custom.js
    public function updateAddonStatus(Request $request)
    {
        if ($request->ajax()) { // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)
            // dd($data);

            if ($data['status'] == 'Active') { // $data['status'] comes from the 'data' object inside the $.ajax() method    // reverse the 'status' from (ative/inactive) 0 to 1 and 1 to 0 (and vice versa)
                $status = 0;
            } else {
                $status = 1;
            }


            \App\Models\Addon::where('id', $data['addon_id'])->update(['status' => $status]); // $data['addon_id'] comes from the 'data' object inside the $.ajax() method
            // echo '<pre>', var_dump($data), '</pre>';

            return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                'status' => $status,
                'addon_id' => $data['addon_id']
            ]);
        }
    }

    // Delete a Addon via AJAX in admin/addons/addons.blade.php, check admin/js/custom.js
    public function deleteAddon($id)
    {
        \App\Models\Addon::where('id', $id)->delete();

        $message = 'Addon has been deleted successfully!';

        return redirect()->back()->with('success_message', $message);
    }

    // Render admin/addons/add_edit_addon.blade.php page with 'GET' request ('Edit/Upate the Addon') if the {id?} Optional Parameter is passed, or if it's not passed, it's a GET request too to 'Add a Addon', or it's a POST request for the HTML Form submission in the same page
    public function addEditAddon(Request $request, $id = null)
    { // the slug (Route Parameter) {id?} is an Optional Parameter, so if it's passed, this means 'Edit/Update the Addon', and if not passed, this means' Add a Addon'    // GET request to render the add_edit_addon.blade.php view (whether Add or Edit depending on passing or not passing the Optional Parameter {id?}), and POST request to submit the <form> in that same page    // {id?} Optional Parameters: https://laravel.com/docs/9.x/routing#parameters-optional-parameters
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'addons');


        if ($id == '') { // if there's no $id is passed in the route/URL parameters (Optional Parameters {id?}), this means 'Add a new Addon'
            // Add a new Addon
            $title = 'Add Addon';
            $addon = new \App\Models\Addon;
            // dd($addon);

            $selCats = array();

            $message = 'Addon added successfully!';

        } else { // if the $id is passed in the route/URL parameters (Optional Parameters {id?}), this means 'Edit/Update the Addon'
            // Edit/Update the Addon
            $title = 'Edit Addon';
            $addon = \App\Models\Addon::find($id);
            // dd($addon);

            $selCats = explode(',', $addon['categories']); // selected categories

            $message = 'Addon updated successfully!';
        }



        if ($request->isMethod('post')) { // if the HTML Form is submitted (WHETHER Add or Update!)
            $data = $request->all();
            //dd($data);


            // Laravel's Validation    // Customizing Laravel's Validation Error Messages: https://laravel.com/docs/9.x/validation#customizing-the-error-messages    // Customizing Validation Rules: https://laravel.com/docs/9.x/validation#custom-validation-rules
            $rules = [
                'categories' => 'required',
                'addon_option' => 'required',
                'addon_type' => 'required',
                'amount_type' => 'required',
                'amount' => 'required|numeric',
            ];

            $customMessages = [ // Specifying A Custom Message For A Given Attribute: https://laravel.com/docs/9.x/validation#specifying-a-custom-message-for-a-given-attribute
                'categories.required' => 'Select Categories',
                'addon_option.required' => 'Select Addon Option',
                'addon_type.required' => 'Select Addon Type',
                'amount_type.required' => 'Select Amount Type',
                'amount.required' => 'Enter Amount',
                'amount.numeric' => 'Enter Valid Amount',
            ];

            $this->validate($request, $rules, $customMessages);



            if (isset($data['categories'])) {
                $categories = implode(',', $data['categories']);
            } else {
                $categories = '';
            }


            $addon_code = \Illuminate\Support\Str::random(8); // Str::random(): https://laravel.com/docs/9.x/helpers#method-str-random




            $adminType = Auth::guard('admin')->user()->type; // Get the currently authenticated user's `type` from `admins` table using our Custom 'admin' Authentication Guard    // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
            if ($adminType == 'vendor') {
                $addon->vendor_id = Auth::guard('admin')->user()->vendor_id; // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
            } else {
                $addon->vendor_id = 0;
            }


            // dd($data);


            // Insert data into `addons` database table
            $addon->addon_option = $data['addon_option'];
            $addon->addon_code = $addon_code;
            $addon->categories = $categories;
            $addon->addon_type = $data['addon_type'];
            $addon->addon_name = $data['addon_name'];
            $addon->addon_detail = $data['addon_detail'];
            $addon->qty = $data['qty'];
            $addon->amount_type = $data['amount_type'];
            $addon->amount = $data['amount'];
            $addon->status = 1;

            $addon->save();


            return redirect('admin/addons')->with('success_message', $message);
        }



        // Get ALL the Sections with their Categories and Subcategories (Get all sections with its categories and subcategories)    // $categories are ALL the `sections` with their (parent) categories (if any (if exist)) and subcategories (if any (if exist))
        $categories = \App\Models\Section::with('categories')->get()->toArray(); // with('categories') is the relationship method name in the Section.php Model
        // dd($categories);

        // Get all brands
        $brands = \App\Models\Brand::where('status', 1)->get()->toArray();
        // dd($brands);

        // Get all users' emails
        $users = \App\Models\User::select('email')->where('status', 1)->get();
        // dd($users);


        return view('admin.addons.add_edit_addon')->with(compact('title', 'addon', 'categories', 'brands', 'users', 'selCats'));
    }

}
