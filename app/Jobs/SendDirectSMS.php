<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendDirectSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $msisdn;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param string $msisdn
     * @param string $message
     */
    public function __construct(string $msisdn, string $message)
    {
        $this->msisdn = $msisdn;
        $this->message = $message;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        send_sms($this->msisdn, $this->message);
    }
}
