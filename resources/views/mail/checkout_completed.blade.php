<x-mail::message>
<h1 style="text-align: center; font-size: 24px;">
    Payment was Completed Successfully
</h1>

@foreach ($orders as $order)
<x-mail::table>
    <table>
        <tbody>
            <tr>
                <td>Seller</td>
                <td>
                    <a href="{{ url('/')}}">
                        {{ $order->vendorUser->vendor->store_name }}
                    </a>
                </td>
            </tr>
            <tr>
                <td>Order #</td>
                <td>{{ $order->id }}</td>
            </tr>
            <tr>
                <td>Items</td>
                <td>{{$order->total_price}}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>{{ \Illuminate\Support\Number::currency($order->total_price) }}</td>
            </tr>
        </tbody>
    </table>
</x-mail::table>

<x-mail::table>
    <table>
        <thead>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price</th>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $orderItem)
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td padding="5" style="padding: 5px;">
                                    <img src="{{ $orderItem->product->getImageForOptions($orderItem->variation_type_option_ids) }}"
                                        style="min-width: 60px; max-width: 60px;" alt="">
                                </td>
                                <td style="font-size: 14px; padding: 5px;">
                                    {{ $orderItem->product->title }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        {{ $orderItem->quantity }}
                    </td>
                    <td>
                        {{ \Illuminate\Support\Number::currency($orderItem->price) }}
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
</x-mail::table>

<x-mail::button :url="$order->id">
    View Order Details
</x-mail::button>

@endforeach
<x-mail::subcopy>

</x-mail::subcopy>

<x-mail::panel>
    Thank you for having business with us.
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>