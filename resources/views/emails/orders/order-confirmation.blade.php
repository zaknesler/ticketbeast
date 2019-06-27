<p>Thanks for your order!</p>
<p>You can view your tickets at any time by visiting this URL:</p>

<p>
  <a href="{{ route('orders.show', $order->confirmation_number) }}">
    {{ route('orders.show', $order->confirmation_number) }}
  </a>
</p>
