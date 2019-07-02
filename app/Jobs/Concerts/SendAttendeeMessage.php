<?php

namespace App\Jobs\Concerts;

use Illuminate\Bus\Queueable;
use App\Models\AttendeeMessage;
use App\Mail\AttendeeMessageEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAttendeeMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The message to send.
     *
     * @var \App\Models\AttendeeMessage
     */
    public $attendeeMessage;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\AttendeeMessage  $attendeeMessage
     * @return void
     */
    public function __construct(AttendeeMessage $attendeeMessage)
    {
        $this->attendeeMessage = $attendeeMessage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->attendeeMessage->recipients()->each(function ($recipient) {
            Mail::to($recipient)->send(new AttendeeMessageEmail($this->attendeeMessage));
        });
    }
}
