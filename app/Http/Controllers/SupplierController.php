<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use DB;

class SupplierController extends Controller
{

    public function supplier_manage_view(){

        $suppliers = Supplier::all();
       return view("backend.supplier.supplierManage",compact("suppliers"));
    }

    public function supplier_manage_modal_show(Request $request)
    {

      $get_supplier_specefic_data = Supplier::where('id', $request->id)->first();

      if($get_supplier_specefic_data){
        return response()->json([
           "status"=>200,
            "get_supplier_specefic_data"=>$get_supplier_specefic_data
        ]);
      }
        
    }
    public function supplier_manage_modal_edit(Request $request)
    {

      $get_supplier_specefic_data_edit = Supplier::where('id', $request->id)->first();

      if($get_supplier_specefic_data_edit){
        return response()->json([
           "status"=>200,
            "get_supplier_specefic_data_edit"=>$get_supplier_specefic_data_edit
        ]);
      }
        
    }


    public function supplier_manage_modal_update(Request $request){
      
      $supplier = Supplier::find($request->id);
      $supplier->name = $request->name;
      $supplier->email = $request->email;
      $supplier->phone = $request->phone;
      $supplier->address = $request->address;
      $supplier->update();

      return response()->json([
        "status"=>200
     ]);
  }
  public function supplier_manage_modal_delete(Request $request){
    
      $supplier = Supplier::where('id','=',$request->id)->first();

      $supplier->delete();
      

      if($supplier)
      {
          return response()->json([
              'status'=>200,
              'supplier'=>  $supplier,
              'supplier_id'=>  $supplier->id,
              'supplier_name'=>  $supplier->name,
          ]);
      }
      else
      {
          return response()->json([
              'status'=>404,
              'message'=>'supplier Not Found',
          ]);
      }
 
      
  }

  
    
}
