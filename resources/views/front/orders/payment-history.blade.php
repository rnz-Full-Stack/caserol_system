{{-- This page is rendered by orders() method inside Front/OrderController.php (depending on if the order id Optional Parameter (slug) is passed in or not) --}}


@extends('front.layout.layout')



@section('content')
    <!-- Page Introduction Wrapper -->
    <div class="page-style-a">
        <div class="container">
            <div class="page-intro">
                <h2>Payment History</h2>
                <ul class="bread-crumb">
                    <li class="has-separator">
                        <i class="ion ion-md-home"></i>
                        <a href="index.html">Home</a>
                    </li>
                    <li class="is-marked">
                        <a href="#">Payment History</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Page Introduction Wrapper /- -->
    <!-- Cart-Page -->
    <div class="page-cart u-s-p-t-80">
        <div class="container">
            <div class="row">
                <table class="table table-striped table-borderless">
                    <tr class="table-danger">
                        <th>Order Date</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Package</th>
                        @foreach ($orders as $order)
                        @foreach ($orders as $order)
                        @if ($order['orders_products']) {{-- If the 'vendor' has ordered products (if a 'vendor' product has been ordered), show them. Check how we constrained the eager loads using a subquery in orders() method in Admin/OrderController.php inside the if condition --}}
                            <tr>
                                <td>{{ date('Y-m-d h:i:s', strtotime($order['created_at'])) }}</td>
                                <td>{{ $order['grand_total'] }}</td>
                                <td>{{ strtoupper($order['payment_gateway']) }}</td>
                                <td>
                                    @foreach ($order['orders_products'] as $product)
                                        {{ $product['product_code'] }} ({{ $product['product_qty'] }})
                                        <br>
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                    @endforeach
                        @endforeach
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <!-- Cart-Page /- -->
@endsection
