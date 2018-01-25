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
                        <h3 class="text-themecolor">List of Patient</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('Admin/dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">patient list</li>
                        </ol>
                    </div>
                </div>
                <!-- ============================================================== -->
              <div class="row">
               <div class="col-md-12">
                  <span class='message' style="color: green">{{Session::get('patient_unblocked')}}</span>
                  <span class='message' style="color: green">{{Session::get('patient_blocked')}}</span>
                <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile No.</th>
                                                <th>Appointment</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          @foreach($patientList as $PL)  
                                            <tr>
                                                <td>{{$PL->name}}</td>
                                                <td>{{$PL->email}}</td>
                                                <td>{{$PL->mobile}}</td>
                                                <td><a href="{{url('Admin/appointment_list/patient')}}/{{$PL->id}}" class="btn btn-danger btn-sm"> See list</a></td>
                                                <td>
                                                   @if($PL->status == 1)
                                                   <a href="{{url('Admin/block_patient')}}/{{$PL->id}}/0" class="btn btn-danger">Block</a>
                                                   @endif
                                                   @if($PL->status == 0)
                                                      <a href="{{url('Admin/block_patient')}}/{{$PL->id}}/1" class="btn btn-success">Un Block</a>
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