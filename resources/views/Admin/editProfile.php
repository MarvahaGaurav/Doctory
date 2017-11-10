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
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Edit all information of admin</h4>
                                <form class="form p-t-20">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ti-user"></i></div>
                                            <input type="text" class="form-control" id="exampleInputuname" placeholder="Username">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Profile image</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ti-user"></i></div>
                                            <input type="file" class="form-control" id="" placeholder="Username">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Email address</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ti-email"></i></div>
                                            <input type="email" class="form-control" id="" placeholder="Enter email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ti-mobile"></i></div>
                                            <input type="text" class="form-control" id="" placeholder="Enter mobile">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Location</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ti-location-pin"></i></div>
                                            <input type="text" class="form-control" id="" placeholder="Enter location">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Linkedin</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ti-linkedin"></i></div>
                                            <input type="text" class="form-control" id="" placeholder="Enter linkedin id">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Twitter</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ti-twitter"></i></div>
                                            <input type="text" class="form-control" id="" placeholder="Enter twitter id">
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
            <?php include 'footer.php';?>
            