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
                        <h3 class="text-themecolor">Edit Qualification</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="addQualification.php">Add qualification</a></li>
                            <li class="breadcrumb-item active">Edit qualification</li>
                        </ol>
                    </div>
                </div>
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-md-8">
                    <span class="SpecialityErrors" style="color:red">{{$errors->first()}}</span>
                    <span class='message' style="color: red">{{Session::get('qualificationy_already_exist')}}</span>
                    
                    <span class='message' style="color: green">{{Session::get('qualificationy_updated')}}</span>
                        <div class="card card-outline-info">
                            <div class="card-header">
                                <h4 class="m-b-0 text-white">Edit Qualification</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{url('Admin/save_qualification')}}" method="POST">
                                    {{csrf_field()}}
                                    <input type="hidden" name="qa_id" value="{{Request::segment(3)}}">
                                    <div class="form-body">
                                       <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="form-group">
                                                    <label>Qualification Name</label>
                                                    <input type="text" name="qualification_name" value="{{ucfirst($Qualification->name)}}" required class="form-control">
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
                
            </div>
            
             @include('Admin/footer')