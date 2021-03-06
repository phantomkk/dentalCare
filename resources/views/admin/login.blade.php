<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="/assets/admin/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/admin/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/assets/admin/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/assets/admin/dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="/assets/admin/plugins/iCheck/square/blue.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="/"><b>Dental Care</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="{!! url('/loginAdmin') !!}" method="Post">
            {{ csrf_field() }}
            <div class="form-group has-feedback {{ $errors->has('phone') ? ' has-error' : '' }}">
                <input type="text" class="form-control" placeholder="Phone" name="phone" value="{{ old('phone') }}"
                       required autofocus>
                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                @if ($errors->has('phone'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                @endif
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" name="password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password'))
                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                @endif
            </div>
            @if (\Session::has('fail'))
                <span class="help-block has-error" style="color: #dd4b39">
                       <strong>{!! \Session::get('fail') !!}</strong>
                </span>
            @endif


            {{--<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">--}}
            {{--<label for="email" class="col-md-4 control-label">E-Mail Address</label>--}}

            {{--<div class="col-md-6">--}}
            {{--<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>--}}


            {{--</div>--}}
            {{--</div>--}}

            {{--<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">--}}
            {{--<label for="password" class="col-md-4 control-label">Password</label>--}}

            {{--<div class="col-md-6">--}}
            {{--<input id="password" type="password" class="form-control" name="password" required>--}}

            {{--@if ($errors->has('password'))--}}
            {{--<span class="help-block">--}}
            {{--<strong>{{ $errors->first('password') }}</strong>--}}
            {{--</span>--}}
            {{--@endif--}}
            {{--</div>--}}
            {{--</div>--}}


            <div class="row">
                <!-- /.col -->
                <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                <!-- /.col -->
            </div>
        </form>


        <!-- /.social-auth-links -->

        <a href="#">I forgot my password</a><br>
        <a href="register.html" class="text-center">Register a new membership</a>

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="/assets/admin/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/assets/admin/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/assets/admin/plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' /* optional */
        });
    });
</script>
</body>
</html>
