<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Trang quản trị hệ thống</title>
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('admin/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('admin/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
            folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('admin/dist/css/skins/_all-skins.min.css') }}">
    <!-- Pace style -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/pace/pace.min.css') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <link rel="icon" sizes="32x32" type="image/png" href="{{ asset('ico.png') }}" />
    <link rel="stylesheet" href="{{ asset('admin/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

        <script src="{!! asset('admin/ckeditor/ckeditor.js') !!}"></script>
        <script src="{!! asset('admin/ckfinder/ckfinder.js') !!}"></script>
        <script src="{!! asset('admin/dist/js/func_ckfinder.js') !!}"></script>
        <script>
            var baseURL = "{!! url('/')!!}"
        </script>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <header class="main-header">
            <!-- Logo -->
            <a href="/" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>N</b>.Ân</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>{{ get_data_user('admins', 'name') }}</b></span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{ pare_url_file(get_data_user('admins', 'avatar')) }}" class="user-image"
                                    alt="User Image">
                                <span class="hidden-xs">{{ get_data_user('admins', 'name') }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img src="{{ pare_url_file(get_data_user('admins', 'avatar')) }}" class="img-circle"
                                        alt="User Image">
                                    <p>
                                        {{ get_data_user('admins', 'name') }}
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="{{ route('admin.profile.index') }}"
                                            class="btn btn-default btn-flat">Profile</a>
                                    </div>

                                    <div class="pull-right">
                                        <a href="{{ route('get.logout.admin') }}" class="btn btn-default btn-flat">Đăng
                                            xuất</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                        {{--                            <li> --}}
                        {{--                                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a> --}}
                        {{--                            </li> --}}
                    </ul>
                </div>
            </nav>
        </header>
        <!-- =============================================== -->
        <!-- Left side column. contains the sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="{{ pare_url_file(get_data_user('admins', 'avatar')) }}" class="img-circle"
                            alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>{{ get_data_user('admins', 'name') }}</p>
                        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu tree" data-widget="tree">
                    <li class="header">MAIN NAVIGATION</li>


                    @foreach (config('sidebar') as $item)
                        @if (isset($item['label']))
                            <li class="header">{{ $item['name'] }}</li>
                            @continue;
                        @endif
                        @php
                            $level = get_data_user('admins', 'level');
                            //dump($level);
                        @endphp
                        @if (in_array($level, $item['level']))
                            <li
                                class="{{ isset($item['sub']) ? 'treeview' : '' }}
									{{ in_array(Request::segment(2), $item['list-check']) ? ' active menu-open' : '' }}">
                                <a href="{{ isset($item['sub']) ? '#' : route($item['route']) }}">
                                    <i class="fa {{ $item['icon'] }}"></i>
                                    <span>{{ $item['name'] }}</span>
                                    @if (isset($item['sub']))
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    @endif
                                </a>
                                @if (isset($item['sub']))
                                    <ul class="treeview-menu">
                                        @foreach ($item['sub'] as $li)
                                            @if (in_array($level, $li['level']))
                                                <li
                                                    class="{{ Request::segment(2) == $li['namespace'] ? 'active' : '' }}">
                                                    <a href="{{ route($li['route']) }}">
                                                        <i class="fa {{ $li['icon'] }}"></i>
                                                        <span>{{ $li['name'] }}</span>
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endif
                    @endforeach

                    {{--                        @if (get_data_user('admins', 'level') == 1) --}}
                    {{--                            <li><a href="{{  route('admin.account_admin.index') }}"><i class="fa fa-circle-o text-red"></i> <span>Ql Admin</span></a></li> --}}
                    {{--                            <li><a href="{{  route('admin.slide.index') }}"><i class="fa fa-circle-o text-red"></i> <span>QL Slide</span></a></li> --}}
                    {{--                            <li><a href="{{  route('admin.event.index') }}"><i class="fa fa-circle-o text-red"></i> <span>Ql Event</span></a></li> --}}
                    {{--                            <li><a href="{{  route('admin.static.index') }}"><i class="fa fa-circle-o text-red"></i> <span>QL Static</span></a></li> --}}
                    {{--                            <li><a href="{{  route('admin.statistical') }}"><i class="fa fa-circle-o text-red"></i> <span>Thống kê</span></a></li> --}}
                    {{--                        @endif --}}
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>
        <!-- =============================================== -->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">

            <strong>Web Do an</strong>
        </footer>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
    <!-- jQuery 3 -->
    <script src="{{ asset('admin/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- PACE -->
    <script src="{{ asset('admin/bower_components/PACE/pace.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('admin/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('admin/bower_components/fastclick/lib/fastclick.js') }}"></script>
    <script src="{{ asset('admin/bower_components/select2/dist/js/select2.min.js') }}"></script>

    <!-- AdminLTE App -->
    <script src="{{ asset('admin/dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('admin/dist/js/demo.js') }}"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    @yield('script')
    <!-- page script -->
    <script type="text/javascript">
        // To make Pace works on Ajax calls
        $(document).ajaxStart(function() {
            Pace.restart()
        })
        $('.ajax').click(function() {
            $.ajax({
                url: '#',
                success: function(result) {
                    $('.ajax-content').html('<hr>Ajax Request Completed !')
                }
            })
        })
        $(function() {
            // run select2
            if ($(".js-select2-keyword").length > 0) {
                $(".js-select2-keyword").select2({
                    placeholder: 'Chọn keyword',
                    maximumSelectionLength: 3
                });
            }
            let $elementSelect = $(".js-select2");
            if ($elementSelect.length > 0) {
                $elementSelect.select2({
                    placeholder: 'Chọn dữ liệu',
                    maximumSelectionLength: 2
                });
            }

            // preview  hình ảnh
            $(".js-upload").change(function() {
                let $this = $(this);
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $this.parents('.block-images').find('img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            $(".js-preview-transaction").click(function(event) {
                event.preventDefault();
                let $this = $(this);
                let URL = $this.attr('href');
                let ID = $this.attr('data-id');
                $("#idTransaction").html("#" + ID);
                $.ajax({
                    url: URL
                }).done(function(results) {
                    $("#modal-preview-transaction .content").html(results.html)
                    $("#modal-preview-transaction").modal({
                        show: true
                    })
                });
            });

            $('body').on("click", '.js-delete-order-item', function(event) {
                event.preventDefault();
                let URL = $(this).attr('href');
                let $this = $(this);
                $.ajax({
                    url: URL
                }).done(function(results) {
                    if (results.code == 200) {
                        $this.parents("tr").remove();
                    }
                });
            })

            $(".js-delete-confirm").click(function(event) {
                event.preventDefault();
                let URL = $(this).attr('href');
                $.confirm({
                    title: 'Bạn có muốn xoá dữ liệu không?',
                    content: 'Dữ liệu xoá đi không thể khôi phục',
                    type: 'red',
                    buttons: {
                        ok: {
                            text: "ok!",
                            btnClass: 'btn-primary',
                            keys: ['enter'],
                            action: function() {
                                window.location.href = URL;
                            }
                        },
                        cancel: function() {

                        }
                    }
                });
            })
        })
    </script>
</body>

</html>
