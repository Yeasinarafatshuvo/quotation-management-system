<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Country;
use App\Models\CrmAssignTo;
use App\Models\State;
use App\Models\Product;
use App\Models\City;
use App\Models\Address;
use App\Models\CrmManage;
use App\Models\CRM_Reminder;
use App\Models\CRM_Comments;
use App\Models\Cart;
use Carbon\Carbon;
use App\Models\Wishlist;
use App\Models\Review;
use Illuminate\Support\Facades\DB;


class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:show_customers'])->only('index');
        $this->middleware(['permission:view_customers'])->only('show');
        $this->middleware(['permission:delete_customers'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $customers = User::whereNotIn('user_type', ['staff', 'employee'])->orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $customers = $customers->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%')->orWhere('phone', 'like', '%'.$sort_search.'%');
        }
        $customers = $customers->paginate(15);
        $data['all_state'] = State::all();
        $category_controller_instance = new CategoryController();
        $all_bd_cities = $category_controller_instance->all_bd_cities(); 
        return view('backend.customers.index', compact('customers','all_bd_cities'), $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $cart_data = Cart::where('user_id', $id)->get();
        $user_data = User::where('id', $id)->get();
        $whishlist_data = Wishlist::where('user_id', $id)->get();
        $review_data = Review::where('user_id', $id)->get();
        $order_data = Order::where('user_id', $id)->get();
        $user = User::where('id', $id)->first();
        $user_address = Address::where("user_id",$id)->first();
        $followed_shop_data = $user->followed_shops;
        $references = CrmManage::select('reference_by')->where('user_id', $id)->first();
        $bank_info = CrmManage::select('bank_information')->where('user_id', $id)->first();
        $reminder_rows = CRM_Reminder::where('customer_id', $id)->get();
        $states = State::all();
        $cities = City::all();
        return view('backend.customers.show', compact('user','reminder_rows','cart_data','user_data','whishlist_data','review_data','followed_shop_data','order_data', 'user_address','references','bank_info','states','cities'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        $address = Address::where("user_id",$id)->get()->first();
        
        $countrys = Country::all();
        $states = State::all();
        $cities = City::all();

        return view('backend.customers.edit', compact('user','countrys','states','cities','address'));
    }

    public function customer_profile_update(Request $request){

        $user_instance = User::find($request->id);
        $user_instance->name = $request->name;
        $user_instance->phone = $request->phone_number;
        $user_instance->email = $request->email_address;
        $user_instance->customer_type = $request->client_type;
        $save_instance = $user_instance->save();

        if($save_instance){
            return response()->json([
                "customer_id" => $request->id,
                "status" => "Success 101",
                "message" => "Successfully Updated Client Information",
            ]);
        }
        else{
            return response()->json([
                "status" => "Error 101",
                "message" => "Something Wrong",
            ]);
        }
    }

    public function customer_document_update(Request $request){

        $path = $request->file('document_file')->store('uploads');
        $document_name = $request->document_name;

        $document_data = [
            'document_name' => $document_name,
            'path' => $path
        ];

        // Save file path to database

        $document_instance = CrmManage::where('user_id', $request->user_id)->first();
        $documents = !is_null($document_instance) ? json_decode($document_instance->documents, true) : [];

        if (is_null($document_instance)) {
            // Create a new CrmManage instance with the user_id and documents attributes
            $document_instance = new CrmManage;
            $document_instance->user_id = $request->user_id;
            $document_instance->documents = json_encode([$document_data]);
            $document_instance->save();
        } else {
            if (is_null($documents)) {
                // Create a new array with the new document data
                $documents = [$document_data];
            } else {
                // Push the new document_data to the existing documents array
                array_push($documents, $document_data);
            }
            $document_instance->documents = json_encode($documents);
            $document_instance->save();
        }

        $request->session()->flash('success', 'Document added successfully!');
        return redirect()->back();

    }

    public function customer_phone_update(Request $request){

        $user_instance = User::where('id', $request->user_id)->first();
        $user_instance->phone = $request->phone_number;
        $user_instance->save();

        echo "Successfully Phone Number Updated ";
        return redirect()->back();

    }

    public function customer_reference_update(Request $request){
        $reference_instance = CrmManage::where('user_id', $request->user_id)->first();
        $reference_instance->reference_by = $request->reference_name;
        $reference_instance->save();

        
        echo "Successfully Reference Updated ";
        return redirect()->back();
    }

    public function customer_bank_info_update(Request $request){
        $bank_instance = CrmManage::where('user_id', $request->user_id)->first();
        $bank_instance->bank_information = $request->bank_info;
        $bank_instance->save();

        echo "Successfully Bank Information Updated ";
        return redirect()->back();

    }

    public function customer_address_update(Request $request){

        $office_address = $request->input('office_address');
        $state_id = $request->input('state');
        $state_name = State::select('name')->where('id', $state_id)->first();
        $city_id = $request->input('city');
        $city_name = City::select('name')->where('id', $city_id)->first();
    
        $user_id = $request->input('user_id');
        $address_instance = Address::where('user_id', $user_id)->first();
        $address_instance->address = $office_address;
        $address_instance->state_id = $state_id;
        $address_instance->state = $state_name->name;
        $address_instance->city_id = $city_id;
        $address_instance->city = $city_name->name;
        $save_instance = $address_instance->save();
    
        echo "Successfully Address Updated ";
        return redirect()->back();
    
    }

    public function customer_comment_update(Request $request){
        $comment = $request->comment;
        $comment_id = $request->comment_id;
        $all_product_id = $request->all_product_id;

        $comment_instance = CRM_Comments::find($comment_id);
        $comment_instance->id = $comment_id;
        $comment_instance->comments = $comment;
        $data = $comment_instance->product_ids =  $all_product_id;
        $save_instance = $comment_instance->save();

        if($save_instance){
            return response()->json([
                "all_product_id" => $all_product_id,
                "status" => "Success 101",
                "message" => "Successfully Updated Client Information",
            ]);
        }
        else{
            return response()->json([
                "error" => "Error 101",
                "message" => "Something wrong",
            ]);
        }

    }
    
    function customer_reminder_view(Request $request){

        $data = CRM_Reminder::where('id', $request->reminder_id)->first();
        $product_ids = CRM_Reminder::select('interested_product')->where('id', $request->reminder_id)->first();
        $product_name_list = [];
        
        if ($product_ids) { // make sure the $product_ids variable is not empty or null
          $ids = json_decode($product_ids->interested_product); // convert the string to an array
        
          foreach ($ids as $id) {
            // assuming the Product model is correctly defined and 'name' is the product name field
            $product_name = Product::select('name')->where('id', $id)->first();
        
            if ($product_name) { // make sure the product exists
              array_push($product_name_list, $product_name->name); // push the product name into the list
            }
          }
        }
        

        return response()->json([
            "data" => $data,
            "product_name_list" => $product_name_list,
            "status" => "Success 101",
            "message" => "Successfully Updated Client Information",
        ]);
    }

    function customer_reminder_update(Request $request){

        $date_time_arr = explode("T", $request->date); // Split date and time using "T" as the separator

        $reminder_instance = CRM_Reminder::find($request->reminder_id);
        $reminder_instance->assign_by = $request->user_id;
        $reminder_instance->assign_to = $request->assign_to;
        $reminder_instance->customer_id = $request->customer_id;
        $reminder_instance->interested_product = $request->all_product_id;
        $reminder_instance->note = $request->reminder_note;
        $reminder_instance->status = $request->status;
        $reminder_instance->date = $date_time_arr[0]; // date
        $reminder_instance->time = $date_time_arr[1]; // time
        $save_instance = $reminder_instance->save();
        if($save_instance){
            return response()->json([
                "status" => "Success 101",
                "message" => "Successfully Updated Reminder",
            ]);
        }
        else{
            return response()->json([
                "error" => "Error 101",
                "message" => "Something wrong",
            ]);
        }
    }

    function getUserName(Request $request){
        $user = User::where('id', $request->id)->first();
        $user_name =  $user->name;
        return response()->json([
            "user_name" => $user_name,
        ]);
    }

    function customer_assignto_add(Request $request){

        $assignto_instance = new CrmAssignTo;
        $assignto_instance->customer_id = $request->customer_id_assign_to;
        $assignto_instance->assign_by = $request->assign_by_add;
        $assignto_instance->assign_to = $request->assign_to_add;
        $save_instance = $assignto_instance->save();
        if($save_instance){
            return response()->json([
                "status" => "success",
                "message" => "Successfully Updated",
            ]);
        }
        else{
            return response()->json([
                "error" => "error",
                "message" => "Something wrong",
            ]);
        }

    }

    public function customer_assignto_view(Request $request){

        

        if($request->user_type == "admin"){
            $data = CrmAssignTo::where('customer_id', $request->customer_id)->get();
            return response()->json([
                "status" => "Success 101",
                "data" => $data,
                "message" => "Successfully Updated"
            ]);

        }
        else{
            $data = CrmAssignTo::where('customer_id', $request->customer_id)->where('assign_to', $request->user_id)->get();
            return response()->json([
                "status" => "Success 101",
                "data" => $data,
                "message" => "Successfully Updated"
            ]);
        }

    }

    public function customer_edit_assignto(Request $request){

        $data = CrmAssignTo::where('id', $request->assign_id)->first();
        return response()->json([
            "data" => $data
        ]);
    }

    public function customer_delete_assignto(Request $request){

        $assignto_instance = CrmAssignTo::find($request->id);
        $assignto_instance->delete();

        return response()->json([
            "data" => "deleted"
        ]);
    }

    public function customer_update_assignto(Request $request){


        $assignto_instance = CrmAssignTo::find($request->assignto_row);
        $assignto_instance->customer_id = $request->customer_id;
        $assignto_instance->assign_by = $request->assign_by_id;
        $assignto_instance->assign_to = $request->assignto_user;
        $assignto_instance->updated_at = $today = Carbon::now();
        $save_instance = $assignto_instance->save();
        $customer_id = $request->customer_id;
        if($save_instance){
            return response()->json([
                "status" => "success",
                "customer" => $customer_id
            ]);
        }
        else{
            return response()->json([
                "status" => "error"
            ]);
        }


    }

    
    


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        $user->orders()->delete();
        $user->reviews()->delete();
        $user->carts()->delete();
        $user->wallets()->delete();
        $user->addresses()->delete();
        $user->reviews()->delete();

        $user->delete();

        flash(translate('Customer deleted successfully'))->error();
        return back();
    }

    public function ban($id) {
        $user = User::find($id);

        if($user->banned == 1) {
            $user->banned = 0;
            flash(translate('Customer Unbanned Successfully'))->success();
        } else {
            $user->banned = 1;
            flash(translate('Customer Banned Successfully'))->success();
        }

        $user->save();

        return back();
    }
}
