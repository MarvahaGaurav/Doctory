    <?php include 'header.php';?>
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
                        <div class="card"> <img class="" src="assets/images/background/profile-bg.jpg" alt="Card image cap">
                            <div class="card-body little-profile text-center">
                                <div class="pro-img"><img src="assets/images/users/4.jpg" alt="user" /></div>
                                <h3 class="m-b-0">Doctor Name</h3>
                                <div class="row text-center m-t-20">
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">MBBS</h3><small>Qualification</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">2 year</h3><small>Experience</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">Heart</h3><small>Speciality</small></div>
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
                                                <h3 class="card-title">Delhi</h3>
                                                <h6 class="card-subtitle">Location</h6> 
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 15%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="text-muted display-5"><i class="mdi mdi-emoticon-sad"></i></span> 
                                            <div class="dl m-l-10">
                                                <h3 class="card-title">0987756554</h3>
                                                <h6 class="card-subtitle">Mobile no.</h6> 
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 15%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="text-muted display-5"><i class="mdi mdi-emoticon-neutral"></i></span>
                                            <div class="dl m-l-10">
                                                <h3 class="card-title">English</h3>
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
            <?php include 'footer.php';?>
            <script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
    
    </script>