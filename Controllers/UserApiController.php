<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\EstateAgent;
use App\Models\Builder;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\postFavourite;
use App\Models\Property;
use App\Models\UserFollower;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\BuyerSeller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Twilio\Rest\Client;

class UserApiController extends Controller
{
    public function loginApi(Request $req)
    {
        $user= User::where('phone',$req->phone)->where('status',1)->first();
        if(!$user || !hash::check($req->password,$user->password))
        {
            return ['success'=> false,"error"=>"Phone and Password is not matched"];
        }
        elseif($user->status != 1){
             return ['success'=> false,"error"=>"Account Not Active"];
        }
        else{
            $response['user'] = $user;
            if($user->user_type == 'builder'){
                $user_detail = Builder::where('user_id',$user->id)->get();
            }elseif($user->user_type == 'estate_agent'){
                 $user_detail = EstateAgent::where('user_id',$user->id)->get();
            }else{
                 $user_detail = BuyerSeller::where('user_id',$user->id)->get();
            }
            $user['detail'] = $user_detail;
            return response()->json(['success'=> true , 'token'=> $user->token,'response' =>$user ]);
        }
        


    }
    public function signup_user(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
         'name'             => 'required',
         'phone'            => 'required|unique:users', 
         'email'            => 'required|email|unique:users',
         'password'         => 'required|confirmed',
            //  'image'            => 'required'
      ]);
      
        if($validator->fails())
        {
            return $validator->errors();
         }
    else{
        if ($image_64 = $request->image) {
                $extension = '.png';
                $image = $image_64;
                $imageName = Str::random(10) . '.' . $extension;
                Storage::disk('public')->put('/images/userProfile/' . $imageName, base64_decode($image));
        }
      $user = new User(); 
      $user->ms_id = Str::random(6);
      $user->name = $request->name; 
      $user->email = $request->email;
      $user->phone = $request->phone;
      $user->image = $request->image ? $imageName : '';
      $user->password = Hash::make($request->password);
      $user->token = sha1(time());
      $user->user_type = $request->user_type;
      $user->status = $request->user_type == 'builder' ? 0:1;
      $user->save();
     
      
      if($request->user_type == 'builder'){
          
              $register = new Builder();
              $register->frim_name = $request->frim_name;
              $register->landline_number = $request->landline_number;
              $register->location = $request->location; 
              $register->address = $request->address;
              $register->description = $request->description;
              $register->designation = $request->designation;
              $register->about_us = $request->about_us;
              $register->user_id = $user->id;
              
              if( $register->save())
                {
                 return response()->json(['success' => true , 'token' =>  $user->token ]);
                } else{
                return response()->json(['success'=> false , 'Message' => 'Registration Failed']);
                }
      }elseif($request->user_type == 'estate_agent'){
          
              $register = new EstateAgent();
              $register->real_estate_name = $request->real_estate_name;
              $register->location = $request->location; 
              $register->address = $request->address;
              $register->description = $request->description;
              $register->designation = $request->designation;
              $register->about_us = $request->about_us;
              $register->user_id = $user->id;
              
              if( $register->save())
                {
                 return response()->json(['success' => true , 'token' =>  $user->token ]);
                } else{
                return response()->json(['success'=> false , 'Message' => 'Registration Failed']);
                }
              
      }else{
              $register = new BuyerSeller();
              $register->previous_work = $request->previous_work;
              $register->experience = $request->experience; 
              $register->office_name = $request->office_name;
              $register->address = $request->address;
              $register->description = $request->description;
              $register->area = $request->area;
              $register->user_id = $user->id;
              
              if( $register->save())
                {
                 return response()->json(['success' => true , 'token' =>  $user->token ]);
                } else{
                return response()->json(['success'=> false , 'Message' => 'Registration Failed']);
                }
      }
      
    
    }


    }
    
    public function signup_estateAgent(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
        'name'=> 'required',
         'real_estate_name' => 'required',
         'mobile' => 'required', 
         'email' => 'required|unique:users', 
         'password' => 'required',
         'city'   => 'required', 
         'address'   => 'required',
         'certification'  => 'required|mimes:pdf',
         'image'          => 'required'
      ]);
      
        if($validator->fails())
        {
    
            return $validator->errors();
          
 }else{
   
        if ($image_64 = $request->image) {

             

                $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1]; // .jpg .png .pdf

                $replace = substr($image_64, 0, strpos($image_64, ',') + 1);

                $image = str_replace($replace, '', $image_64);

                $image = str_replace(' ', '+', $image);

                $imageName = Str::random(10) . '.' . $extension;

                Storage::disk('public')->put('/images/userProfile/' . $imageName, base64_decode($image));
                // $imageName= $imageName.$newname.",";
               

                
            
        }
        
       
        if($request->file('certification')) 
        {
            $file = $request->file('certification');
            $filename = time() . '.' . $request->file('certification')->extension();
            $filePath = public_path() . '/images/EstateAgentCertification/';
            //  $filePath =  Storage::disk('public')->put('/images/EstateAgentCertification/');
            $file->move($filePath, $filename);
        }
        
      $user = new User(); 
     
      $user->name = $request->name; 
      $user->email = $request->email;
      $user->image = $imageName;
      $user->password = Hash::make($request->password);
      $user->token = sha1(time());
      $user->user_type = 'Estate Agent';
      $user->save();
      $estate = new EstateAgent();
      
      $estate->name = $request->name; 
      $estate->mobile = $request->mobile;
      $estate->user_id = $user->id;
      $estate->email = $request->email;
      $estate->password = Hash::make($request->password);
      $estate->real_estate_name = $request->real_estate_name;
      $estate->city = $request->city; 
      $estate->address = $request->address;
      $estate->certification = $filename;
      $estate->image = $imageName;
   
   if( $estate->save())
     {
         return response()->json(['error' => false , 'token' =>  $user->token ]);
     
        } else{
         
        return response()->json(['error'=> true , 'Message' => 'ERROR...................']);
        }
    }


    }

    public function signupBuyerSeller(Request $request)
    {
        $validator = Validator::make($request->all(),[
                        
            'name' => 'required', 
            'mobile' => 'required', 
            'email' => 'required|unique:users', 
            'password' => 'required',
            'image'    => 'required'

        ]);
      
        if($validator->fails())
        {
            return $validator->errors();
        }
        else{
                 if ($image_64 = $request->image) {

             

                $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1]; // .jpg .png .pdf

                $replace = substr($image_64, 0, strpos($image_64, ',') + 1);

                $image = str_replace($replace, '', $image_64);

                $image = str_replace(' ', '+', $image);

                $imageName = Str::random(10) . '.' . $extension;

                Storage::disk('public')->put('/images/userProfile/' . $imageName, base64_decode($image));
                // $imageName= $imageName.$newname.",";
               

                
            
        }
        
        
            $user = new User();
            $user->name = $request->name; 
            $user->email =$request->email;
            $user->image = $imageName;
            $user->password = Hash::make($request->password);
            $user->token = sha1(time());
            $user->user_type = 'Buyer Seller';
            $user->save();

            $buyer = new BuyerSeller();
            $buyer->name = $request->name; 
            $buyer->email = $request->email;
            $buyer->password = Hash::make($request->password);
            $buyer->mobile = $request->mobile;
            $buyer->image = $imageName;
            $buyer->user_id = $user->id;
            if($buyer->save())
            {
                return response()->json(['error' => false, 'token' =>  $user->token ]);
            }
            else{
                return response()->json(['error'=> true , 'Message' => 'ERROR................']);
            }

        }
    }
    
 public function add_property(Request $request)
 {
     try{
    $request->validate([
        'user_id'        => 'required',
        'property_type'  => 'required',
        ]); 
        $count = DB::table('posts')->where('user_id',$request->user_id)
                   ->whereRaw('DATEDIFF(now(), created_at) < 16')->count();
        if($count > 20)
        {
           return response()->json(['success'=>false,'msg'=>'Limit Exceeded for creating post please contact AssetsLinkers'],400); 
        }
        $user = User::find($request->user_id);
        
         $imagee=[];
        if ($file = $request->images) {
            foreach ($file as $image_64) {
                 $extension = '.png';
                $image = $image_64;
               $imageName = Str::random(10) . '.' . $extension;

                Storage::disk('public')->put('/images/property/' . $imageName, base64_decode($image));
                // $imageName= $imageName.$newname.",";
                 
                $imagee[] = $imageName;
                 

                
            }
            $request['images'] = implode(',', $imagee);
            
        }
        
        $property = Property::create($request->all());
        // $property->user_id = $user->id;
        // $property->property_type = $request->property_type;
        // $property->size = $request->size;
        // $property->rent_sale   = $request->rent_sale;
        // $property->images = implode(',', $imagee);
        // $property->save();
        
        $post = new Post();
        $post->property_id = $property->id;
        $post->user_id     = $user->id;
        $post->save();
        return response()->json(['succes' => true, 'msg'=> 'Post Add Successfully'],200);
         
     }catch(Exception $e){
          return response()->json(['success'=>false,'error'=>$e],500); 
     }
        
 }
 
 
