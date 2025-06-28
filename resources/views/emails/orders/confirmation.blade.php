@component('mail::message')
# Hello {{ $order->customer->first_name }},

Your order **#{{ $order->order_number }}** has been successfully placed.

@component('mail::panel')
**Total:** KES {{ number_format($order->total, 2) }}  
**Status:** {{ ucfirst($order->status) }}  
@endcomponent

We’ll notify you as it progresses through delivery.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
