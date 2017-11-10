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
                        <h3 class="text-themecolor">Qualification Management</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">qualification Mgt.</li>
                        </ol>
                    </div>
                </div>
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-md-8">
                    <span style="color: red">{{$errors->first()}}</span>
                    <span class='message' style="color: green">{{Session::get('QA_added')}}</span>
                    <span class='message' style="color: red">{{Session::get('invalid_detail')}}</span>
                    <span class='message' style="color: red">{{Session::get('QA_already_exist')}}</span>
                    <span class='message' style="color: green">{{Session::get('QA_deleted')}}</span>
                    <span class='message' style="color: red">{{Session::get('QA_exist_under_doctor')}}</span>
                        <div class="card card-outline-info">
                            <div class="card-header">
                                <h4 class="m-b-0 text-white">Add new Qualification</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{url('Admin/add_qualification')}}" method="POST">
                                    {{csrf_field()}}
                                    <div class="form-body">
                                       <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="form-group">
                                                    <label>Qualification Name</label>
                                                    <input type="text" name="qualification_name" required class="form-control" value="{{old('qualification_name')}}">
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
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          @foreach($QualificationList as $QA)
                                            <tr>
                                                <td>{{ucfirst($QA->name)}}</td>
                                                <td><a href="{{url('Admin/qualification_edit')}}/{{$QA->id}}" class="btn btn-danger btn-sm"> <i class="fa fa-edit"></i></a>
                                                  <a onclick="return confirm('Do you want to delete?')" href="{{url('Admin/qualification_delete')}}/{{$QA->id}}" class="btn btn-danger btn-sm"> <i class="fa fa-bank"></i></a>
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