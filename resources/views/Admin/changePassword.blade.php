 @include('Admin/header')
<!-- Page wrapper  -->
<!-- ============================================================== -->
<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="row page-titles">
            <div class="col-md-12 col-12 align-self-center">
                <h3 class="text-themecolor">Edit admin password</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="profile.php">Admin profile</a></li>
                    <li class="breadcrumb-item active">Change password</li>
                </ol>
            </div>
        </div>
        <!-- Row -->
        <div class="row">
            <div class="col-lg-7 col-md-8 col-sm-10">
                <span style="color:red">{{Session::get('invalid_old_password')}}</span>
                <span style="color:green">{{Session::get('password_updated')}}</span>
                <span style="color:red">{{$errors->first()}}</span>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Enter your new password</h4>
                        <form class="form p-t-20" action="{{url('Admin/change_password')}}" method="POST">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>Old password</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-lock"></i></div>
                                    <input type="password" class="form-control" id="" required name="old_password" placeholder="Enter old password" value="{{old('old_password')}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-lock"></i></div>
                                    <input type="password" class="form-control" id="" name="new_password" required placeholder="Enter new password" value="{{old('new_password')}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-lock"></i></div>
                                    <input type="password" class="form-control" id="" name="confirm_password" required placeholder="Enter confirm password" value="{{old('confirm_password')}}">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Submit</button>
                            <button type="submit" class="btn btn-inverse waves-effect waves-light">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
    </div>
    @include('Admin/footer')