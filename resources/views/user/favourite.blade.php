@extends('layouts.app_master_user')
@section('css')
<link rel="stylesheet" href="{{ asset('css/user.min.css') }}">
    <style>
    </style>
@stop
@section('content')
    <section>
        <div class="title">Danh sách sản phẩm yêu thích</div>
        <form class="form-inline">
            <div class="form-group " style="margin-right: 10px;">
                <input type="text" class="form-control" value="{{ Request::get('name') }}" name="name" placeholder="Name">
            </div>

            <div class="form-group" style="margin-right: 10px;">
                <button type="submit" class="btn btn-pink btn-sm">Tìm kiếm</button>
            </div>
        </form>
        <div class="row mb-5">
           <div class="col-sm-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Mã ĐH</th>
                            <th class="w-25" scope="col">Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Avatar</th>
                            <th scope="col">Price</th>
                            <th scope="col">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $item)
                            <tr>
                                <th scope="row">DH{{ $item->id }}</th>
                                <th>{{ $item->pro_name }}</th>
                                <th>
                                    <span class="label label-success">{{ $item->category->c_name ?? "[N\A]" }}</span>
                                </th>
                                <th>
                                    <img src="{{ pare_url_file($item->pro_avatar) }}" style="width: 80px;height: 100px">
                                </th>
                                <th>{{ number_format($item->pro_price,0,',','.') }} đ</th>
                                <th>
                                    <a class="btn btn-light" href="{{  route('get.user.favourite.delete', $item->id) }}">Huỷ bỏ</a>
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                    <div style="display: block;">
                        {!! $products->appends($query ?? [])->links() !!}
                    </div>
                </table>
           </div>
        </div>
    </section>
@stop
