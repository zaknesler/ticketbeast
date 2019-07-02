<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\AttendeeMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AttendeeMessageEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The message to send.
     *
     * @var \App\Models\AttendeeMessage
     */
    public $attendeeMessage;

    /**
     * Create a new mailable instance.
     *
     * @param  \App\Models\AttendeeMessage  $attendeeMessage
     * @return void
     */
    public function __construct(AttendeeMessage $attendeeMessage)
    {
        $this->attendeeMessage = $attendeeMessage;
    }

    /**
     * Build the mailable.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->attendeeMessage->subject)
                    ->markdown('emails.attendee-message');
    }
}
