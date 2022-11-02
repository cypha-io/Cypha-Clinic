<?php 
include './config/connection.php';
include './common_service/common_functions.php';

$message = '';

if(isset($_POST['submit'])) {

  $patientId = $_POST['patient'];
  $visitDate = $_POST['visit_date'];
  $nextVisitDate = $_POST['next_visit_date'];
  $bp = $_POST['bp'];
  $weight = $_POST['weight'];
  $disease = $_POST['disease'];

  $medicineDetailIds = $_POST['medicineDetailIds'];

  $quantities = $_POST['quantities'];
  $dosages = $_POST['dosages'];

  $visitDateArr = explode("/", $visitDate);
  
  $visitDate = $visitDateArr[2].'-'.$visitDateArr[0].'-'.$visitDateArr[1];

  if($nextVisitDate != '') {
    $nextVisitDateArr = explode("/", $nextVisitDate);
    $nextVisitDate = $nextVisitDateArr[2].'-'.$nextVisitDateArr[0].'-'.$nextVisitDateArr[1];
  }


  try {

    $con->beginTransaction();

      //first to store a row in patient visit

     $queryVisit = "INSERT INTO `patient_visits`(`visit_date`, 
    `next_visit_date`, `bp`, `weight`, `disease`, `patient_id`) 
    VALUES('$visitDate', 
    nullif('$nextVisitDate', ''), 
    '$bp', '$weight', '$disease', $patientId);";
    $stmtVisit = $con->prepare($queryVisit);
    $stmtVisit->execute();

    $lastInsertId = $con->lastInsertId();//latest patient visit id

//now to store data in medication history
    $size = sizeof($medicineDetailIds);
    $curMedicineDetailId = 0;
    $curQuantity = 0;
    $curDosage = 0;

    for($i = 0; $i < $size; $i++) {
      $curMedicineDetailId = $medicineDetailIds[$i];
      $curQuantity = $quantities[$i];
      $curDosage = $dosages[$i];

      $qeuryMedicationHistory = "INSERT INTO `patient_medication_history`(
      `patient_visit_id`,
      `medicine_details_id`, `quantity`, `dosage`)
      VALUES($lastInsertId, $curMedicineDetailId, $curQuantity, $curDosage);";
      $stmtDetails = $con->prepare($qeuryMedicationHistory);
      $stmtDetails->execute();
    }

    $con->commit();

    $message = 'Patient Medication stored successfully.';

  }catch(PDOException $ex) {
    $con->rollback();

    echo $ex->getTraceAsString();
    echo $ex->getMessage();
    exit;
  }

  header("location:congratulation.php?goto_page=new_prescription.php&message=$message");
  exit;
}
$patients = getPatients($con);
$medicines = getMedicines($con);

?>
<!DOCTYPE html>
<html lang="en">
<head>
 <?php include './config/site_css_links.php' ?>

 <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
 <title>New Prescription - Clinic's Patient Management System in PHP</title>

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
              <h1>New Prescription</h1>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">

        <!-- Default box -->
        <div class="card card-outline card-primary rounded-0 shadow">
          <div class="card-header">
            <h3 class="card-title">Add New Prescription</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <!-- best practices-->
            <form method="post">
              <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                  <label>Select Patient</label>
                  <select id="patient" name="patient" class="form-control form-control-sm rounded-0" 
                  required="required">
                  <?php echo $patients;?>
                </select>
              </div>


              <div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
                <div class="form-group">
                  <label>Visit Date</label>
                  <div class="input-group date" 
                  id="visit_date" 
                  data-target-input="nearest">
                  <input type="text" class="form-control form-control-sm rounded-0 datetimepicker-input" data-target="#visit_date" name="visit_date" required="required" data-toggle="datetimepicker" autocomplete="off"/>
                  <div class="input-group-append" 
                  data-target="#visit_date" 
                  data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
              </div>
            </div>
          </div>
          


          <div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
            <div class="form-group">
              <label>Next Visit Date</label>
              <div class="input-group date" 
              id="next_visit_date" 
              data-target-input="nearest">
              <input type="text" class="form-control form-control-sm rounded-0 datetimepicker-input" data-target="#next_visit_date" name="next_visit_date" data-toggle="datetimepicker" autocomplete="off"/>
              <div class="input-group-append" 
              data-target="#next_visit_date" 
              data-toggle="datetimepicker">
              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
          </div>
        </div>
      </div>

      <div class="clearfix">&nbsp;</div>

      <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
        <label>BP</label>
        <input id="bp" class="form-control form-control-sm rounded-0" name="bp" required="required" />
      </div>
      
      <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
        <label>Weight</label>
        <input id="weight" name="weight" class="form-control form-control-sm rounded-0" required="required" />
      </div>

      <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
        <label>Disease</label>
        <input id="disease" required="required" name="disease" class="form-control form-control-sm rounded-0" />
      </div>


    </div>

    <div class="col-md-12"><hr /></div>
    <div class="clearfix">&nbsp;</div>

    <div class="row">
     <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
      <label>Select Medicine</label>
      <select id="medicine" class="form-control form-control-sm rounded-0">
        <?php echo $medicines;?>
      </select>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
      <label>Select Packing</label>
      <select id="packing" class="form-control form-control-sm rounded-0">

      </select>
    </div>

    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
      <label>Quantity</label>
      <input id="quantity" class="form-control form-control-sm rounded-0" />
    </div>

    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
      <label>Dosage</label>
      <input id="dosage" class="form-control form-control-sm rounded-0" />
    </div>

    <div class="col-lg-1 col-md-1 col-sm-6 col-xs-12">
      <label>&nbsp;</label>
      <button id="add_to_list" type="button" class="btn btn-primary btn-sm btn-flat btn-block">
        <i class="fa fa-plus"></i>
      </button>
    </div>

  </div>

  <div class="clearfix">&nbsp;</div>
  <div class="row table-responsive">
    <table id="medication_list" class="table table-striped table-bordered">
      <colgroup>
        <col width="10%">
        <col width="50%">
        <col width="10%">
        <col width="10%">
        <col width="15%">
        <col width="5%">
      </colgroup>
      <thead class="bg-primary">
        <tr>
          <th>S.No</th>
          <th>Medicine Name</th>
          <th>Packing</th>
          <th>QTY</th>
          <th>Dosage</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody id="current_medicines_list">

      </tbody>
    </table>
  </div>

  <div class="clearfix">&nbsp;</div>
  <div class="row">
    <div class="col-md-10">&nbsp;</div>
    <div class="col-md-2">
      <button type="submit" id="submit" name="submit" 
      class="btn btn-primary btn-sm btn-flat btn-block">Save</button>
    </div>
  </div>
