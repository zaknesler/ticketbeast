<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Models\Order;
use App\Mail\OrderConfirmationEmail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderConfirmationEmailTest extends TestCase
{
    /**
     * Render a specified mailable to perform tests upon it.
     *
     * @param  \Illuminate\Contracts\Mail\Mailable  $mailable
     * @return string
     */
    private function render($mailable)
    {
        $mailable->build();

        return view($mailable->view, $mailable->buildViewData())->render();
    }

    /** @test */
    function order_confirmation_email_contains_a_link_to_the_order_confirmation_page()
    {
        $order = factory(Order::class)->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
        ]);

        $renderedEmail = $this->render(new OrderConfirmationEmail($order));

        $this->assertContains(url('/orders/ORDERCONFIRMATION1234'), $renderedEmail);
    }

    /** @test */
    function order_confirmation_email_has_a_subject()
    {
        $order = factory(Order::class)->make();

        $email = new OrderConfirmationEmail($order);

        $this->assertEquals('Your Ticketbeast Order', $email->build()->subject);
    }
}
