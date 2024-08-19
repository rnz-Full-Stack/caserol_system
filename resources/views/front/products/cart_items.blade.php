{{-- Note: This whole file is 'include'-ed in front/products/cart.blade.php (to allow the AJAX call when updating orders quantities in the Cart) --}}


<!-- Products-List-Wrapper -->
<div class="table-wrapper u-s-m-b-60">
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Add-ons</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>


            {{-- We'll place this $total_price inside the foreach loop to calculate the total price of all products in Cart. Check the end of the next foreach loop before @endforeach --}}
            @php 
                $total_price = 0;
                $subtotal = 0;
            @endphp

            @foreach ($getCartItems as $item) {{-- $getCartItems is passed in from cart() method in Front/ProductsController.php --}}
                @php
                    $getDiscountAttributePrice = \App\Models\Product::getDiscountAttributePrice($item['product_id'], $item['size']); // from the `products_attributes` table, not the `products` table
                    // dd($getDiscountAttributePrice);
                @endphp

                <tr>
                    <td>
                        <div class="cart-anchor-image">
                            <a href="{{ url('product/' . $item['product_id']) }}">
                                <img src="{{ asset('front/images/product_images/small/' . $item['product']['product_image']) }}" alt="Product">
                                <h6>
                                    {{ $item['product']['product_name'] }} ({{ $item['product']['product_code'] }}) - {{ $item['size'] }}
                                    <br>
                                    Color: {{ $item['product']['product_color'] }}
                                </h6>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="cart-price">



                            @if ($getDiscountAttributePrice['discount'] > 0) {{-- If there's a discount on the price, show the price before (the original price) and after (the new price) the discount --}}
                                <div class="price-template">
                                    <div class="item-new-price">
                                        P{{ $getDiscountAttributePrice['final_price'] }}
                                    </div><br>
                                    <div class="item-old-price" style="margin-left: -40px">
                                        P{{ $getDiscountAttributePrice['product_price'] }}
                                    </div>
                                </div>
                            @else {{-- if there's no discount on the price, show the original price --}}
                                <div class="price-template">
                                    <div class="item-new-price">
                                        P{{ $getDiscountAttributePrice['final_price'] }}
                                    </div>
                                </div>
                            @endif



                        </div>
                    </td>
                    <td>
                        <div class="cart-quantity">
                            <div class="quantity">
                                <input type="text" class="quantity-text-field" value="{{ $item['quantity'] }}">
                                <a data-max="1000" class="plus-a  updateCartItem" data-cartid="{{ $item['id'] }}" data-qty="{{ $item['quantity'] }}">&#43;</a> {{-- The Plus sign:  Increase items by 1 --}} {{-- .updateCartItem CSS class and the Custom HTML attributes data-cartid & data-qty are used to make the AJAX call in front/js/custom.js --}}
                                <a data-min="1"    class="minus-a updateCartItem" data-cartid="{{ $item['id'] }}" data-qty="{{ $item['quantity'] }}">&#45;</a> {{-- The Minus sign: Decrease items by 1 --}} {{-- .updateCartItem CSS class and the Custom HTML attributes data-cartid & data-qty are used to make the AJAX call in front/js/custom.js --}}
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $subtotal = $getDiscountAttributePrice['final_price'] * $item['quantity'];
                        @endphp
                        @foreach ($item['addons'] as $addon)
                            @php
                            $addon_total = 0;
                                $addon_info = \App\Models\Addon::where('id', $addon['addon_id'])->first();
                                $addon_subtotal = $addon['amount'] * $addon['qty'];
                                $addon_total += $addon_subtotal;
                                $subtotal += $addon_total;
                            @endphp
                            
                            <table class="table">
                                <thead>
                                    <th>Name</th>
                                    <th>Detail</th>
                                    <th>Amount</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </thead>
                                <tbody>
                                    <td>{{ $addon_info->addon_name }}</td>
                                    <td>{{ $addon_info->addon_detail }}</td>
                                    <td>{{ $addon['amount'] }}</td>
                                    <td>{{ $addon['qty'] }}</td>
                                    <th>{{ $addon_subtotal }}</th>
                                </tbody>
                            </table>
                        @endforeach
                    </td>
                    <td>
                        <div class="cart-price">
                            P{{ $subtotal }} {{-- price of all products (after discount (if any)) (= price (after discoutn) * no. of products) --}}
                        </div>
                    </td>
                    <td>
                        <div class="action-wrapper">
                            {{-- <button class="button button-outline-secondary fas fa-sync"></button> --}}
                            <button class="button button-outline-secondary fas fa-trash deleteCartItem" data-cartid="{{ $item['id'] }}"></button>{{-- .deleteCartItem CSS class and the Custom HTML attribute data-cartid is used to make the AJAX call in front/js/custom.js --}}
                        </div>
                    </td>
                </tr>


                {{-- This is placed here INSIDE the foreach loop to calculate the total price of all products in Cart --}}
                @php $total_price = $total_price + $subtotal @endphp
            @endforeach



        </tbody>
    </table>
</div>
<!-- Products-List-Wrapper /- -->





{{-- To solve the problem of Submiting the Coupon Code works only once, we moved the Coupon part from cart_items.blade.php to here in cart.blade.php --}} {{-- Explanation of the problem: http://publicvoidlife.blogspot.com/2014/03/on-on-or-event-delegation-explained.html --}}





<!-- Billing -->
<div class="calculation u-s-m-b-60">
    <div class="table-wrapper-2">
        <table>
            <thead>
                <tr>
                    <th colspan="2">Cart Totals</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <h3 class="calc-h3 u-s-m-b-0">Sub Total</h3> {{-- Total Price before any Coupon discounts --}}
                    </td>
                    <td>
                        <span class="calc-text">P{{ $total_price }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3 class="calc-h3 u-s-m-b-0">Coupon Discount</h3>
                    </td>
                    <td>
                        <span class="calc-text couponAmount"> {{-- We create the 'couponAmount' CSS class to use it as a handle for AJAX inside    $('#applyCoupon').submit();    function in front/js/custom.js --}}

                            @if (\Illuminate\Support\Facades\Session::has('couponAmount')) {{-- We stored the 'couponAmount' in a Session Variable inside the applyCoupon() method in Front/ProductsController.php --}}
                                P{{ \Illuminate\Support\Facades\Session::get('couponAmount') }}
                            @else
                                P0
                            @endif
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3 class="calc-h3 u-s-m-b-0">Grand Total</h3> {{-- Total Price after Coupon discounts (if any) --}}
                    </td>
                    <td>
                        <span class="calc-text grand_total">P{{ $total_price - \Illuminate\Support\Facades\Session::get('couponAmount') }}</span> {{-- We create the 'grand_total' CSS class to use it as a handle for AJAX inside    $('#applyCoupon').submit();    function in front/js/custom.js --}} {{-- We stored the 'couponAmount' a Session Variable inside the applyCoupon() method in Front/ProductsController.php --}}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- Billing /- -->
