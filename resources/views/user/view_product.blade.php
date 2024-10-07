@extends('layouts.app_master_frontend')
@section('css')
<link rel="stylesheet" href="{{ asset('css/user_view_product.css') }}">
@stop
@section('content')
    <div class="product-two">
        <div class="container">
            <div class="product-list">
                <div class="right">
                    <div class="group" id="user-product-view">
                        <div class="spinner">
                            <div class="rect1"></div>
                            <div class="rect2"></div>
                            <div class="rect3"></div>
                            <div class="rect4"></div>
                            <div class="rect5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script>
		var LOADPRODUCTVIEW = '{{ route('ajax_get.product_view') }}'
    </script>
    <script type="text/javascript">
		<?php $js = file_get_contents('js/product_search.js');echo $js;?>
    </script>
@stop
