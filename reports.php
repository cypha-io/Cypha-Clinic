<?php 
include './config/connection.php';
include './common_service/common_functions.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
 <?php include './config/site_css_links.php' ?>

 <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
 <title>Reports - Clinic's Patient Management System in PHP</title>

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
            <h1>Reports</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="card card-outline card-primary rounded-0 shadow">
        <div class="card-header">
          <h3 class="card-title">Patient Visits Between Two Dates</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            
            <?php 
            echo getDateTextBox('From', 'patients_from');

            echo getDateTextBox('To', 'patients_to');
            ?>
          
          <div class="col-md-2">
            <label>&nbsp;</label>
            <button type="button" id="print_visits" class="btn btn-primary btn-sm btn-flat btn-block">Generate  PDF</button>
          </div>
          </div>
        </div>
        <!-- /.card-body -->
        
        <!-- /.card-footer-->
      </div>
      <!-- /.card -->




<div class="card card-outline card-primary rounded-0 shadow">
        <div class="card-header">
          <h3 class="card-title">Disease Based Report Between Two Dates</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <label>Disease</label>
              <input id="disease" class="form-control form-control-sm rounded-0" />
            </div>
            <?php 
            echo getDateTextBox('From', 'disease_from');

            echo getDateTextBox('To', 'disease_to');
            ?>
          
          <div class="col-md-2">
            <label>&nbsp;</label>
            <button type="button" id="print_diseases" class="btn btn-primary btn-sm btn-flat btn-block">Generate  PDF</button>
          </div>
          </div>
        </div>
        <!-- /.card-body -->
        
        <!-- /.card-footer-->
      </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php include './config/footer.php' ?>  
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php include './config/site_js_links.php' ?>

<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
  showMenuSelected("#mnu_reports", "#mi_reports");

  $(document).ready(function() {
    $('#patients_from, #patients_to, #disease_from, #disease_to').datetimepicker({
      format: 'L'
    });

    $("#print_visits").click(function() {
      var from = $("#patients_from").val();
      var to = $("#patients_to").val();
      
      if(from !== '' && to !== '') {
        var win = window.open("print_patients_visits.php?from=" + from 
          +"&to=" + to, "_blank");
        if(win) {
          win.focus();
        } else {
          showCustomMessage('Please allow popups.');
        }
      }
    });



$("#print_diseases").click(function() {
      var from = $("#disease_from").val();
      var to = $("#disease_to").val();
      var disease = $("#disease").val().trim();
      
      if(from !== '' && to !== '' && disease !== '') {
        var win = window.open("print_diseases.php?from=" + from 
          +"&to=" + to + "&disease=" + disease, "_blank");
        if(win) {
          win.focus();
        } else {
          showCustomMessage('Please allow popups.');
        }
      }
    });

    });

</script>
</body>
</html>