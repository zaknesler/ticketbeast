<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order to which the confirmation email pertains.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * Create a new mailable instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Ticketbeast Order')
                    ->markdown('emails.order-confirmation');
    }
}
