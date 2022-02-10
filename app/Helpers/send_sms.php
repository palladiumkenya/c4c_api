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
use App\Outbox;
use Illuminate\Support\Facades\Log;

function send_sms($recipientNumber, $message){

    Log::info("saving to outbox...");
    $outbox = new Outbox();
    $outbox->message = $message;
    $outbox->recipient = $recipientNumber;
    $outbox->saveOrFail();
    Log::info("saved to outbox...");

    Log::info("sending message...");

    $username = "Ushauri_KE";
    $apiKey   = "972bdb6f53893725b09eaa3581a264ebf77b0e816ef5e9cb9f29e0c7738e41c1";
    $AT       = new AfricasTalking($username, $apiKey);

    $sms      = $AT->sms();

    $result   = $sms->send([
        'from'      => '40149',
        'to'      => $recipientNumber,
        'message' => $message
    ]);


    Log::info("message sent. See AT response below...");


    Log::info($result);

}
