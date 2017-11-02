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
                        <h3 class="text-themecolor">List of Appointment</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active">appointment list</li>
                        </ol>
                    </div>
                </div>
                <!-- ============================================================== -->
              <div class="row">
               <div class="col-md-12">
                <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Patient Name</th>
                                                <th>Doctor name</th>
                                                <th>Doc. Profile</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>View chat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Aiger Nixon</td>
                                                <td>doctor Alex</td>
                                                <td><a href="docProfile.php" class="btn btn-danger btn-sm"> Profile</a></td>
                                                <td>24-10-2018</td>
                                                <td>19:00 AM</td>
                                                <td><a href="chatView.php" class="btn btn-danger btn-sm"> chat</a></td>
                                            </tr>
                                            <tr>
                                                <td>Aiger Nixon</td>
                                                <td>doctor Alex</td>
                                                <td><a href="docProfile.php" class="btn btn-danger btn-sm"> Profile</a></td>
                                                <td>24-10-2018</td>
                                                <td>19:00 AM</td>
                                                <td><a href="chatView.php" class="btn btn-danger btn-sm"> chat</a></td>
                                            </tr>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
               </div>
              </div>
                <!-- End Bread crumb and right sidebar toggle -->
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
            <?php include 'footer.php';?>
             
            <script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
    
    </script>