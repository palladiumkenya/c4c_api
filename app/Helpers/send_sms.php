<?php
/**
 * Created by PhpStorm.
 * User: muoki
 * Date: 20120-03-26
 * Time: 12:22
 * @param $recipientNumber
 * @param $message
 */

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;

function send_sms($recipientNumber, $message){

    $username = "mhealthkenya";
    $apiKey   = "9318d173cb9841f09c73bdd117b3c7ce3e6d1fd559d3ca5f547ff2608b6f3212";
    $AT       = new AfricasTalking($username, $apiKey);

    $sms      = $AT->sms();

    $result   = $sms->send([
        'from'      => '40146',
        'to'      => $recipientNumber,
        'message' => $message
    ]);

    Log::info($result);

}
