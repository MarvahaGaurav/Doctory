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
                        <h3 class="text-themecolor">Approved List of Doctors</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('Admin/dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Doctor list</li>
                        </ol>
                    </div>
                </div>
                <!-- ============================================================== -->
              <div class="row">
               <div class="col-md-12">
               <span style="color:green">{{Session::get('docotr_approved')}}</span>
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
                                                <th>Appointment</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          @foreach($Approved_doctor_list as $ADL)
                                            <tr>
                                                <td>{{$ADL->name}}</td>
                                                <td>{{$ADL->email}}</td>
                                                <td>{{$ADL->country_code}}-{{$ADL->mobile}}</td>
                                                <td><a href="{{url('Admin/doctor_profile')}}/{{$ADL->id}}" class="btn btn-danger btn-sm"> Profile</a></td>
                                                <td><a href="{{url('Admin/appointment_list')}}/{{$ADL->id}}" class="btn btn-danger btn-sm"> See list</a></td>
                                                <td><label class="label label-rounded label-success">Approved</label></td>
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