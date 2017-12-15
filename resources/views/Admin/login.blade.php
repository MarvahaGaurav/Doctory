<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('Admin/assets/images/favicon.png')}}">
    <title>Doctory</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{asset('Admin/assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- chartist CSS -->
    <link href="{{asset('Admin/assets/plugins/chartist-js/dist/chartist.min.css')}}" rel="stylesheet">
    <link href="{{asset('Admin/assets/plugins/chartist-js/dist/chartist-init.css')}}" rel="stylesheet">
    <link href="{{asset('Admin/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css')}}" rel="stylesheet">
    <link href="{{asset('Admin/assets/plugins/css-chart/css-chart.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('Admin/assets/plugins/html5-editor/bootstrap-wysihtml5.css')}}" />

    <!-- Vector CSS -->

    <!-- Custom CSS -->
    <link href="{{asset('Admin/css/style.css')}}" rel="stylesheet">
    <!-- You can change the theme colors from here -->
    <link href="{{asset('Admin/css/colors/blue.css')}}" id="theme" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <section id="wrapper">
        <div class="login-register" style="background-image:url('{{asset('Admin/assets/images/background/login-register.jpg')}}');">
            <div class="login-box card">
                <div class="card-body">
                    <span class='message' style="color: red">{{Session::get('invalid_credentials')}}</span>
                    <form class="form-horizontal form-material" id="loginform" action="{{url('Admin/login')}}" method='POST'>
                        {{ csrf_field() }}
                        <h3 class="box-title m-b-20">Sign In</h3>
                        @if(isset($_COOKIE['Dr_Admin_Email']))
                            <div class="form-group ">
                                <div class="col-xs-12">
                                    <input class="form-control" type="text" name="email" required="" placeholder="Email" value="{{$_COOKIE['Dr_Admin_Email']}}"> </div>
                            </div>
                        @else
                            <div class="form-group ">
                                <div class="col-xs-12">
                                    <input class="form-control" type="text" name="email" required="" placeholder="Email" value="{{old('email')}}"> </div>
                            </div>
                        @endif

                        @if(isset($_COOKIE['Dr_Admin_Password']))
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control" type="password" required="" name="password" placeholder="Password" value="{{$_COOKIE['Dr_Admin_Password']}}"> </div>
                        </div>
                        @else

                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input class="form-control" type="password" required="" name="password" placeholder="Password" value="{{old('password')}}"> 
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="checkbox checkbox-primary pull-left p-t-0">
                                    <input id="checkbox-signup" name="remember" type="checkbox"  @if(isset($_COOKIE['Dr_Admin_Remember'])) checked @endif>
                                    <label for="checkbox-signup"> Remember me </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <div class="col-xs-12">
                                <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>
                            </div>
                        </div>

                    </form>
                    <form class="form-horizontal" id="recoverform" action="">
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <h3>Recover Password</h3>
                                <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input class="form-control" type="text" required="" placeholder="Email"> </div>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <div class="col-xs-12">
                                <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>

    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{asset('Admin/assets/plugins/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{asset('Admin/assets/plugins/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('Admin/assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{asset('Admin/js/jquery.slimscroll')}}"></script>
    <!--Wave Effects -->
    <script src="{{asset('Admin/js/waves')}}"></script>
    <!--Menu sidebar -->
    <script src="{{asset('Admin/js/sidebarmenu')}}"></script>
    <!--stickey kit -->
    <script src="{{asset('Admin/assets/plugins/sticky-kit-master/dist/sticky-kit.min.js')}}"></script>
    <script src="{{asset('Admin/assets/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
    <!--stickey kit -->
    <script src="{{asset('Admin/assets/plugins/sticky-kit-master/dist/sticky-kit.min.js')}}"></script>
    <script src="{{asset('Admin/assets/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
    <script src="{{asset('Admin/assets/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
    <!--Custom JavaScript -->
    <script src="{{asset('Admin/js/custom.min.js')}}"></script>
    <script src="{{asset('Admin/assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!-- chartist chart -->
    <script src="{{asset('Admin/assets/plugins/chartist-js/dist/chartist.min.js')}}"></script>
    <script src="{{asset('Admin/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js')}}"></script>
    <script src="{{asset('Admin/js/dashboard3')}}"></script>

</body>

</html>