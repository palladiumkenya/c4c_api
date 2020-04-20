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

    $username = "mhealthuser";
    $apiKey   = "1f6943f6c8f0d5d6b0dd54cd940935bdec8f7454c4e7863672048dae496ae355";
    $AT       = new AfricasTalking($username, $apiKey);

    $sms      = $AT->sms();

    $result   = $sms->send([
        'from'      => '40146',
        'to'      => $recipientNumber,
        'message' => $message
    ]);

    Log::info($result);

}
