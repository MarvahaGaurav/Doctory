@include('Admin/header')
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-12 col-12 align-self-center">
                <h3 class="text-themecolor">Speciality Management</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="specialityMgt.php">Speciality Mgt.</a></li>
                    <li class="breadcrumb-item active">Edit Speciality Mgt.
                    </li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                 <span class="SpecialityErrors" style="color:red">{{$errors->first()}}</span>
                <span class='message' style="color: red">{{Session::get('speciality_already_exist')}}</span>
                <span class='message' style="color: green">{{Session::get('speciality_updated')}}</span>
                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="m-b-0 text-white">Edit Speciality</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{url('Admin/save_speciality')}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-body">
                                <input type="hidden" name="sp_id" value="{{Request::segment(4)}}" >
                                <div class="row">
                                        <div class="col-md-4 ">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" required name="speciality_name" class="form-control" value="{{$speciality_detail['name']}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4 ">
                                            <img width="75px" height="50px" src="{{url('iconImages')}}/{{$speciality_detail['icon_path']}}">
                                            <div class="form-group">
                                                <label>Image</label>
                                                <input type="file" name="iconImage" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4 ">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" name="desc" class="form-control" value="{{$speciality_detail['description']}}">
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