@extends('layouts.app_master_admin')
@section('content')
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>Kiểm nhập kho</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="">  Nhập kho </a></li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
				<div class="box-title">
					<form class="form-inline">
{{--						<input type="text" class="form-control" value="{{ Request::get('id') }}" name="id" placeholder="ID">--}}
{{--						<input type="text" class="form-control" value="{{ Request::get('name') }}" name="name" placeholder="Name ...">--}}
{{--						<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>--}}
{{--						<button type="submit" name="export" value="true" class="btn btn-info">--}}
{{--							<i class="fa fa-save"></i> Export--}}
{{--						</button>--}}
                        <a href="{{  route('admin.warehousing.add') }}" class="btn btn-primary">Thêm mới</a>
					</form>
				</div>
				<div class="box-body">
					<div class="col-md-12">
						<table class="table">
							<tbody>
							<tr>
                                <th>ID</th>
                                <th>Tên sản phẩm</th>
								<th>Ngày</th>
								<th>Số lượng</th>
								<th>Tổng tiền</th>
								<th>Hành động</th>
							</tr>

							</tbody>
							@if (isset($warehouses))
                                <tr>

    								@foreach($warehouses as $item)
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->pro_name }}</td>
										<td>{{ $item->created_at }}</td>
										<td>{{ $item->w_qty }}</td>
										<td>{{ number_format($item->w_price,0,',','.') }} VNĐ</td>
                                        <td>
                                            <a href="{{ route('admin.warehousing.update', $item->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                                            <a href="{{  route('admin.warehousing.delete', $item->id) }}" class="btn btn-xs btn-danger js-delete-confirm"><i class="fa fa-trash"></i> Delete</a>
                                        </td>
									</tr>
    								@endforeach
							@endif
						</table>
					</div>
				</div>
			</div>
			<!-- /.box -->
		</div>
	</section>
	<!-- /.content -->
@stop