public function get_property($user_id='')
 {
    if(empty($user_id)){
     $property = DB::select(DB::raw("SELECT properties.*, users.user_type,users.ms_id,users.name,users.email,users.phone,users.created_at as member_since,users.image, case when EXISTS (select post_favourite.id from post_favourite where post_favourite.user_id = properties.user_id and post_favourite.post_id = properties.id ) then true else false END as is_favourite  FROM `properties` join users on users.id = properties.user_id")); 

        
    }else{
     $property = DB::select(DB::raw("SELECT properties.*, users.user_type,users.ms_id,users.name,users.email,users.phone,users.created_at as member_since,users.image,  case when EXISTS (select post_favourite.id from post_favourite where post_favourite.user_id = properties.user_id and post_favourite.post_id = properties.id ) then true else false END as is_favourite FROM `properties` join users on users.id = properties.user_id where properties.user_id = $user_id")); 
    }
     if($property)
     {
         foreach($property as $p){
             $image_arr = explode(',',$p->images);
             $p->post_images = $image_arr;
         }
         return response()->json(['status'=> true, 'property' => $property]);
     }
     else
     {
         return response()->json(['status'=> false , 'message'=> 'No Post Found']);
     }
 } 
 
 public function get_property_new($user_id="")
 {
    if(empty($user_id)){
         $property = DB::select(DB::raw("SELECT properties.*, users.user_type,users.ms_id,users.name,users.email,users.phone,users.created_at as member_since,users.image, case when EXISTS (select post_favourite.id from post_favourite where post_favourite.user_id = '.$user_id.' and post_favourite.post_id = properties.id ) then 1 else 0 END as is_favourite  FROM `properties` join users on users.id = properties.user_id")); 
    
        dd($property);
    }else{
     $property = DB::select(DB::raw("SELECT properties.*, users.user_type,users.ms_id,users.name,users.email,users.phone,users.created_at as member_since,users.image,  case when EXISTS (select post_favourite.id from post_favourite where post_favourite.user_id = '.$user_id.' and post_favourite.post_id = properties.id ) then 1 else 0 END as is_favourite FROM `properties` join users on users.id = properties.user_id where properties.user_id = $user_id")); 
    
        dd($property);
    }
     if($property)
     {
         foreach($property as $p){
             $image_arr = explode(',',$p->images);
             $p->post_images = $image_arr;
         }
         dd($property);
         return response()->json(['status'=> true, 'property' => $property]);
     }
     else
     {
         return response()->json(['status'=> false , 'message'=> 'No Post Found']);
     }
 } 
 
 
 public function get_property_data(Request $requets)
 {
     dd('ok');
     $property=DB::table('property')->get();
     return $property;
 } 
 
 
 
 
