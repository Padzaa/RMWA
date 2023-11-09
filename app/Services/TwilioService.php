<?php

namespace App\Services;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Twilio\Rest\Client;
class TwilioService implements ShouldQueue
{
    use Queueable;
    protected $client;
    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }
    public function sendSMS($to,$message)
    {
        return $this->client->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => $message,
        ]);
    }

    public function retrieveMessages(){
        return $this->client->messages->read([
            "dateSent" => Carbon::today()
        ]);
    }
}
