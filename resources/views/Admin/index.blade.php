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
                    <div class="col-md-5 col-8 align-self-center">
                        <h3 class="text-themecolor">Dashboard</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php ">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                    <div class="col-md-7 col-4 align-self-center">
                        <div class="d-flex m-t-10 justify-content-end">
                            <div class="d-flex m-r-20 m-l-10 hidden-md-down">
                                <div class="chart-text m-r-10">
                                    <h6 class="m-b-0"><small>THIS MONTH</small></h6>
                                    <h4 class="m-t-0 text-info">$58,356</h4></div>
                                <div class="spark-chart">
                                    <div id="monthchart"></div>
                                </div>
                            </div>
                            <div class="d-flex m-r-20 m-l-10 hidden-md-down">
                                <div class="chart-text m-r-10">
                                    <h6 class="m-b-0"><small>LAST MONTH</small></h6>
                                    <h4 class="m-t-0 text-primary">$48,356</h4></div>
                                <div class="spark-chart">
                                    <div id="lastmonthchart"></div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- Row -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card card-body">
                            <!-- Row -->
                            <div class="row">
                                <!-- Column -->
                                <div class="col p-r-0 align-self-center">
                                    <h2 class="font-light m-b-0">324</h2>
                                    <h6 class="text-muted">New Clients</h6></div>
                                <!-- Column -->
                                <div class="col text-right align-self-center">
                                    <div data-label="20%" class="css-bar m-b-0 css-bar-info css-bar-20"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card card-body">
                            <!-- Row -->
                            <div class="row">
                                <!-- Column -->
                                <div class="col p-r-0 align-self-center">
                                    <h2 class="font-light m-b-0">2376</h2>
                                    <h6 class="text-muted">Total Visits</h6></div>
                                <!-- Column -->
                                <div class="col text-right align-self-center">
                                    <div data-label="30%" class="css-bar m-b-0 css-bar-success css-bar-20"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card card-body">
                            <!-- Row -->
                            <div class="row">
                                <!-- Column -->
                                <div class="col p-r-0 align-self-center">
                                    <h2 class="font-light m-b-0">1795</h2>
                                    <h6 class="text-muted">New Leads</h6></div>
                                <!-- Column -->
                                <div class="col text-right ">
                                    <div data-label="40%" class="css-bar m-b-0 css-bar-primary css-bar-40"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card card-body">
                            <!-- Row -->
                            <div class="row">
                                <!-- Column -->
                                <div class="col p-r-0 align-self-center">
                                    <h2 class="font-light m-b-0">36870</h2>
                                    <h6 class="text-muted">Page Views</h6></div>
                                <!-- Column -->
                                <div class="col text-right align-self-center">
                                    <div data-label="60%" class="css-bar m-b-0 css-bar-danger css-bar-60"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row -->
                <!-- Row -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Sales Overview</h4>
                                <h6 class="card-subtitle">Ample Admin Vs Pixel Admin</h6>
                                <div class="amp-pxl" style="height: 300px;"></div>
                                <div class="text-center">
                                    <ul class="list-inline">
                                        <li>
                                            <h6 class="text-muted text-success"><i class="fa fa-circle font-10 m-r-10 "></i>Ample</h6> </li>
                                        <li>
                                            <h6 class="text-muted  text-info"><i class="fa fa-circle font-10 m-r-10"></i>Pixel</h6> </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Newsletter Campaign</h4>
                                <h6 class="card-subtitle">Overview of Newsletter Campaign</h6>
                                <div class="campaign2 ct-charts" style="height: 300px;"></div>
                                <div class="text-center">
                                    <ul class="list-inline">
                                        <li>
                                            <h6 class="text-muted text-success"><i class="fa fa-circle font-10 m-r-10 "></i>Open Rate</h6> </li>
                                        <li>
                                            <h6 class="text-muted  text-info"><i class="fa fa-circle font-10 m-r-10"></i>Recurring</h6> </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Current Visitors</h4>
                                <h6 class="card-subtitle">Different Devices Used to Visit</h6>
                                <div id="usa" style="height: 300px"></div>
                                <div class="text-center">
                                    <ul class="list-inline">
                                        <li>
                                            <h6 class="text-success"><i class="fa fa-circle font-10 m-r-10 "></i>Valley</h6> </li>
                                        <li>
                                            <h6 class="text-info"><i class="fa fa-circle font-10 m-r-10"></i>Newyork</h6> </li>
                                        <li>
                                            <h6 class="text-danger"><i class="fa fa-circle font-10 m-r-10"></i>Kansas</h6> </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row -->
                <!-- Row -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <select class="custom-select pull-right">
                                    <option selected>January</option>
                                    <option value="1">February</option>
                                    <option value="2">March</option>
                                    <option value="3">April</option>
                                </select>
                                <h4 class="card-title">Projects of the Month</h4>
                                <div class="table-responsive m-t-20">
                                    <table class="table stylish-table">
                                        <thead>
                                            <tr>
                                                <th colspan="2">Assigned</th>
                                                <th>Name</th>
                                                <th>Priority</th>
                                                <th>Budget</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="width:50px;"><span class="round">S</span></td>
                                                <td>
                                                    <h6>Sunil Joshi</h6><small class="text-muted">Web Designer</small></td>
                                                <td>Elite Admin</td>
                                                <td><span class="label label-success">Low</span></td>
                                                <td>$3.9K</td>
                                            </tr>
                                            <tr class="active">
                                                <td><span class="round"><img src="assets/images/users/2.jpg" alt="user" width="50" /></span></td>
                                                <td>
                                                    <h6>Andrew</h6><small class="text-muted">Project Manager</small></td>
                                                <td>Real Homes</td>
                                                <td><span class="label label-info">Medium</span></td>
                                                <td>$23.9K</td>
                                            </tr>
                                            <tr>
                                                <td><span class="round round-success">B</span></td>
                                                <td>
                                                    <h6>Bhavesh patel</h6><small class="text-muted">Developer</small></td>
                                                <td>MedicalPro Theme</td>
                                                <td><span class="label label-primary">High</span></td>
                                                <td>$12.9K</td>
                                            </tr>
                                            <tr>
                                                <td><span class="round round-primary">N</span></td>
                                                <td>
                                                    <h6>Nirav Joshi</h6><small class="text-muted">Frontend Eng</small></td>
                                                <td>Elite Admin</td>
                                                <td><span class="label label-danger">Low</span></td>
                                                <td>$10.9K</td>
                                            </tr>
                                            <tr>
                                                <td><span class="round round-warning">M</span></td>
                                                <td>
                                                    <h6>Micheal Doe</h6><small class="text-muted">Content Writer</small></td>
                                                <td>Helping Hands</td>
                                                <td><span class="label label-warning">High</span></td>
                                                <td>$12.9K</td>
                                            </tr>
                                            <tr>
                                                <td><span class="round round-danger">N</span></td>
                                                <td>
                                                    <h6>Johnathan</h6><small class="text-muted">Graphic</small></td>
                                                <td>Digital Agency</td>
                                                <td><span class="label label-info">High</span></td>
                                                <td>$2.6K</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <!-- Column -->
                        <div class="card"> <img class="" src="assets/images/background/profile-bg.jpg" alt="Card image cap">
                            <div class="card-body little-profile text-center">
                                <div class="pro-img"><img src="assets/images/users/4.jpg" alt="user" /></div>
                                <h3 class="m-b-0">Angela Dominic</h3>
                                <p>Web Designer &amp; Developer</p>
                                <p><small>Lorem ipsum dolor sit amet, this is a consectetur adipisicing elit</small></p> <a href="javascript:void(0)" class="m-t-10 waves-effect waves-dark btn btn-primary btn-md btn-rounded">Follow</a>
                                <div class="row text-center m-t-20">
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">1099</h3><small>Articles</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">23,469</h3><small>Followers</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">6035</h3><small>Following</small></div>
                                    <div class="col-md-12 m-b-10"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Column -->
                    </div>
                </div>
                <!-- Row -->
                <div class="row">
                    <div class="col-lg-4 col-md-12">
                        <!-- card -->
                        <div class="card card-inverse card-primary">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="m-r-20 align-self-center">
                                        <h1 class="text-white"><i class="ti-pie-chart"></i></h1></div>
                                    <div>
                                        <h3 class="card-title">Bandwidth usage</h3>
                                        <h6 class="card-subtitle">March  2017</h6> </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 align-self-center">
                                        <h2 class="font-light text-white">50 GB</h2> </div>
                                    <div class="col-8 p-t-10 p-b-20 align-self-center">
                                        <div class="usage chartist-chart" style="height:60px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- card -->
                        <div class="card card-inverse card-success">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="m-r-20 align-self-center">
                                        <h1 class="text-white"><i class="icon-cloud-download"></i></h1></div>
                                    <div>
                                        <h3 class="card-title">Download count</h3>
                                        <h6 class="card-subtitle">March  2017</h6> </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 align-self-center">
                                        <h2 class="font-light text-white">35487</h2> </div>
                                    <div class="col-8 p-t-10 p-b-20 text-right">
                                        <div class="spark-count" style="height:60px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Feeds</h4>
                                <ul class="feeds">
                                    <li>
                                        <div class="bg-info"><i class="fa fa-bell-o text-white"></i></div> You have 4 pending tasks. <span class="text-muted">Just Now</span></li>
                                    <li>
                                        <div class="bg-success"><i class="ti-server text-white"></i></div> Server #1 overloaded.<span class="text-muted">2 Hours ago</span></li>
                                    <li>
                                        <div class="bg-warning"><i class="ti-shopping-cart text-white"></i></div> New order received.<span class="text-muted">31 May</span></li>
                                    <li>
                                        <div class="bg-danger"><i class="ti-user text-white"></i></div> New user registered.<span class="text-muted">30 May</span></li>
                                    <li>
                                        <div class="bg-inverse"><i class="fa fa-bell-o text-white"></i></div> New Version just arrived. <span class="text-muted">27 May</span></li>
                                    <li>
                                        <div class="bg-info"><i class="fa fa-bell-o text-white"></i></div> You have 4 pending tasks. <span class="text-muted">Just Now</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body bg-info">
                                <h4 class="text-white card-title">My Contacts</h4>
                                <h6 class="card-subtitle text-white m-b-0 op-5">Checkout my contacts here</h6> </div>
                            <div class="card-body">
                                <div class="message-box contact-box">
                                    <h2 class="add-ct-btn"><button type="button" class="btn btn-circle btn-lg btn-success waves-effect waves-dark">+</button></h2>
                                    <div class="message-widget contact-widget">
                                        <!-- Message -->
                                        <a href="#">
                                            <div class="user-img"> <img src="assets/images/users/1.jpg" alt="user" class="img-circle"> <span class="profile-status online pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Pavan kumar</h5> <span class="mail-desc">info@wrappixel.com</span></div>
                                        </a>
                                        <!-- Message -->
                                        <a href="#">
                                            <div class="user-img"> <img src="assets/images/users/2.jpg" alt="user" class="img-circle"> <span class="profile-status busy pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Sonu Nigam</h5> <span class="mail-desc">pamela1987@gmail.com</span></div>
                                        </a>
                                        <!-- Message -->
                                        <a href="#">
                                            <div class="user-img"> <span class="round">A</span> <span class="profile-status away pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Arijit Sinh</h5> <span class="mail-desc">cruise1298.fiplip@gmail.com</span></div>
                                        </a>
                                        <!-- Message -->
                                        <a href="#">
                                            <div class="user-img"> <img src="assets/images/users/4.jpg" alt="user" class="img-circle"> <span class="profile-status offline pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Pavan kumar</h5> <span class="mail-desc">kat@gmail.com</span></div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            @include('Admin/footer')