public function get_propertyV2(Request $request)
{
    $user_id = $request->id;

    $posts = DB::table('posts')
        ->join('properties', 'posts.property_id', '=', 'properties.id')
        ->get();
    $favourites = DB::table('post_favourite')->where('user_id', $user_id)->pluck('post_id')->all();
    $final_posts = [];
    foreach ($posts as $post) {
        $user = DB::table('users')->where('id', $post->user_id)->select('user_type','name','image','created_at')->first();
        if ($user) {
            $post->user_type = $user->user_type;
            $post->name = $user->name;
            $post->image = $user->image;
            $post->member_since = $user->created_at;
        } else {
            $post->user_type = null; // Handle case where user doesn't exist
        }
        $imageArray = explode(",", $post->images);
        $post->post_images = $imageArray;
        $post->is_favourite = in_array($post->id, $favourites) ? 1 : 0;
        $final_posts[] = $post;
    }
    if (count($final_posts) > 0) {
        return response()->json(['status' => true, 'property' => $final_posts]);
    } else {
        return response()->json(['status' => false, 'message' => 'No Post Found']);
    }
}


  public function delete_property(Request $request)
  {
        $request->validate([
        'user_id'        => 'required',
        'post_id'  => 'required',
        ]); 

      $post = DB::select(DB::raw("SELECT * FROM `properties` where properties.user_id = '$request->user_id' and properties.id = '$request->post_id'"));
      if($post)
      {
          Property::where('id',$request->post_id)->delete();
          return response()->json(['status'=> true , 'message'=> 'Deleted']);
      }
      else
      {
          return response()->json(['status'=> false , 'message'=> 'Not Found...............']);
      }
      
      
  }
  public function getPostByUsertoken(Request $request)
  {
      $token = $request->bearerToken();
    //   dd($token);
      $post = DB::select(DB::raw("SELECT properties.*, users.* FROM `posts` join users on users.id = posts.user_id join properties on properties.id = posts.property_id where users.token = '$token' "));
      if($post)
      {
          return response()->json(['status'=> true , 'post'=> $post]);
      }
      else
      {
          return response()->json(['status'=> false , 'message'=> 'Not Found...............']);
      }
      
      
  }
  
  
  public function follow(Request $request)
  {
        $request->validate([
            'user_id' => 'required',
            'follower_id' => 'required',
        ]);

        $check = UserFollower::where('user_id', $request->user_id)->where('follower_id', $request->follower_id)->first();
        if ($check) {
            if ($check->delete()) {
                return response()->json(['status' => true, 'message' => 'Successfully unFollowed']);
            } else {
                return response()->json(['status' => false, 'message' => 'Error Occurred']);
            }
        } else {
            $user = User::where('id' , '=', $request->user_id)->first();
            
            if($user->user_type == 'buyer_seller')
            {
                $follow = new UserFollower();
                $follow->user_id = $request->user_id;
                $follow->follower_id = $request->follower_id;
               if( $follow->save())
               {
                     return response()->json(['status' => true, 'message' => 'Successfully Followed']);
               }
               else
               {
                    return response()->json(['status' => false, 'message' => 'Error Occurred']);
               }
            }
            else
            {
               return response()->json(['Message' => 'User is Estate Agent']); 
            }
            // if ($request->user_id != $request->follower_id) {
            //     $follow = new UserFollower();
            //     $follow->user_id = $request->user_id;
            //     $follow->follower_id = $request->follower_id;

            //     if ($follow->save()) {
            //         return response()->json(['status' => true, 'message' => 'Successfully Followed']);
            //     } else {
            //         return response()->json(['status' => false, 'message' => 'Error Occurred']);
            //     }
            // } else {
            //     return response()->json(['ERROR' => 'UserId and FollowerId are same']);
            // }
        }
  }
  
   public function post_likes(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'post_id' => 'required',
        ]);

        $check = PostLike::where('post_id', $request->post_id)->where('user_id', $request->user_id)->first();
        $posts = Post::where('id', $request->post_id);

        if ($check && $posts) {

            $post = Post::find($request->post_id);
            $post->like_count = (int) $post->like_count - 1;
            $post->update();
            $check->delete();

            return response()->json(['status' => true, 'message' => 'Successfully Unliked']);
            

        } else {
            $like = new PostLike();
            $like->post_id = $request->post_id;
            $like->user_id = $request->user_id;
            $post = Post::find($request->post_id);
            $post->like_count = (int) $post->like_count + 1;
            if ($like->save() && $post->save()) {
                return response()->json(['status' => true, 'message' => 'Successfully liked']);
            } else {
                return response()->json(['status' => false, 'message' => 'Error Occurred']);
            }
        }
    }
    
   public function update_password(Request $req)
    {
        try{
        $validator = Validator::make($req->all(), [
         'phone'            => 'required', 
         'password'         => 'required|confirmed'
      ]);
      
        if($validator->fails())
        {
            return $validator->errors();
        }
         
        $user= User::where('phone',$req->phone)->where('status',1)->first();
        if(!$user)
        {
            return response()->json(['success'=> false,"error"=>"User Not Found"],400);
        }
        $user_update = User::where('id',$user->id)->update(['password'=> Hash::make($req->password)]); 
       
            if($user_update){
                return response()->json(['success'=> true , 'msg'=> 'Password Update Successfully'],200);
            }else{
                return response()->json(['success'=> false , 'error'=> 'Unable to Update'],400);
            }
        }catch(Exception $e){
            return response()->json(['success'=>false,'error'=>$e],500); 
        }
        
    }
  
   public function update_user(Request $req)
    {
        try
        {
            $validator = Validator::make($req->all(), [
             'user_id'            => 'required'
             ]);
          
            if($validator->fails())
            {
                return $validator->errors();
            }
             
            $user= User::where('id',$req->user_id)->where('status',1)->first();
            if(!$user)
            {
                return response()->json(['success'=> false,"error"=>"User Not Found"],400);
            }
            $common_credentials = [];
      
        
             if ($image_64 = $req->image) {

                  $extension = '.png';
                $image = $image_64;
                $imageName = Str::random(10) . '.' . $extension;
                Storage::disk('public')->put('/images/userProfile/' . $imageName, base64_decode($image));
                 $common_credentials['image'] = $imageName;
        }
            // if ($image_64 = $req->image) {
    
            //         $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1]; // .jpg .png .pdf
            //         $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            //         $image = str_replace($replace, '', $image_64);
            //         $image = str_replace(' ', '+', $image);
            //         $imageName = Str::random(10) . '.' . $extension;
            //         Storage::disk('public')->put('/images/userProfile/' . $imageName, base64_decode($image));
           
            // }
            if($req->name){
            $common_credentials['name'] =$req->name;
            }
            if($common_credentials){
            $user_update = User::where('id',$user->id)->update($common_credentials); 
            }
            $credentials = $req->except(['name','image','user_id']);
             if(!empty($credentials) && $user->user_type == 'builder'){
               $user_update = Builder::where('user_id',$user->id)->update($credentials); 
             }elseif(!empty($credentials)  && $user->user_type == 'estate_agent'){
               $user_update = EstateAgent::where('user_id',$user->id)->update($credentials); 
             }else{
                 $user_update = BuyerSeller::where('user_id',$user->id)->update($credentials); 
             }
          
            if($user_update){
                return response()->json(['success'=> true , 'msg'=> 'Update Successfully'],200);
            }else{
                return response()->json(['success'=> false , 'error'=> 'Unable to Update'],400);
            }
            
        }catch(Exception $e){
            return response()->json(['success'=>false,'error'=>$e],500); 
        }
        
    }
  
  
      public function save_favourite(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'post_id' => 'required',
        ]);
        try{
        $save_favourite = PostFavourite::firstOrCreate(['user_id'=>$request->user_id,'post_id'=>$request->post_id]);
            if($save_favourite->wasRecentlyCreated)
                {
                    return response()->json(['status' => true, 'message' => 'Added to Favourite']);
                }else{
                    return response()->json(['status' => true, 'message' => 'Post Already Added']);
                }
        }
        catch(Exception $e){
                return response()->json(['status' => false, 'message' => $e],500);
             }
    }
    
      public function remove_favourite(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'post_id' => 'required',
        ]);
        try{
        $remove_favourite = PostFavourite::where('user_id',$request->user_id)->where('post_id',$request->post_id)->delete();
            if($remove_favourite)
                {
                    return response()->json(['status' => true, 'message' => 'Remove From Favourite'],200);
                }
        }
        catch(Exception $e){
                return response()->json(['status' => false, 'message' => $e],500);
             }
    }
    
      public function show_favourite(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);
        try{
        $show_favourite = PostFavourite::where('post_favourite.user_id',$request->user_id)
                          ->join('properties','properties.id','=','post_favourite.post_id')->get();
            if($show_favourite)
                {
                    foreach($show_favourite as $p){
                         $image_arr = explode(',',$p->images);
                         $p->post_images = $image_arr;
                     }
         
                    return response()->json(['status' => true, 'message' => 'Remove From Favourite','response'=>$show_favourite],200);
                }
        }
        catch(Exception $e){
                return response()->json(['status' => false, 'message' => $e],500);
             }
    }
    
    
    
     public function show_favouriteV2(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);
        try{
         $PostFavourite=PostFavourite::where('user_id',$request->user_id)->get();
         return  $PostFavourite;
        $show_favourite = PostFavourite::where('post_favourite.user_id',$request->user_id)
                          ->join('properties','properties.id','=','post_favourite.post_id')->get();
            if($show_favourite)
                {
                    foreach($show_favourite as $p){
                         $image_arr = explode(',',$p->images);
                         $p->post_images = $image_arr;
                     }
         
                    return response()->json(['status' => true, 'message' => 'Remove From Favourite','response'=>$show_favourite],200);
                }
        }
        catch(Exception $e){
                return response()->json(['status' => false, 'message' => $e],500);
             }
    }
    
    public function post_views(Request $request)
    {
        $request->validate([
            'post_id' => 'required',
        ]);

        $check = Property::find($request->post_id);

        if ($check) {
            $check->views = (int) $check->views + 1;
            $check->update();
            
            return response()->json(['status' => true, 'message' => 'Successful']);
        }
    }
    
    public function get_all_user($user_id='')
    {
        
        $users= User::where('status',1);
        if($user_id){ $users = $users->where('id',$user_id);}
        $users= $users->get();
        if(!$users)
        {
            return ['success'=> false,"error"=>"Unable to fetch all user"];
        }
        else{
            $response = array();
            foreach($users as $user){
           
            if($user->user_type == 'builder'){
                $user_detail = Builder::where('user_id',$user->id)->get();
            }elseif($user->user_type == 'estate_agent'){
                 $user_detail = EstateAgent::where('user_id',$user->id)->get();
            }else{
                 $user_detail = BuyerSeller::where('user_id',$user->id)->get();
            }
            $user['detail'] = $user_detail;
             $response[] = $user;
            }
            return response()->json(['success'=> true , 'token'=> $user->token,'response' =>$response ]);
        }
        
        // return $user;

    }
    
    function add_news_post(Request $request){
         try{
            $request->validate([
                'user_id'        => 'required'
                ]); 
                
                $data = array('description' => $request->post_description,'user_id'=>$request->user_id);
                 $imagee=[];
                if ($file = $request->images) {
        
                    foreach ($file as $image_64) {
        
                        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1]; // .jpg .png .pdf
        
                        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        
                        $image = str_replace($replace, '', $image_64);
        
                        $image = str_replace(' ', '+', $image);
        
                        $imageName = Str::random(10) . '.' . $extension;
        
                        Storage::disk('public')->put('/images/ceo_post/' . $imageName, base64_decode($image));
                        // $imageName= $imageName.$newname.",";
                         
                        $imagee[] = $imageName;
                         
                    }
                    $data['images'] = implode(',', $imagee);
                    
                }
                
                if($audio = $request->post_audio) {

                $extension = explode('/', explode(':', substr($audio, 0, strpos($audio, ';')))[1])[1]; // .jpg .png .pdf
                $replace = substr($audio, 0, strpos($audio, ',') + 1);
                $audio_name = str_replace($replace, '', $audio);
                $audio_name = str_replace(' ', '+', $audio_name);
                $audiosave = Str::random(10) . '.' . $extension;
                Storage::disk('public')->put('/images/ceo_post/' . $audiosave, base64_decode($audio_name));
                  $data['audio'] = $audiosave;
                 }
                 
                $create_post = DB::table('news_post')->insert($data);
                if($create_post){
                return response()->json(['succes' => true, 'msg'=> 'Post Add Successfully'],200);
                }
             }catch(Exception $e){
                  return response()->json(['success'=>false,'error'=>$e],500); 
             }
    }
    
     public function get_news_post()
    {
    
     $post = DB::select(DB::raw("SELECT p.*,u.ms_id,u.email,u.name,u.user_type,u.image as user_image, 
                            (case when u.user_type = 'estate_agent' then 
                            (SELECT e.designation from estate_agents e where e.user_id = u.id) 
                             when u.user_type = 'builder' THEN
                            (select b.designation from builders b where b.user_id = u.id)
                             end) as designation  
                            FROM news_post p join users u on u.id = p.user_id;")); 
    
    if($post)
     {
         foreach($post as $p){
             if($p->images){
                $image_arr = explode(',',$p->images);
                $p->post_images = $image_arr;
             }else{
                $p->post_images = '';
             }
         }
         return response()->json(['status'=> true, 'property' => $post]);
     }
     else
     {
         return response()->json(['status'=> false , 'message'=> 'No Post Found']);
     }
    }
    
    public function delete_news_post(Request $request)
  {
        $request->validate([
        'post_id'  => 'required',
        ]); 

      $post = DB::table('news_post')->where('post_id',$request->post_id)->delete();
      if($post){
          return response()->json(['status'=> true , 'message'=> 'Deleted']);
      }
      else
      {
          return response()->json(['status'=> false , 'message'=> 'Not Found...............']);
      }
      
      
  }
  
  public function allow_to_postNew(Request $request)
  {
   
    $validator = Validator::make($request->all(), [
     'ms_id'            => 'required'
     ]);
  
    if($validator->fails())
    {
        return $validator->errors();
    }
     
    $user= User::where('ms_id',$request->ms_id)->update(['allow_to_post'=> 1]);
        if($user)
        {
            return response()->json(['success'=> true,"error"=>"Permission Added"],200);
        }else{
            return response()->json(['success'=> false,"error"=>"User Not Found"],400);
        }
    }
    

}
