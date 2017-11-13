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
                <h3 class="text-themecolor">Edit admin profile</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="profile.php">Admin profile</a></li>
                    <li class="breadcrumb-item active">Edit profile</li>
                </ol>
            </div>
        </div>
        <!-- Row -->
        <div class="row">
            <div class="col-lg-7 col-md-8 col-sm-10">
                <span style="color:green">{{Session::get('Admin_profile_updated')}}</span>
                 <span style="color:red">{{$errors->first()}}</span>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit all information of admin</h4>
                        <form class="form p-t-20" action="{{url('Admin/edit_profile')}}" method="POST" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>Name</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-user"></i></div>
                                    <input type="text" class="form-control" id="exampleInputuname" required name="name" placeholder="Username" value="{{$AdminDetail->name}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Profile image</label>
                                <img src="{{asset('Admin/images')}}/{{$AdminDetail->profile_image}}" width='100px' height="60px">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-user"></i></div>
                                    <input type="file" class="form-control" id="" placeholder="Username" name="profile_image" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email address</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-email"></i></div>
                                    <input type="email" class="form-control" name="email" id="" required placeholder="Enter email" value="{{$AdminDetail->email}}" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Mobile</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-mobile"></i></div>
                                    <input type="text" class="form-control" name="mobile" id="" required placeholder="Enter mobile" value="{{$AdminDetail->mobile}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-location-pin"></i></div>
                                    <input type="text" required name="location" class="form-control" id="" placeholder="Enter location" value="{{$AdminDetail->location}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Linkedin</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-linkedin"></i></div>
                                    <input type="text" name="linkedin" class="form-control" id="" placeholder="Enter linkedin id" value="{{$AdminDetail->linkedin}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Twitter</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-twitter"></i></div>
                                    <input type="text" name="twitter" class="form-control" id="" placeholder="Enter twitter id" value="{{$AdminDetail->twitter}}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
  </div>
@include('Admin/footer')