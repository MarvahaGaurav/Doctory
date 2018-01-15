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
                                                <th>Status</th>
                                                <th>Time</th>
                                                <th>View chat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($final_result as $key => $value)
                                               <tr>
                                                   <td>{{$value->PatientDetail->name}}</td>
                                                   <td>{{$value->DoctorDetailForWeb->name}}</td>
                                                   <td><a href="{{url('/Admin/doctor_profile/')}}/{{$value->DoctorDetailForWeb->id}}" class="btn btn-danger btn-sm"> Profile</a></td>
                                                   <td>{{date('d-M-Y',strtotime($value->appointment_date))}}</td>
                                                   <td><b>{{$value->status_of_appointment}}</b></td>
                                                   <td>{{date('h:i a',strtotime($value->TimeSlotDetail->start_time))}}</td>
                                                   <td><a href="chatView.php" class="btn btn-danger btn-sm"> chat</a></td>
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