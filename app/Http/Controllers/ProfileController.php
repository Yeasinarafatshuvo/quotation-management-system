<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use App\Models\State;
use App\Models\City;
use Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.admin_profile.index');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        if(env('DEMO_MODE') == 'On'){
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $address = Address::where('user_id',$id)->get()->first();
        $state = State::find($request->state);
        $city = City::find($request->city);

        if(!empty($address)){
            $address->user_id = $id;
            $address->address = $request->address;
            $address->country = "Bangladesh";
            $address->country_id = 18;
            $address->state = $state->name;
            $address->state_id = $request->state;
            $address->city = $city->name;
            $address->city_id = $request->city;
            $address->postal_code = $request->postal_code;
            $address->phone = $request->shipping_phone;
            $address->default_shipping = 1;
            $address->default_billing = 1;
            $address->save();
        }else{
            $address = new Address;
            $address->user_id = $id;
            $address->address = $request->address;
            $address->country = "Bangladesh";
            $address->country_id = 18;
            $address->state = $state->name;
            $address->state_id = $request->state;
            $address->city = $city->name;
            $address->city_id = $request->city;
            $address->postal_code = $request->postal_code;
            $address->phone = $request->shipping_phone;
            $address->default_shipping = 1;
            $address->default_billing = 1;
            $address->save();
        }
        
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->phone != null){
            $user->phone = $request->phone;
        }
        if($request->new_password != null && ($request->new_password == $request->confirm_password)){
            $user->password = Hash::make($request->new_password);
        }
        $user->avatar = $request->avatar;
        if($user->save()){
            flash(translate('Your Profile has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
