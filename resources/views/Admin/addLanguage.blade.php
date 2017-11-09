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
                <h3 class="text-themecolor">Language Management</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Language Mgt.</li>
                </ol>
            </div>
        </div>
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-md-8">
               <span style="color: red">{{$errors->first()}}</span>
                    <span class='message' style="color: green">{{Session::get('mother_language_added')}}</span>
                    <span class='message' style="color: green">{{Session::get('ML_deleted')}}</span>
                    <span class='message' style="color: red">{{Session::get('mother_language_already_exist')}}</span>
                    <span class='message' style="color: red">{{Session::get('invalid_detail')}}</span>
                    <span class='message' style="color: red">{{Session::get('ML_exist_under_doctor')}}</span>

                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="m-b-0 text-white">Add new Language</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{url('Admin/add_mother_language')}}" method="POST">
                           {{ csrf_field() }}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="form-group">
                                            <label>Mother Language Name</label>
                                            <input type="text" name="language_name" required class="form-control">
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
                                    @foreach($MotherLanguage as $ML)
                                    <tr>
                                        <td>{{ucfirst($ML->name)}}</td>
                                        <td>
                                            <a href="#" class="btn btn-danger btn-sm"> <i class="fa fa-edit"></i></a>
                                            <a onclick="return confirm('Do you want to delete?')" href="{{url('Admin/mother_language_delete')}}/{{$ML->id}}" class="btn btn-danger btn-sm"> <i class="fa fa-bank"></i></a>
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