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
                        <h3 class="text-themecolor">Doctor profile</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active">doctor profile</li>
                        </ol>
                    </div>
                </div>
                <!-- Row -->
                <div class="row">
                    <div class="col-md-6">
                        <!-- Column -->
                        <div class="card"> <img class="" src="{{asset('Admin/assets/images/background/profile-bg.jpg')}}" alt="Card image cap">
                            <div class="card-body little-profile text-center">
                                <div class="pro-img">
                                @if(empty($Doctor_detail['profile_image']))
                                    <img src="{{asset('Admin/assets/images/users/4.jpg')}}" alt="user" />
                                @else
                                    <img src="{{url('userImages')}}/big{{$Doctor_detail['profile_image']}}" alt="user" />
                                @endif

                                </div>
                                <h3 class="m-b-0">{{ucfirst($Doctor_detail['name'])}}</h3>
                                <div class="row text-center m-t-20">
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">{{ucfirst($Doctor_detail['working_place'])}}</h3><small>Location</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">{{$Doctor_detail['experience']}} year</h3><small>Experience</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">{{ucfirst($Doctor_detail['speciality']['name'])}}</h3><small>Speciality</small></div>
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
                                            <span class="text-muted display-5"><i class="mdi mdi-emoticon-cool"></i></span> 
                                            <div class="dl m-l-10">
                                            @foreach($Doctor_detail['qualification'] as $QA)
                                                <span class="card-title"><b>{{ucfirst($QA['qualification_name'])}}</b></span>
                                            @endforeach
                                                <h6 class="card-subtitle">Qualification</h6> 
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 15%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="text-muted display-5"><i class="mdi mdi-emoticon-sad"></i></span> 
                                            <div class="dl m-l-10">
                                                <h3 class="card-title">{{$Doctor_detail['mobile']}}</h3>
                                                <h6 class="card-subtitle">Mobile no.</h6> 
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 15%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="text-muted display-5"><i class="mdi mdi-emoticon-neutral"></i></span>
                                            <div class="dl m-l-10">
                                                <h3 class="card-title">@if($Doctor_detail['language'] == 'en')
                                                    English
                                                @else
                                                    Arabics
                                                @endif
                                                </h3>
                                                <h6 class="card-subtitle">Language</h6> 
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
                <!-- ============================================================== -->
            </div>
            @include('Admin/footer')
            <script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
    
    </script>