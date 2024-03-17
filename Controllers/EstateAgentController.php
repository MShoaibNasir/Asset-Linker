<?php

namespace App\Http\Controllers;
use App\Models\EstateAgent;
use App\Models\User;
use App\Models\Property;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EstateAgentController extends Controller
{

    public function indexAgents()
    {
        $agent = EstateAgent::all();
        return view('agent.index', compact('agent'));
    }

    public function createAgent()
    {
        return view('agent.create');
    }

    public function storeAgent(Request $request)
    {
        $request->validate([
        'name'                 => 'required',
        'real_estate_name'     => 'required',
        'mobile'               => 'required', 
        'email'                => 'required|unique:users', 
        'password'             => 'required',
        'city'                 => 'required',
        'address'                 => 'required'

        ]);

             $user = new User();

             $user->name     = $request->name;
             $user->email    = $request->email;
             $user->password = Hash::make($request->password);
             $user->token    = sha1(time());
             $user->save();

             $estateAgent = new EstateAgent();

             $estateAgent->user_id             = $user->id;
             $estateAgent->name                = $request->name;
             $estateAgent->real_estate_name    = $request->real_estate_name;
             $estateAgent->mobile              = $request->mobile;
             $estateAgent->email               = $request->email;
             $estateAgent->password            = Hash::make($request->password); 
             $estateAgent->city                = $request->city;
             $estateAgent->address             = $request->address; 
             if($estateAgent->save())
             {
                return redirect()->back()->with('message' , 'New Estate Agent has been created successfully');
             }
             else{
                return redirect()->back()->with('message', 'ERROR..............');
            }

}

public function editAgent($id)
{
     $agent = EstateAgent::where('id', '=', $id)->first();
     return view('agent.edit', compact('agent'));
}


public function updateAgent(Request $request, EstateAgent $agent)
{
     $request->validate([
        'name'                 => 'required',
        'real_estate_name'     => 'required',
        'mobile'               => 'required', 
        'email'                => 'required',
        'password'             => 'required',
        'city'                 => 'required',
        'address'                 => 'required'

     ]);

     $user = User::where('id', '=' , $agent->user_id)->first();
     $user->name = $request->name; 
     $user->email = $request->email;
     $user->password = Hash::make($request->password);
     $user->save();

     $agent->name = $request->name ; 
     $agent->real_estate_name = $request->real_estate_name;
     $agent->mobile = $request->mobile;
     $agent->email = $request->email;
     $agent->password = Hash::make($request->password);
     $agent->city = $request->city;
     $agent->address = $request->address;
     if($agent->save())
     {
        return back()->with('message', 'Estate Agent has been updated successfully');
     }
     else{
        return back()->with('message', 'ERROR...................');
     }

}

public function deleteAgent(EstateAgent $agent)
{
      $user = User::where('id', '=', $agent->user_id)->first();
      $user->delete();
      if($agent->delete())
      {
        return back()->with('message', 'Estate Agent has been deleted successfully');
      }
      else{
        return back()->with('message', 'ERROR....................');
      }
}

public function update_status(Request $request, $id)
   {
      
           EstateAgent::where('id','=' , $id)->update(['status' => '0']);
        return redirect('indexAgents');
        //   $products->status = 'Pending'; 
         
   }
    
   public function on_status(Request $request, $id)
   {
   
        EstateAgent::where('id','=' , $id)->update(['status' => '1']);
         return redirect('indexAgents');
        
   }


public function estateAgentreport(Request $request)
{
    $estateAgent = Property::select('properties.*', 'users.*')->join('users', 'users.id', '=', 'properties.user_id')->where('users.user_type', '=', 'Estate Agent')->get();
    // $estateAgent = DB::select(DB::raw("SELECT properties.* , users.* FROM `properties` join users on users.id = properties.user_id WHERE users.user_type = 'Estate Agent'"));
    return view('agent.estateAgentReport', compact('estateAgent'));
    
}

}
