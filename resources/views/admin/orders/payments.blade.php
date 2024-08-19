{{-- This page is rendered by orders() method inside Admin/OrderController.php --}}
@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Payment History</h4>



                            <div class="table-responsive pt-3">
                                {{-- DataTable --}}
                                <table id="orders" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>Order Date</th>
                                            <th>Amount</th>
                                            <th>Payment Method</th>
                                            <th>Package</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // dd($orders); // check if the authenticated/logged-in user is 'vendor' (show ONLY orders of products belonging to them), or 'admin' (show ALL orders)
                                        @endphp
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
                                                    <td>
                                                        <a title="View Order Details" href="{{ url('admin/orders/' . $order['id']) }}">
                                                            <i style="font-size: 25px" class="mdi mdi-file-document"></i> {{-- Icons from Skydash Admin Panel Template --}}
                                                        </a>
                                                        &nbsp;&nbsp;

                                                        {{-- View HTML invoice --}}
                                                        <a title="View Order Invoice" href="{{ url('admin/orders/invoice/' . $order['id']) }}" target="_blank">
                                                            <i style="font-size: 25px" class="mdi mdi-printer"></i> {{-- Icons from Skydash Admin Panel Template --}}
                                                        </a>
                                                        &nbsp;&nbsp;

                                                        {{-- View PDF invoice --}}
                                                        <a title="Print PDF Invoice" href="{{ url('admin/orders/invoice/pdf/' . $order['id']) }}" target="_blank">
                                                            <i style="font-size: 25px" class="mdi mdi-file-pdf"></i> {{-- Icons from Skydash Admin Panel Template --}}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2023. All rights reserved.</span>
            </div>
        </footer>
        <!-- partial -->
    </div>
@endsection
