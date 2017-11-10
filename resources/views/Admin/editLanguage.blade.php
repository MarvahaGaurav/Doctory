@include('Admin/header')

<div class="page-wrapper">
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 col-12 align-self-center">
            <h3 class="text-themecolor">Edit Language</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="addLanguage.php">Add Language</a></li>
                <li class="breadcrumb-item active">Edit language</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <span class="SpecialityErrors" style="color:red">{{$errors->first()}}</span>
            <span class='message' style="color: red">{{Session::get('mother_language_already_exist')}}</span>
            <span class='message' style="color: green">{{Session::get('mother_language_updated')}}</span>
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white">Edit Language</h4>
                </div>
                <div class="card-body">
                    <form action="{{url('Admin/save_mother_language')}}" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" name="lg_id" value="{{Request::segment(4)}}">
                        <div class="form-body">
                           <div class="row">
                                <div class="col-md-12 ">
                                    <div class="form-group">
                                        <label>Language Name</label>
                                        <input type="text" name="Language_name" class="form-control" required value="{{$mother_language_detail->name}}">
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