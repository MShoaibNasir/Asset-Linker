<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\BuyerSeller;
use App\Models\User;

use Illuminate\Http\Request;

class BuyerSellerController extends Controller
{

public function indexBuyers()
{
  $buyer= BuyerSeller::all();
  return view('buyer.index', compact('buyer'));
}

  public function createBuyer()
  {
    return view('buyer.create');
  }

public function storeBuyer(Request $request)
{
   $request->validate([
    'name'      => 'required',
    'mobile'    => 'required', 
    'email'     => 'required|unique:users',
    'password'  => 'required',

   ]);

   $user = new User();
 
   $user->name        = $request->name; 
   $user->email       = $request->email;
   $user->password    = Hash::make($request->password);
   $user->token       = sha1(time());
   $user->save();

   $buyerSeller = new BuyerSeller();

   $buyerSeller->user_id    = $user->id;
   $buyerSeller->name    = $request->name;
   $buyerSeller->mobile  = $request->mobile;
   $buyerSeller->email   = $request->email;
   $buyerSeller->password = Hash::make($request->password);
   if($buyerSeller)
   {
    return redirect()->back()->with('message' , 'New Buyer Seller has been created successfully');
   }
   else{
    return redirect()->back()->with('message', 'ERROR..............');
   }


}

public function editbuyer($id)
{
         $buyer = BuyerSeller::where('id' , '=' , $id)->first();
         return view('buyer.edit', compact('buyer'));
}

public function updatebuyer(Request $request, BuyerSeller $buyer)
{

         $request->validate([
              'name'  => 'required',
              'mobile' => 'required', 
              'email' => 'required', 
              'password' => 'required' 

         ]);
    
$user = User::where('id', '=', $buyer->user_id)->first();
$user->name = $request->name;
$user->email = $request->email;
$user->password = hash::make($request->password);
$user->save();

     $buyer->name = $request->name; 
     $buyer->mobile = $request->mobile;
     $buyer->email = $request->email;    
     $buyer->password = $request->password;
     If($buyer->save())
     {
      return back()->with('message', 'Buyer Seller has been updated successfully');
     }
     else{
      return back()->with('message', 'ERROR..............');
     }
}


public function deleteBuyer(BuyerSeller $buyer)
{
     $user = User::where('id', '=', $buyer->user_id)->first();
     $user->delete();
   
     if($buyer->delete())
     {
      return back()->with('messsage', 'Buyer has been deleted successfully');
     }
     else{
      return back()->with('message', 'ERROR...........');
     }
}


}
