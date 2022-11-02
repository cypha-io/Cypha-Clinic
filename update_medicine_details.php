<?php 
include './config/connection.php';
include './common_service/common_functions.php';

$message = '';

if(isset($_POST['submit'])) {

  $medicineId = $_POST['medicine'];
  $medicineDetailId = $_POST['hidden_id'];
  $packing = $_POST['packing'];  

  $query = "update `medicine_details` 
  set `medicine_id` = $medicineId, 
  `packing` = '$packing' 
  where `id` = $medicineDetailId;";

  try {

    $con->beginTransaction();

    $stmtUpdate = $con->prepare($query);
    $stmtUpdate->execute();

    $con->commit();

    $message = 'medicine details updated successfully.';

  }  catch(PDOException $ex) {
    $con->rollback();

    echo $ex->getMessage();
    echo $ex->getTraceAsString();
    exit;
  }
  header("location:congratulation.php?goto_page=medicine_details.php&message=$message");
  exit;
}

$medicineId = $_GET['medicine_id'];
$medicineDetailId = $_GET['medicine_detail_id'];
$packing = $_GET['packing'];

$medicines = getMedicines($con, $medicineId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
 <?php include './config/site_css_links.php';?>
 <?php include './config/data_tables_css.php';?>
 <title>Update Medicine Details - Clinic's Patient Management System in PHP</title>

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
              <h1>Medicine Details</h1>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">

        <!-- Default box -->
        <div class="card card-outline card-primary rounded-0 shadow">
          <div class="card-header">
            <h3 class="card-title">Update Medicine Details</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
              
            </div>
          </div>
          <div class="card-body">
            <form method="post">

              <input type="hidden" name="hidden_id" 
              value="<?php echo $medicineDetailId;?>" />

              <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Select Medicine</label>
                  <select id="medicine" name="medicine" class="form-control form-control-sm rounded-0" required="required">
                    <?php echo $medicines;?>
                  </select>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Packing</label>
                  <input id="packing" name="packing" class="form-control form-control-sm rounded-0"  required="required" value="<?php echo $packing;?>" />
                </div>

                <div class="col-lg-1 col-md-2 col-sm-4 col-xs-12">
                  <label>&nbsp;</label>
                  <button type="submit" id="submit" name="submit" 
                  class="btn btn-primary btn-sm btn-flat btn-block">Update</button>
                </div>
              </div>
            </form>
          </div>
          <!-- /.card-body -->
          
        </div>
        <!-- /.card -->

      </section>



      <!-- /.content-wrapper -->
    </div>

    <?php include './config/footer.php';

    $message = '';
    if(isset($_GET['message'])) {
      $message = $_GET['message'];
    }
    ?>  
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <?php include './config/site_js_links.php'; ?>
  <?php include './config/data_tables_js.php'; ?>
  <script>
    showMenuSelected("#mnu_medicines", "#mi_medicine_details");

    var message = '<?php echo $message;?>';

    if(message !== '') {
      showCustomMessage(message);
    }


  </script>
</body>
</html>