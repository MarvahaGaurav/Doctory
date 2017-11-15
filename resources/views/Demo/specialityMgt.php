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
                        <h3 class="text-themecolor">Speciality Management</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active">Speciality Mgt.</li>
                        </ol>
                    </div>
                </div>
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-outline-info">
                            <div class="card-header">
                                <h4 class="m-b-0 text-white">Add new Speciality</h4>
                            </div>
                            <div class="card-body">
                                <form action="#">
                                    <div class="form-body">
                                       <div class="row">
                                            <div class="col-md-4 ">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 ">
                                                <div class="form-group">
                                                    <label>Image</label>
                                                    <input type="file" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 ">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Add</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
              <div class="row">
               <div class="col-md-12">
                <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Image</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Aiger Nixon</td>
                                                <td><img src="assets/images/big/img1.jpg" alt="Doc img" class="img-responsive radius" style="width: 100px;"></td>
                                                <td>Heart</td>
                                                <td><a href="#" class="btn btn-danger btn-sm"> <i class="fa fa-edit"></i></a>
                                                  <a href="#" class="btn btn-danger btn-sm"> <i class="fa fa-bank"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tiger Nixon</td>
                                                <td><img src="assets/images/big/img1.jpg" alt="Doc img" class="img-responsive radius" style="width: 100px;"></td>
                                                <td>Heart</td>
                                                <td><a href="#" class="btn btn-danger btn-sm"> <i class="fa fa-edit"></i></a>
                                                  <a href="#" class="btn btn-danger btn-sm"> <i class="fa fa-bank"></i></a>
                                                </td>
                                            </tr><tr>
                                                <td>Tiger Nixon</td>
                                                <td><img src="assets/images/big/img1.jpg" alt="Doc img" class="img-responsive radius" style="width: 100px;"></td>
                                                <td>Heart</td>
                                                <td><a href="#" class="btn btn-danger btn-sm"> <i class="fa fa-edit"></i></a>
                                                  <a href="#" class="btn btn-danger btn-sm"> <i class="fa fa-bank"></i></a>
                                                </td>
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