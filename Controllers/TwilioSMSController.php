<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class TwilioSMSController extends Controller
{
    public function otpsend(){
      return view('otp');

    }
}
