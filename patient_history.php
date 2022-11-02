<?php 
include './config/connection.php';
include './common_service/common_functions.php';

$patients = getPatients($con);

?>
<!DOCTYPE html>
<html lang="en">
<head>
 <?php include './config/site_css_links.php';?>
 <title>Patient History - Clinic's Patient Management System in PHP</title>

</head>
<body class="hold-transition sidebar-mini dark-mode layout-fixed layout-navbar-fixed">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->

<?php include './config/header.php';
include './config/sidebar.php';?>  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Patient History</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="card card-outline card-primary rounded-0 shadow">
        <div class="card-header">
          <h3 class="card-title">Search Patient History</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
           
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
              <select id="patient" class="form-control form-control-sm rounded-0">
                <?php echo $patients;?>
              </select>
            </div>

            <div class="col-lg-1 col-md-2 col-sm-4 col-xs-12">
              <button type="button" id="search" 
              class="btn btn-primary btn-sm btn-flat btn-block">Search</button>
            </div>
            </div>

            <div class="clearfix">&nbsp;</div>
            <div class="clearfix">&nbsp;</div>

            <div class="row">
              <div class="col-md-12 table-responsive">
                <table id="patient_history" class="table table-striped table-bordered">
                  <colgroup>
                    <col width="10%">
                    <col width="15%">
                    <col width="15%">
                    <col width="40%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                  </colgroup>
                  <thead>
                    <tr class="bg-gradient-primary text-light">
                      <th class="p-1 text-center">S.No</th>
                      <th class="p-1 text-center">Visit Date</th>
                      <th class="p-1 text-center">Disease</th>
                      <th class="p-1 text-center">Medicine</th>
                      <th class="p-1 text-center">Packing</th>
                      <th class="p-1 text-center">QTY</th>
                      <th class="p-1 text-center">Dosage</th>
                    </tr>
                  </thead>

                  <tbody id="history_data">
                    
                  </tbody>
                </table>
              </div>
            </div>
        </div>
        <!-- /.card-body -->
        
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php include './config/footer.php' ?>  
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php include './config/site_js_links.php' ?>

<script>
  showMenuSelected("#mnu_patients", "#mi_patient_history");

  $(document).ready(function() {

    $("#search").click(function() {
      var patientId = $("#patient").val();

      if(patientId !== '') {

        $.ajax({
          url: "ajax/get_patient_history.php",
          type: 'GET', 
          data: {
            'patient_id': patientId
          },
          cache:false,
          async:false,
          success: function (data, status, xhr) {
              $("#history_data").html(data);
          },
          error: function (jqXhr, textStatus, errorMessage) {
            showCustomMessage(errorMessage);
          }
        });

        //alert('hello');

      }

    });


    $("#abc").click(function() {

    });

//event driven programming

  });
</script>

</body>
</html>