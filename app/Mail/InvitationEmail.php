<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The invitation that should be sent.
     *
     * @var \App\Models\Invitation
     */
    public $invitation;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Invitation  $invitation
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('You have been invited to join Ticketbeast')
                    ->markdown('emails.promoter-invitation');
    }
}
