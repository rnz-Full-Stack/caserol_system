{{-- Variables are passed in from the addEditAddon() method in Admin/AddonsController --}}
@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h4 class="card-title">Addons</h4>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="justify-content-end d-flex">
                                <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                        <a class="dropdown-item" href="#">January - March</a>
                                        <a class="dropdown-item" href="#">March - June</a>
                                        <a class="dropdown-item" href="#">June - August</a>
                                        <a class="dropdown-item" href="#">August - November</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $title }}</h4>


                            {{-- Our Bootstrap error code in case of wrong current password or the new password and confirm password are not matching: --}}
                            {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                            @if (Session::has('error_message')) <!-- Check AdminController.php, updateAdminPassword() method -->
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif



                            {{-- Displaying Laravel Validation Errors: https://laravel.com/docs/9.x/validation#quick-displaying-the-validation-errors --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">


                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif



                            {{-- Displaying The Validation Errors: https://laravel.com/docs/9.x/validation#quick-displaying-the-validation-errors AND https://laravel.com/docs/9.x/blade#validation-errors --}}
                            {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                            {{-- Our Bootstrap success message in case of updating admin password is successful: --}}
                            @if (Session::has('success_message')) <!-- Check AdminController.php, updateAdminPassword() method -->
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif



                            <form class="forms-sample"   @if (empty($addon['id'])) action="{{ url('admin/add-edit-addon') }}" @else action="{{ url('admin/add-edit-addon/' . $addon['id']) }}" @endif   method="post" enctype="multipart/form-data">  <!-- If the id is not passed in from the route, this measn 'Add a new Addon', but if the id is passed in from the route, this means 'Edit the Addon' --> <!-- Using the enctype="multipart/form-data" to allow uploading files (images) -->
                                @csrf

                                @if (empty($addon['addon_code'])) {{-- In case of 'Add a new Addon' --}}
                                    <input type="hidden" name="addon_option" value="Manual" id="ManualAddon"/>

                                @else {{-- In case of 'Update the Addon' --}}
                                    <input type="hidden" name="addon_option" value="{{ $addon['addon_option'] }}">
                                    <input type="hidden" name="addon_code"   value="{{ $addon['addon_code'] }}">

                                    <div class="form-group">
                                        <label for="addon_code">Addon Code:</label>
                                        <span style="color: green; font-weight: bold">{{ $addon['addon_code'] }}</span>
                                    </div>
                                @endif


                                <div class="form-group">
                                    <input type="hidden" name="addon_type" value="Single Time"/>
                                    <label for="addon_type">Addon Name:</label><br>
                                    <span><input type="text" class="form-control" name="addon_name"  @if (isset($addon['addon_name'])) value="{{ $addon['addon_name'] }}" @else value="{{ old('addon_name') }}" @endif></span>
                                </div>

                                <div class="form-group">
                                    <label for="addon_type">Addon Detail:</label><br>
                                    <span><textarea class="form-control" name="addon_detail" >
                                        @if (isset($addon['addon_detail'])) {{ $addon['addon_detail'] }} @else {{ old('addon_detail') }} @endif
                                        </textarea>
                                    </span>
                                </div>

                                <div class="form-group">
                                    <label for="amount">Pieces:</label>
                                    <input type="text" class="form-control" id="qty" placeholder="Enter Addon Quantity" name="qty"  @if (isset($addon['qty'])) value="{{ $addon['qty'] }}" @else value="{{ old('qty') }}" @endif>  {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>

                                <div class="form-group">
                                    <input type="hidden" name="amount_type" value="Fixed"/>
                                    <label for="amount">Amount:</label>
                                    <input type="text" class="form-control" id="amount" placeholder="Enter Addon Amount" name="amount"  @if (isset($addon['amount'])) value="{{ $addon['amount'] }}" @else value="{{ old('amount') }}" @endif>  {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>



                                <div class="form-group">
                                    <label for="categories">Select Category:</label>
                                    <select name="categories[]" class="form-control text-dark" multiple> {{-- "multiple" HTML attribute: https://www.w3schools.com/tags/att_multiple.asp --}} {{-- We used the Square Brackets [] in name="categories[]" is an array because we used the "multiple" HTML attribute to be able to choose multiple categories (more than one category) at the same time --}}
                                        @foreach ($categories as $section) {{-- $categories are ALL the `sections` with their related 'parent' categories (if any (if exist)) and their subcategories or `child` categories (if any (if exist)) --}} {{-- Check AddonsController.php --}}
                                            <optgroup label="{{ $section['name'] }}"> {{-- sections --}}
                                                @foreach ($section['categories'] as $category) {{-- parent categories --}} {{-- Check AddonsController.php --}}

                                                    <option value="{{ $category['id'] }}"  @if (in_array($category['id'], $selCats)) selected @endif>&nbsp;&nbsp;&nbsp;--&nbsp;{{ $category['category_name'] }}</option> {{-- parent categories --}}
                                                    @foreach ($category['sub_categories'] as $subcategory) {{-- subcategories or child categories --}} {{-- Check AddonsController.php --}}
                                                        <option value="{{ $subcategory['id'] }}" @if (in_array($subcategory['id'], $selCats)) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;{{ $subcategory['category_name'] }}</option> {{-- subcategories or child categories --}}
                                                    @endforeach

                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <button type="reset"  class="btn btn-light">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>
@endsection
