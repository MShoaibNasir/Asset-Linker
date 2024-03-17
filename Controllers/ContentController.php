<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;



class ContentController extends Controller
{
    public function privacy_policy(){
        return view ('privacy_policy');
    }
       
      public function sendsms()
    {
        try {
        $account_sid = getenv("TWILIO_ACCOUNT_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_PHONE");
        $receiverNumber = '+92 3092481858'; 
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($receiverNumber, [
        'from' => $twilio_number, 
        'body' => '1234'
        ]);
        return 'SMS Sent Successfully.';
        } catch (Exception $e) {
        info("Error: ". $e->getMessage());
        }
        }
}
