@component('mail::message')
# Order Created

Thank you for your order! You can view your tickets at any time by clicking the button below:

@component('mail::button', ['url' => route('orders.show', $order->confirmation_number)])
View Order
@endcomponent
@endcomponent
