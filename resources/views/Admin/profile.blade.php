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
                <h3 class="text-themecolor">Admin profile</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('Admin/profile')}}">Home</a></li>
                    <li class="breadcrumb-item active">Admin profile</li>
                </ol>
            </div>
        </div>
        <!-- Row -->
        <div class="row">
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-info"><i class="ti-wallet"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-light">3249</h3>
                                <h5 class="text-muted m-b-0">Total Patient</h5></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-warning"><i class="mdi mdi-cellphone-link"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">2376</h3>
                                <h5 class="text-muted m-b-0">Total Doctor</h5></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-primary"><i class="mdi mdi-cart-outline"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">20</h3>
                                <h5 class="text-muted m-b-0">Today register</h5></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-danger"><i class="mdi mdi-bullseye"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">687</h3>
                                <h5 class="text-muted m-b-0">Ad. Expense</h5></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
        </div>
        <!-- Row -->

        <div class="row">
            <div class="col-md-6">
                <!-- Column -->
                <div class="card"> <img class="" src="{{asset('Admin/assets/images/background/profile-bg3.jpg')}}" alt="Card image cap">
                    <div class="card-body little-profile text-center">
                        <div class="pro-img"><img src="{{asset('Admin/images')}}/{{$AdminDetail->profile_image}}" alt="user" /></div>
                        <h3 class="m-b-0">{{$AdminDetail->name}}</h3>
                        <div class="row text-center m-t-20">
                            <div class="col-lg-6 col-md-6 m-t-20">
                                <h3 class="m-b-0 font-light">{{$AdminDetail->mobile}}</h3><small>Mobile</small></div>
                            <div class="col-lg-6 col-md-6 m-t-20">
                                <h3 class="m-b-0 font-light">{{$AdminDetail->location}}</h3><small>Location</small></div>
                            <div class="col-md-12 m-b-10"></div>
                        </div>
                    </div>
                </div>
                <!-- Column -->
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="row">
                        <div class="col-md-12 b-l p-l-0">
                            <ul class="product-review">
                                <li>
                                    <span class="text-muted display-5"><i class="mdi mdi-linkedin"></i></span>
                                    <div class="dl m-l-10">
                                        <h3 class="card-title">Linkedin</h3>
                                        <h6 class="card-subtitle">{{$AdminDetail->linkedin}}</h6>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 15%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </li>
                                <li>
                                    <span class="text-muted display-5"><i class="mdi mdi-email"></i></span>
                                    <div class="dl m-l-10">
                                        <h3 class="card-title">{{$AdminDetail->email}}</h3>
                                        <h6 class="card-subtitle">Email</h6>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 15%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </li>
                                <li>
                                    <span class="text-muted display-5"><i class="mdi mdi-twitter"></i></span>
                                    <div class="dl m-l-10">
                                        <h3 class="card-title">{{$AdminDetail->twitter}}</h3>
                                        <h6 class="card-subtitle">Twitter</h6>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 15%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <!-- Column -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-actions">
                    <a href="{{url('Admin/edit_profile')}}" class="btn btn-success"> Edit profile</a>
                    <a href="{{url('Admin/change_password')}}" class="btn btn-inverse">Change password</a>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
   </div>
@include('Admin/footer')