</form>

</div>

</div>
<!-- /.card -->

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include './config/footer.php';
$message = '';
if(isset($_GET['message'])) {
  $message = $_GET['message'];
}
?>  
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<?php include './config/site_js_links.php';
?>

<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
  var serial = 1;
  showMenuSelected("#mnu_patients", "#mi_new_prescription");

  var message = '<?php echo $message;?>';

  if(message !== '') {
    showCustomMessage(message);
  }


  $(document).ready(function() {
    
    $('#medication_list').find('td').addClass("px-2 py-1 align-middle")
    $('#medication_list').find('th').addClass("p-1 align-middle")
    $('#visit_date, #next_visit_date').datetimepicker({
      format: 'L'
    });


    $("#medicine").change(function() {

      // var medicineId = $("#medicine").val();
      var medicineId = $(this).val();

      if(medicineId !== '') {
        $.ajax({
          url: "ajax/get_packings.php",
          type: 'GET', 
          data: {
            'medicine_id': medicineId
          },
          cache:false,
          async:false,
          success: function (data, status, xhr) {
            $("#packing").html(data);
          },
          error: function (jqXhr, textStatus, errorMessage) {
            showCustomMessage(errorMessage);
          }
        });
      }
    });


    $("#add_to_list").click(function() {
      var medicineId = $("#medicine").val();
      var medicineName = $("#medicine option:selected").text();
      
      var medicineDetailId = $("#packing").val();
      var packing = $("#packing option:selected").text();

      var quantity = $("#quantity").val().trim();
      var dosage = $("#dosage").val().trim();

      var oldData = $("#current_medicines_list").html();

      if(medicineName !== '' && packing !== '' && quantity !== '' && dosage !== '') {
        var inputs = '';
        inputs = inputs + '<input type="hidden" name="medicineDetailIds[]" value="'+medicineDetailId+'" />';
        inputs = inputs + '<input type="hidden" name="quantities[]" value="'+quantity+'" />';
        inputs = inputs + '<input type="hidden" name="dosages[]" value="'+dosage+'" />';


        var tr = '<tr>';
        tr = tr + '<td class="px-2 py-1 align-middle">'+serial+'</td>';
        tr = tr + '<td class="px-2 py-1 align-middle">'+medicineName+'</td>';
        tr = tr + '<td class="px-2 py-1 align-middle">'+packing+'</td>';
        tr = tr + '<td class="px-2 py-1 align-middle">'+quantity+'</td>';
        tr = tr + '<td class="px-2 py-1 align-middle">'+dosage + inputs +'</td>';

        tr = tr + '<td class="px-2 py-1 align-middle text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-0" onclick="deleteCurrentRow(this);"><i class="fa fa-times"></i></button></td>';
        tr = tr + '</tr>';
        oldData = oldData + tr;
        serial++;

        $("#current_medicines_list").html(oldData);

        $("#medicine").val('');
        $("#packing").val('');
        $("#quantity").val('');
        $("#dosage").val('');

      } else {
        showCustomMessage('Please fill all fields.');
      }

    });

  });

  function deleteCurrentRow(obj) {

    var rowIndex = obj.parentNode.parentNode.rowIndex;
    
    document.getElementById("medication_list").deleteRow(rowIndex);
  }
</script>
</body>
</html>