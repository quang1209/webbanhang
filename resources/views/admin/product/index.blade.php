@extends('layouts.app_master_admin')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Quản lý sản phẩm</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{  route('admin.product.index') }}"> Product</a></li>
            <li class="active"> List </li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
               <div class="box-title">
                    <form class="form-inline">
                        <input type="text" class="form-control" value="{{ Request::get('id') }}" name="id" placeholder="ID">
                        <input type="text" class="form-control" value="{{ Request::get('name') }}" name="name" placeholder="Name ...">
                        <select name="category" class="form-control" >
                            <option value="0">Danh mục</option>
                            @foreach($categories as $item)
                                <option value="{{ $item->id }}" {{ Request::get('category') == $item->id ? "selected='selected'" : "" }}>{{  $item->c_name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
{{--                        <button type="submit" name="export" value="true" class="btn btn-info">--}}
{{--                            <i class="fa fa-save"></i> Export--}}
{{--                        </button>--}}
                        <a href="{{ route('admin.product.create') }}" class="btn btn-primary">Thêm mới <i class="fa fa-plus"></i></a>
                    </form>
                </div>
                <div class="box-body">
                   <div class="col-md-12">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width: 10px">STT</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Avatar</th>
                                    <th>Giá</th>
                                    <th>Hot</th>
                                    <th>Trạng thái</th>
                                    <th>Số lượng</th>
                                    <th>Action</th>
                                </tr>

                            </tbody>
                            @if (isset($products))
                                    @foreach($products as $key => $product)
                                        <tr>
                                            <td>{{ (($products->currentPage() - 1) * $products->perPage()) + ( $key + 1)  }}</td>
                                            <td>{{ $product->pro_name }}</td>
                                            <td>
                                                <span class="label label-success">{{ $product->category->c_name ?? "[N\A]" }}</span>
                                            </td>
                                            <td>
                                                <img src="{{ pare_url_file($product->pro_avatar) }}" style="width: 80px;height: 100px">
                                            </td>
                                            <td>
                                                @if ($product->pro_sale)
                                                    <span style="text-decoration: line-through;">{{ number_format($product->pro_price,0,',','.') }} vnđ</span><br>
                                                    @php
                                                        $price = ((100 - $product->pro_sale) * $product->pro_price)  /  100 ;
                                                    @endphp
                                                    <span>{{ number_format($price,0,',','.') }} vnđ</span>
                                                @else
                                                    {{ number_format($product->pro_price,0,',','.') }} vnđ
                                                @endif

                                            </td>
                                            <td>
                                                @if ($product->pro_hot == 1)
                                                    <a href="{{ route('admin.product.hot', $product->id) }}" class="label label-info">Nổi bật</a>
                                                @else
                                                    <a href="{{ route('admin.product.hot', $product->id) }}" class="label label-default">Không</a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($product->pro_active == 1)
                                                    <a href="{{ route('admin.product.active', $product->id) }}" class="label label-info">Hiển thị</a>
                                                @else
                                                    <a href="{{ route('admin.product.active', $product->id) }}" class="label label-default">Ẩn</a>
                                                @endif
                                            </td>
                                            <td>{{  $product->pro_number }}</td>
                                            <td>
                                                <a href="{{ route('admin.product.update', $product->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                                                <a href="{{  route('admin.product.delete', $product->id) }}" class="btn btn-xs btn-danger js-delete-confirm"><i class="fa fa-trash"></i> Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    {!! $products->appends($query)->links() !!}
                </div>
                <!-- /.box-footer-->
            </div>
            <!-- /.box -->
        </div>
    </section>
    <!-- /.content -->
@stop
