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
                        <h3 class="text-themecolor">Pending List of Doctors</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active">Doctor list</li>
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
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>View profile</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          @foreach($Pending_doctor_list as $PDL)
                                             <tr>
                                                <td>{{$PDL->name}}</td>
                                                <td>{{$PDL->email}}</td>
                                                <td>{{$PDL->mobile}}</td>
                                                <td><a href="docProfile.php" class="btn btn-danger btn-sm"> Profile</a></td>
                                                <td><label class="label label-rounded label-success">Pending</label></td>
                                                <td>
                                                   @if($PDL->status == 0)
                                                      <a onclick="return confirm('Do you want to approve?')" href="{{url('Admin/approve_doctor')}}/{{$PDL->id}}" class="btn btn-success">Approve</a>
                                                   @endif
                                                </td>
                                             </tr>
                                          @endforeach
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
            @include('Admin/footer')
             
            <script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
    
    </script>