<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Twilio\Rest\Client;

class SMSController extends Controller
{
public function sendsms(){
 try {
        $sid = getenv("TWILIO_SID");
        $token = getenv("TWILIO_TOKEN");
        $send_number = getenv("TWILIO_PHONE");
        $twilio = new Client($sid, $token);

        $message = $twilio->messages->create('+92 333 3603115', [
            "body" => "send otp",
            "from" => $send_number
        ]);

        dd('Message sent successfully!');
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}


}
