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
                        <h3 class="text-themecolor">Rank of Doctor</h3>
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
                                    <table id="myTable" class="table table-bordered no-wrap table-striped">
                                        <thead>
                                            <tr>
                                                <th>Patient name</th>
                                                <th>Review</th>
                                                <th>Rank</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($Review_List as $List)
                                                <tr>
                                                    <td>{{$List->patient_detail->name}}</td>
                                                    <td>{{$List->review_text}}</td>
                                                    <td>
                                                        <div class="rating"> 
                                                           @for($i = 0;$i < $List->rating; $i++ )
                                                            <i class="fa fa-star"></i>
                                                           @endfor
                                                        </div>
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